<?php
/**
 * EasyStream Docker Startup Helper
 * This script helps start the EasyStream platform with Docker
 */

echo "🎬 EasyStream Docker Startup Helper\n";
echo "===================================\n\n";

// Check if Docker is available
$docker_check = shell_exec('docker --version 2>&1');
if (strpos($docker_check, 'Docker version') === false) {
    echo "❌ Docker is not available. Please install Docker first.\n";
    exit(1);
}

echo "✅ Docker is available: " . trim($docker_check) . "\n\n";

// Check if docker-compose.yml exists
if (!file_exists('docker-compose.yml')) {
    echo "❌ docker-compose.yml not found in current directory.\n";
    echo "Please run this script from the EasyStream root directory.\n";
    exit(1);
}

echo "✅ docker-compose.yml found\n\n";

// Display startup options
echo "🚀 EasyStream Startup Options:\n";
echo "1. Start all services (recommended for first time)\n";
echo "2. Start in background (detached mode)\n";
echo "3. View logs\n";
echo "4. Stop all services\n";
echo "5. Rebuild and start (if you made changes)\n";
echo "6. Show service status\n";
echo "7. Run database setup\n";
echo "8. Test API endpoints\n";
echo "0. Exit\n\n";

$choice = readline("Enter your choice (0-8): ");

switch ($choice) {
    case '1':
        echo "\n🔄 Starting all EasyStream services...\n";
        echo "This may take a few minutes on first run (downloading images).\n\n";
        passthru('docker-compose up');
        break;
        
    case '2':
        echo "\n🔄 Starting EasyStream in background...\n";
        passthru('docker-compose up -d');
        echo "\n✅ Services started in background.\n";
        echo "Access EasyStream at: http://localhost:8083\n";
        echo "View logs with: docker-compose logs -f\n";
        break;
        
    case '3':
        echo "\n📋 Viewing EasyStream logs (Ctrl+C to exit)...\n";
        passthru('docker-compose logs -f');
        break;
        
    case '4':
        echo "\n🛑 Stopping all EasyStream services...\n";
        passthru('docker-compose down');
        echo "\n✅ All services stopped.\n";
        break;
        
    case '5':
        echo "\n🔨 Rebuilding and starting EasyStream...\n";
        passthru('docker-compose down');
        passthru('docker-compose build --no-cache');
        passthru('docker-compose up -d');
        echo "\n✅ Services rebuilt and started.\n";
        echo "Access EasyStream at: http://localhost:8083\n";
        break;
        
    case '6':
        echo "\n📊 EasyStream Service Status:\n";
        passthru('docker-compose ps');
        break;
        
    case '7':
        echo "\n🗄️ Running database setup...\n";
        echo "Make sure services are running first!\n";
        passthru('docker-compose exec php php setup.php');
        break;
        
    case '8':
        echo "\n🧪 Testing API endpoints...\n";
        echo "Make sure services are running first!\n";
        passthru('docker-compose exec php php test_critical_fixes.php');
        break;
        
    case '0':
        echo "\n👋 Goodbye!\n";
        break;
        
    default:
        echo "\n❌ Invalid choice. Please run the script again.\n";
        break;
}

echo "\n📚 Useful Commands:\n";
echo "- Start: docker-compose up -d\n";
echo "- Stop: docker-compose down\n";
echo "- Logs: docker-compose logs -f\n";
echo "- Status: docker-compose ps\n";
echo "- Access: http://localhost:8083\n";
echo "- Admin: http://localhost:8083/admin\n\n";

echo "🎯 Quick Links:\n";
echo "- Main Site: http://localhost:8083\n";
echo "- Admin Panel: http://localhost:8083/admin\n";
echo "- Setup Page: http://localhost:8083/setup.php\n";
echo "- API Test: http://localhost:8083/test_critical_fixes.php\n\n";
?>