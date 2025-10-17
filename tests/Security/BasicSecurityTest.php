<?php

use PHPUnit\Framework\TestCase;

class BasicSecurityTest extends TestCase
{
    public function testSecurityClassExists()
    {
        $this->assertTrue(class_exists('VSecurity'));
    }
    
    public function testBasicInputValidation()
    {
        $this->assertEquals(123, VSecurity::validateInput('123', 'int'));
        $this->assertNull(VSecurity::validateInput('abc', 'int'));
    }
    
    public function testCSRFTokenGeneration()
    {
        $token = VSecurity::generateCSRFToken('test');
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token));
    }
}