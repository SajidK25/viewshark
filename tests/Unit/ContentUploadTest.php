<?php

use PHPUnit\Framework\TestCase;

class ContentUploadTest extends TestCase
{
    private $storageDir;
    private $originalUploadDir;
    private $originalUploadUrl;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->storageDir = __DIR__ . '/../temp/uploads';
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
        
        global $cfg;
        $this->originalUploadDir = $cfg['upload_files_dir'] ?? null;
        $this->originalUploadUrl = $cfg['upload_files_url'] ?? null;
        $cfg['upload_files_dir'] = $this->storageDir;
        $cfg['upload_files_url'] = 'https://example.com/uploads';
        
        // Reset singleton for clean state between tests
        $reflection = new ReflectionProperty(VContent::class, 'instance');
        $reflection->setAccessible(true);
        $reflection->setValue(null);
    }
    
    protected function tearDown(): void
    {
        global $cfg;
        if ($this->originalUploadDir !== null) {
            $cfg['upload_files_dir'] = $this->originalUploadDir;
        }
        if ($this->originalUploadUrl !== null) {
            $cfg['upload_files_url'] = $this->originalUploadUrl;
        }
        
        if (is_dir($this->storageDir)) {
            $this->removeDirectory($this->storageDir);
        }
        
        $reflection = new ReflectionProperty(VContent::class, 'instance');
        $reflection->setAccessible(true);
        $reflection->setValue(null);
        
        parent::tearDown();
    }
    
    public function testHandleUploadStoresFileSuccessfully(): void
    {
        $content = VContent::getInstance();
        
        $tmpFile = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($tmpFile, str_repeat('A', 1024));
        
        $file = [
            'name'     => 'test-video.mp4',
            'tmp_name' => $tmpFile,
            'size'     => filesize($tmpFile),
            'error'    => UPLOAD_ERR_OK,
            'type'     => 'video/mp4'
        ];
        
        $result = $content->handleUpload($file, 123, [
            'type'           => 'video',
            'store_metadata' => false
        ]);
        
        $this->assertTrue($result['success'], 'Expected upload to succeed');
        $this->assertArrayHasKey('data', $result);
        
        $data = $result['data'];
        $this->assertEquals('video', $data['type']);
        $this->assertNotEmpty($data['stored_path']);
        
        $storedPath = $this->storageDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $data['stored_path']);
        $this->assertFileExists($storedPath, 'Stored file should exist on disk');
        
        $progress = $content->getUploadProgress($data['upload_id']);
        $this->assertEquals('completed', $progress['status']);
        $this->assertEquals(100, $progress['progress']);
    }
    
    public function testHandleUploadRejectsInvalidMimeType(): void
    {
        $content = VContent::getInstance();
        
        $tmpFile = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($tmpFile, "<?php echo 'malicious'; ?>");
        
        $file = [
            'name'     => 'malware.php',
            'tmp_name' => $tmpFile,
            'size'     => filesize($tmpFile),
            'error'    => UPLOAD_ERR_OK,
            'type'     => 'text/x-php'
        ];
        
        $result = $content->handleUpload($file, 321, [
            'type'           => 'document',
            'store_metadata' => false
        ]);
        
        $this->assertFalse($result['success'], 'Upload should fail for dangerous MIME types');
        $this->assertNotEmpty($result['message']);
    }
    
    public function testProgressLifecycle(): void
    {
        $content  = VContent::getInstance();
        $uploadId = 'progress_test_' . bin2hex(random_bytes(4));
        
        $content->initProgress($uploadId, 2000);
        $progress = $content->getUploadProgress($uploadId);
        $this->assertEquals('in_progress', $progress['status']);
        $this->assertEquals(0, $progress['progress']);
        
        $content->updateProgress($uploadId, 1000, 2000);
        $progress = $content->getUploadProgress($uploadId);
        $this->assertEquals(50, $progress['progress']);
        
        $content->completeProgress($uploadId);
        $progress = $content->getUploadProgress($uploadId);
        $this->assertEquals('completed', $progress['status']);
        $this->assertEquals(100, $progress['progress']);
        
        $content->cleanupProgress($uploadId);
        $progress = $content->getUploadProgress($uploadId);
        $this->assertEquals('unknown', $progress['status']);
    }
    
    private function removeDirectory($dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getRealPath());
            } else {
                @unlink($item->getRealPath());
            }
        }
        
        @rmdir($dir);
    }
}
