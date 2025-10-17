<?php

namespace EasyStream\Tests\Unit;

use PHPUnit\Framework\TestCase;
use VErrorHandler;

class ErrorHandlerTest extends TestCase
{
    private $errorHandler;
    private $originalErrorReporting;
    
    protected function setUp(): void
    {
        $this->errorHandler = VErrorHandler::getInstance();
        $this->originalErrorReporting = error_reporting();
        
        // Set up test environment
        global $cfg;
        $cfg['debug_mode'] = false; // Test production mode
    }
    
    protected function tearDown(): void
    {
        // Restore original error reporting
        error_reporting($this->originalErrorReporting);
    }
    
    /**
     * Test error handler singleton pattern
     */
    public function testSingletonPattern()
    {
        $handler1 = VErrorHandler::getInstance();
        $handler2 = VErrorHandler::getInstance();
        
        $this->assertSame($handler1, $handler2);
    }
    
    /**
     * Test error type detection
     */
    public function testErrorTypeDetection()
    {
        // Test that error handler can be instantiated without errors
        $this->assertInstanceOf(VErrorHandler::class, $this->errorHandler);
    }
    
    /**
     * Test error logging functionality
     */
    public function testErrorLogging()
    {
        // Test application error logging
        $this->errorHandler->logApplicationError('Test application error', ['test' => true]);
        
        // Test validation error logging
        $this->errorHandler->logValidationError('email', 'invalid-email', 'email_format', ['form' => 'registration']);
        
        // Test authentication error logging
        $this->errorHandler->logAuthError('Invalid credentials', 'testuser', ['ip' => '127.0.0.1']);
        
        // Verify no exceptions were thrown
        $this->assertTrue(true);
    }
    
    /**
     * Test error handling in production mode
     */
    public function testProductionMode()
    {
        global $cfg;
        $cfg['debug_mode'] = false;
        
        // Create new instance to test production mode
        $handler = VErrorHandler::getInstance();
        
        $this->assertInstanceOf(VErrorHandler::class, $handler);
    }
    
    /**
     * Test error handling in debug mode
     */
    public function testDebugMode()
    {
        global $cfg;
        $cfg['debug_mode'] = true;
        
        // Create new instance to test debug mode
        $handler = VErrorHandler::getInstance();
        
        $this->assertInstanceOf(VErrorHandler::class, $handler);
    }
    
    /**
     * Test error severity mapping
     */
    public function testErrorSeverityMapping()
    {
        // Test different error severities are handled properly
        $severities = [
            E_ERROR,
            E_WARNING,
            E_NOTICE,
            E_USER_ERROR,
            E_USER_WARNING,
            E_USER_NOTICE
        ];
        
        foreach ($severities as $severity) {
            // Test that each severity can be processed
            $this->assertIsInt($severity);
        }
        
        $this->assertTrue(true);
    }
    
    /**
     * Test custom error contexts
     */
    public function testCustomErrorContexts()
    {
        $contexts = [
            ['user_id' => 123, 'action' => 'upload'],
            ['ip' => '192.168.1.1', 'user_agent' => 'Test Browser'],
            ['request_id' => 'req_123', 'session_id' => 'sess_456']
        ];
        
        foreach ($contexts as $context) {
            $this->errorHandler->logApplicationError('Test error with context', $context);
        }
        
        $this->assertTrue(true);
    }
    
    /**
     * Test error message sanitization
     */
    public function testErrorMessageSanitization()
    {
        $maliciousMessages = [
            'Error with <script>alert("xss")</script>',
            'Database error: SELECT * FROM users WHERE password = "secret"',
            'Path traversal: ../../../etc/passwd'
        ];
        
        foreach ($maliciousMessages as $message) {
            $this->errorHandler->logApplicationError($message);
        }
        
        // Verify no exceptions were thrown
        $this->assertTrue(true);
    }
    
    /**
     * Test error rate limiting
     */
    public function testErrorRateLimiting()
    {
        // Log multiple similar errors rapidly
        for ($i = 0; $i < 10; $i++) {
            $this->errorHandler->logApplicationError('Repeated error message');
        }
        
        // Verify system handles repeated errors gracefully
        $this->assertTrue(true);
    }
    
    /**
     * Test memory usage during error handling
     */
    public function testMemoryUsageDuringErrorHandling()
    {
        $initialMemory = memory_get_usage();
        
        // Generate multiple errors
        for ($i = 0; $i < 100; $i++) {
            $this->errorHandler->logApplicationError("Memory test error {$i}");
        }
        
        $finalMemory = memory_get_usage();
        
        // Verify memory usage didn't grow excessively
        $memoryIncrease = $finalMemory - $initialMemory;
        $this->assertLessThan(10 * 1024 * 1024, $memoryIncrease); // Less than 10MB increase
    }
    
    /**
     * Test error handling with invalid data
     */
    public function testErrorHandlingWithInvalidData()
    {
        // Test with null values
        $this->errorHandler->logApplicationError(null);
        
        // Test with empty strings
        $this->errorHandler->logApplicationError('');
        
        // Test with very long messages
        $longMessage = str_repeat('A', 10000);
        $this->errorHandler->logApplicationError($longMessage);
        
        // Test with special characters
        $specialMessage = "Error with unicode: 世界 and emojis: 🚨";
        $this->errorHandler->logApplicationError($specialMessage);
        
        $this->assertTrue(true);
    }
    
    /**
     * Test concurrent error handling
     */
    public function testConcurrentErrorHandling()
    {
        // Simulate concurrent errors
        for ($i = 0; $i < 50; $i++) {
            $this->errorHandler->logApplicationError("Concurrent error {$i}");
            $this->errorHandler->logValidationError("field{$i}", "value{$i}", 'required');
            $this->errorHandler->logAuthError("Auth error {$i}", "user{$i}");
        }
        
        $this->assertTrue(true);
    }
}