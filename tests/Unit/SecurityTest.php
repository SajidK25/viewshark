<?php

namespace EasyStream\Tests\Unit;

use PHPUnit\Framework\TestCase;
use VSecurity;

class SecurityTest extends TestCase
{
    private $security;
    
    protected function setUp(): void
    {
        $this->security = VSecurity::getInstance();
        
        // Clear any existing session data
        if (isset($_SESSION)) {
            $_SESSION = [];
        }
        
        // Clear superglobals for clean testing
        $_GET = [];
        $_POST = [];
    }
    
    protected function tearDown(): void
    {
        // Clean up after each test
        if (isset($_SESSION)) {
            $_SESSION = [];
        }
        $_GET = [];
        $_POST = [];
    }
    
    /**
     * Test input validation with various data types
     */
    public function testInputValidationWithEdgeCases()
    {
        // Test integer validation
        $this->assertEquals(123, VSecurity::validateInput('123', 'int'));
        $this->assertEquals(0, VSecurity::validateInput('0', 'int'));
        $this->assertEquals(-123, VSecurity::validateInput('-123', 'int'));
        $this->assertNull(VSecurity::validateInput('abc', 'int'));
        $this->assertNull(VSecurity::validateInput('123.45', 'int'));
        $this->assertNull(VSecurity::validateInput('999999999999999999999', 'int'));
        
        // Test integer with min/max constraints
        $this->assertEquals(50, VSecurity::validateInput('50', 'int', null, ['min' => 10, 'max' => 100]));
        $this->assertNull(VSecurity::validateInput('5', 'int', null, ['min' => 10, 'max' => 100]));
        $this->assertNull(VSecurity::validateInput('150', 'int', null, ['min' => 10, 'max' => 100]));
        
        // Test float validation
        $this->assertEquals(123.45, VSecurity::validateInput('123.45', 'float'));
        $this->assertEquals(0.0, VSecurity::validateInput('0.0', 'float'));
        $this->assertNull(VSecurity::validateInput('abc', 'float'));
        
        // Test email validation
        $this->assertEquals('test@example.com', VSecurity::validateInput('test@example.com', 'email'));
        $this->assertEquals('user+tag@domain.co.uk', VSecurity::validateInput('user+tag@domain.co.uk', 'email'));
        $this->assertNull(VSecurity::validateInput('invalid-email', 'email'));
        $this->assertNull(VSecurity::validateInput('test@', 'email'));
        $this->assertNull(VSecurity::validateInput('@example.com', 'email'));
        
        // Test URL validation
        $this->assertEquals('https://example.com', VSecurity::validateInput('https://example.com', 'url'));
        $this->assertEquals('http://localhost:8080/path', VSecurity::validateInput('http://localhost:8080/path', 'url'));
        $this->assertNull(VSecurity::validateInput('not-a-url', 'url'));
        $this->assertNull(VSecurity::validateInput('ftp://invalid', 'url'));
        
        // Test alpha validation
        $this->assertEquals('abcDEF', VSecurity::validateInput('abcDEF', 'alpha'));
        $this->assertEquals('test', VSecurity::validateInput('test123!@#', 'alpha'));
        $this->assertNull(VSecurity::validateInput('123', 'alpha'));
        
        // Test alphanum validation
        $this->assertEquals('abc123', VSecurity::validateInput('abc123', 'alphanum'));
        $this->assertEquals('test123', VSecurity::validateInput('test123!@#', 'alphanum'));
        $this->assertNull(VSecurity::validateInput('!@#', 'alphanum'));
        
        // Test slug validation
        $this->assertEquals('test-slug_123', VSecurity::validateInput('test-slug_123', 'slug'));
        $this->assertEquals('test-slug', VSecurity::validateInput('test-slug!@#', 'slug'));
        
        // Test filename validation
        $this->assertEquals('test.txt', VSecurity::validateInput('test.txt', 'filename'));
        $this->assertEquals('file_name.pdf', VSecurity::validateInput('file_name.pdf', 'filename'));
        $this->assertEquals('test.txt', VSecurity::validateInput('test/path.txt', 'filename'));
        
        // Test boolean validation
        $this->assertTrue(VSecurity::validateInput('true', 'boolean'));
        $this->assertTrue(VSecurity::validateInput('1', 'boolean'));
        $this->assertTrue(VSecurity::validateInput('yes', 'boolean'));
        $this->assertFalse(VSecurity::validateInput('false', 'boolean'));
        $this->assertFalse(VSecurity::validateInput('0', 'boolean'));
        $this->assertFalse(VSecurity::validateInput('no', 'boolean'));
        
        // Test string validation with length constraints
        $this->assertEquals('test', VSecurity::validateInput('test', 'string', null, ['min_length' => 2, 'max_length' => 10]));
        $this->assertNull(VSecurity::validateInput('a', 'string', null, ['min_length' => 2, 'max_length' => 10]));
        $this->assertNull(VSecurity::validateInput('verylongstring', 'string', null, ['min_length' => 2, 'max_length' => 10]));
    }
    
    /**
     * Test GET parameter handling
     */
    public function testGetParameterHandling()
    {
        $_GET['test_int'] = '123';
        $_GET['test_string'] = 'hello world';
        $_GET['test_email'] = 'test@example.com';
        $_GET['test_invalid'] = 'invalid_email';
        
        $this->assertEquals(123, VSecurity::getParam('test_int', 'int'));
        $this->assertEquals('hello world', VSecurity::getParam('test_string', 'string'));
        $this->assertEquals('test@example.com', VSecurity::getParam('test_email', 'email'));
        $this->assertEquals('default', VSecurity::getParam('test_invalid', 'email', 'default'));
        $this->assertNull(VSecurity::getParam('nonexistent', 'string'));
        $this->assertEquals('default', VSecurity::getParam('nonexistent', 'string', 'default'));
    }
    
    /**
     * Test POST parameter handling
     */
    public function testPostParameterHandling()
    {
        $_POST['username'] = 'testuser';
        $_POST['age'] = '25';
        $_POST['email'] = 'user@example.com';
        $_POST['malicious'] = '<script>alert("xss")</script>';
        
        $this->assertEquals('testuser', VSecurity::postParam('username', 'string'));
        $this->assertEquals(25, VSecurity::postParam('age', 'int'));
        $this->assertEquals('user@example.com', VSecurity::postParam('email', 'email'));
        $this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', VSecurity::postParam('malicious', 'string'));
        $this->assertNull(VSecurity::postParam('nonexistent', 'string'));
    }
    
    /**
     * Test CSRF token generation and validation
     */
    public function testCSRFProtection()
    {
        // Test token generation
        $token1 = VSecurity::generateCSRFToken('test_action');
        $this->assertIsString($token1);
        $this->assertEquals(64, strlen($token1)); // 32 bytes = 64 hex chars
        
        // Test token validation
        $this->assertTrue(VSecurity::validateCSRFToken($token1, 'test_action'));
        
        // Test invalid token
        $this->assertFalse(VSecurity::validateCSRFToken('invalid_token', 'test_action'));
        
        // Test token is one-time use
        $this->assertFalse(VSecurity::validateCSRFToken($token1, 'test_action'));
        
        // Test different actions have different tokens
        $token2 = VSecurity::generateCSRFToken('different_action');
        $this->assertNotEquals($token1, $token2);
        
        // Test default action
        $defaultToken = VSecurity::generateCSRFToken();
        $this->assertTrue(VSecurity::validateCSRFToken($defaultToken, 'default'));
    }
    
    /**
     * Test CSRF field generation
     */
    public function testCSRFFieldGeneration()
    {
        $field = VSecurity::getCSRFField('test_form');
        
        $this->assertStringContainsString('<input type="hidden"', $field);
        $this->assertStringContainsString('name="csrf_token"', $field);
        $this->assertStringContainsString('value="', $field);
        
        // Extract token from field
        preg_match('/value="([^"]+)"/', $field, $matches);
        $token = $matches[1] ?? '';
        
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token));
    }
    
    /**
     * Test CSRF validation from POST data
     */
    public function testCSRFValidationFromPost()
    {
        $token = VSecurity::generateCSRFToken('form_submit');
        $_POST['csrf_token'] = $token;
        
        $this->assertTrue(VSecurity::validateCSRFFromPost('form_submit'));
        
        // Test with invalid token
        $_POST['csrf_token'] = 'invalid_token';
        $this->assertFalse(VSecurity::validateCSRFFromPost('form_submit'));
        
        // Test with missing token
        unset($_POST['csrf_token']);
        $this->assertFalse(VSecurity::validateCSRFFromPost('form_submit'));
    }
    
    /**
     * Test output escaping
     */
    public function testOutputEscaping()
    {
        $maliciousInput = '<script>alert("xss")</script>';
        $escaped = VSecurity::escapeOutput($maliciousInput);
        
        $this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $escaped);
        
        // Test with quotes and ampersands
        $input = 'Hello "world" & <friends>';
        $escaped = VSecurity::escapeOutput($input);
        $this->assertEquals('Hello &quot;world&quot; &amp; &lt;friends&gt;', $escaped);
    }
    
    /**
     * Test JavaScript escaping
     */
    public function testJavaScriptEscaping()
    {
        $input = 'Hello "world" & <script>';
        $escaped = VSecurity::escapeJS($input);
        
        $this->assertIsString($escaped);
        $this->assertStringContainsString('\u003C', $escaped); // < should be escaped
        $this->assertStringContainsString('\u0022', $escaped); // " should be escaped
    }
    
    /**
     * Test file upload validation
     */
    public function testFileUploadValidation()
    {
        // Create mock uploaded file
        $validFile = createMockUploadedFile('test.txt', 'Hello World', 'text/plain');
        
        // Test valid file
        $result = VSecurity::validateFileUpload($validFile, ['text/plain'], 1024);
        $this->assertTrue($result['valid']);
        $this->assertEquals('text/plain', $result['mime_type']);
        
        // Test file too large
        $result = VSecurity::validateFileUpload($validFile, ['text/plain'], 5);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('too large', $result['error']);
        
        // Test invalid MIME type
        $result = VSecurity::validateFileUpload($validFile, ['image/jpeg'], 1024);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Invalid file type', $result['error']);
        
        // Test invalid upload (no file)
        $invalidFile = ['tmp_name' => '', 'size' => 0];
        $result = VSecurity::validateFileUpload($invalidFile);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('No file uploaded', $result['error']);
    }
    
    /**
     * Test rate limiting functionality
     */
    public function testRateLimiting()
    {
        $key = 'test_user_' . uniqid();
        
        // Test within limits
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue(VSecurity::checkRateLimit($key, 5, 60));
        }
        
        // Test exceeding limits
        $this->assertFalse(VSecurity::checkRateLimit($key, 5, 60));
        
        // Test different key
        $key2 = 'test_user_' . uniqid();
        $this->assertTrue(VSecurity::checkRateLimit($key2, 5, 60));
    }
    
    /**
     * Test XSS prevention in various contexts
     */
    public function testXSSPrevention()
    {
        $xssPayloads = [
            '<script>alert("XSS")</script>',
            'javascript:alert("XSS")',
            '<img src="x" onerror="alert(\'XSS\')">',
            '<svg onload="alert(1)">',
            '"><script>alert("XSS")</script>',
            '\';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//--></SCRIPT>">\'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>'
        ];
        
        foreach ($xssPayloads as $payload) {
            $sanitized = VSecurity::validateInput($payload, 'string');
            
            // Should not contain dangerous elements
            $this->assertStringNotContainsString('<script>', strtolower($sanitized));
            $this->assertStringNotContainsString('javascript:', strtolower($sanitized));
            $this->assertStringNotContainsString('onerror=', strtolower($sanitized));
            $this->assertStringNotContainsString('onload=', strtolower($sanitized));
        }
    }
    
    /**
     * Test SQL injection prevention patterns
     */
    public function testSQLInjectionPrevention()
    {
        $sqlInjectionPayloads = [
            "'; DROP TABLE users; --",
            "1' OR '1'='1",
            "1; UPDATE users SET password='hacked' WHERE 1=1; --",
            "' UNION SELECT * FROM users --",
            "admin'--",
            "admin'/*",
            "' OR 1=1#"
        ];
        
        foreach ($sqlInjectionPayloads as $payload) {
            // These should be safely handled by input validation
            $result = VSecurity::validateInput($payload, 'string');
            
            // The result should be escaped and safe
            $this->assertIsString($result);
            $this->assertStringNotContainsString('DROP TABLE', strtoupper($result));
            $this->assertStringNotContainsString('UNION SELECT', strtoupper($result));
        }
    }
    
    /**
     * Test edge cases and boundary conditions
     */
    public function testEdgeCases()
    {
        // Test null input
        $this->assertEquals('default', VSecurity::validateInput(null, 'string', 'default'));
        
        // Test empty string
        $this->assertEquals('', VSecurity::validateInput('', 'string'));
        
        // Test whitespace handling
        $this->assertEquals('test', VSecurity::validateInput('  test  ', 'string'));
        
        // Test very long strings
        $longString = str_repeat('a', 10000);
        $result = VSecurity::validateInput($longString, 'string', null, ['max_length' => 100]);
        $this->assertNull($result);
        
        // Test unicode handling
        $unicode = 'Hello 世界 🌍';
        $result = VSecurity::validateInput($unicode, 'string');
        $this->assertStringContainsString('Hello', $result);
        
        // Test array input (should be handled gracefully)
        $result = VSecurity::validateInput(['array', 'input'], 'string', 'default');
        $this->assertEquals('default', $result);
    }
}