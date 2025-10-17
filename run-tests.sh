#!/bin/bash

# EasyStream Test Runner Script
# This script sets up the test environment and runs the complete test suite

set -e

echo "🚀 EasyStream Test Runner"
echo "========================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is available
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed or not in PATH"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed or not in PATH"
    exit 1
fi

# Function to cleanup
cleanup() {
    print_status "Cleaning up test environment..."
    docker-compose -f docker-compose.test.yml down -v --remove-orphans 2>/dev/null || true
}

# Set trap to cleanup on exit
trap cleanup EXIT

# Parse command line arguments
TEST_SUITE="all"
COVERAGE=false
VERBOSE=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --suite)
            TEST_SUITE="$2"
            shift 2
            ;;
        --coverage)
            COVERAGE=true
            shift
            ;;
        --verbose)
            VERBOSE=true
            shift
            ;;
        --help)
            echo "Usage: $0 [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --suite SUITE     Run specific test suite (unit|integration|security|performance|all)"
            echo "  --coverage        Generate code coverage report"
            echo "  --verbose         Enable verbose output"
            echo "  --help           Show this help message"
            echo ""
            echo "Examples:"
            echo "  $0                          # Run all tests"
            echo "  $0 --suite unit             # Run only unit tests"
            echo "  $0 --coverage               # Run tests with coverage"
            echo "  $0 --suite security --verbose  # Run security tests with verbose output"
            exit 0
            ;;
        *)
            print_error "Unknown option: $1"
            echo "Use --help for usage information"
            exit 1
            ;;
    esac
done

print_status "Starting EasyStream test suite..."
print_status "Test suite: $TEST_SUITE"
print_status "Coverage: $COVERAGE"
print_status "Verbose: $VERBOSE"

# Create necessary directories
print_status "Creating test directories..."
mkdir -p tests/temp tests/fixtures tests/coverage tests/results
mkdir -p f_data/logs/test f_data/uploads/test f_data/cache/test

# Start test environment
print_status "Starting test environment with Docker Compose..."
docker-compose -f docker-compose.test.yml up -d

# Wait for services to be ready
print_status "Waiting for services to be ready..."
sleep 10

# Check if services are healthy
print_status "Checking service health..."
if ! docker-compose -f docker-compose.test.yml ps | grep -q "healthy"; then
    print_warning "Some services may not be fully ready, waiting additional time..."
    sleep 20
fi

# Install dependencies if needed
if [ ! -d "vendor" ]; then
    print_status "Installing PHP dependencies..."
    docker-compose -f docker-compose.test.yml exec -T test-php composer install --no-progress --prefer-dist
fi

# Run the appropriate test suite
case $TEST_SUITE in
    "unit")
        print_status "Running unit tests..."
        if [ "$COVERAGE" = true ]; then
            docker-compose -f docker-compose.test.yml exec -T test-php composer run-script test-unit -- --coverage-html tests/coverage/html
        else
            docker-compose -f docker-compose.test.yml exec -T test-php composer run-script test-unit
        fi
        ;;
    "integration")
        print_status "Running integration tests..."
        docker-compose -f docker-compose.test.yml exec -T test-php composer run-script test-integration
        ;;
    "security")
        print_status "Running security tests..."
        docker-compose -f docker-compose.test.yml exec -T test-php composer run-script test-security
        ;;
    "performance")
        print_status "Running performance tests..."
        docker-compose -f docker-compose.test.yml exec -T test-php composer run-script test-performance
        ;;
    "all")
        print_status "Running complete test suite..."
        if [ "$COVERAGE" = true ]; then
            docker-compose -f docker-compose.test.yml exec -T test-php composer run-script test-coverage
        else
            docker-compose -f docker-compose.test.yml exec -T test-php composer run-script test
        fi
        ;;
    *)
        print_error "Unknown test suite: $TEST_SUITE"
        print_error "Valid options: unit, integration, security, performance, all"
        exit 1
        ;;
esac

TEST_EXIT_CODE=$?

# Copy test results from container
print_status "Copying test results..."
docker-compose -f docker-compose.test.yml exec -T test-php find tests/results -name "*.xml" -exec cp {} tests/results/ \; 2>/dev/null || true
docker-compose -f docker-compose.test.yml exec -T test-php find tests/coverage -name "*.html" -exec cp {} tests/coverage/ \; 2>/dev/null || true

# Display results
if [ $TEST_EXIT_CODE -eq 0 ]; then
    print_success "All tests passed! ✅"
    
    if [ "$COVERAGE" = true ]; then
        print_status "Coverage report generated in tests/coverage/html/"
        if command -v open &> /dev/null; then
            print_status "Opening coverage report..."
            open tests/coverage/html/index.html
        elif command -v xdg-open &> /dev/null; then
            print_status "Opening coverage report..."
            xdg-open tests/coverage/html/index.html
        fi
    fi
else
    print_error "Some tests failed! ❌"
    print_status "Check the output above for details"
fi

# Show test summary
print_status "Test Summary:"
echo "=============="
if [ -f "tests/results/junit.xml" ]; then
    # Parse JUnit XML for summary (basic parsing)
    TOTAL_TESTS=$(grep -o 'tests="[0-9]*"' tests/results/junit.xml | grep -o '[0-9]*' | head -1)
    FAILED_TESTS=$(grep -o 'failures="[0-9]*"' tests/results/junit.xml | grep -o '[0-9]*' | head -1)
    ERROR_TESTS=$(grep -o 'errors="[0-9]*"' tests/results/junit.xml | grep -o '[0-9]*' | head -1)
    
    echo "Total Tests: ${TOTAL_TESTS:-0}"
    echo "Failed Tests: ${FAILED_TESTS:-0}"
    echo "Error Tests: ${ERROR_TESTS:-0}"
    echo "Passed Tests: $((${TOTAL_TESTS:-0} - ${FAILED_TESTS:-0} - ${ERROR_TESTS:-0}))"
else
    print_warning "No JUnit results file found"
fi

print_status "Test run completed!"

exit $TEST_EXIT_CODE