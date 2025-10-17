<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

/**
 * Content upload management (validation, storage, and progress tracking)
 */
class VContent
{
    private static $instance;
    
    /** @var VDatabase */
    private $db;
    
    /** @var VLogger */
    private $logger;
    
    /** @var VSecurity */
    private $security;
    
    /** @var array */
    private $cfg;
    
    /** @var Redis|null */
    private $redis;
    
    /** @var array */
    private $tableExistsCache = [];
    
    /** @var string */
    private $progressPrefix = 'upload_progress_';
    
    /** @var string */
    private $progressDir;
    
    /** @var int */
    private $progressTtl = 3600;
    
    /** @var array */
    private $defaultAllowedTypes = [
        'video'    => ['video/mp4', 'video/webm', 'video/ogg', 'video/x-matroska', 'video/quicktime', 'application/x-mpegURL', 'video/mp2t'],
        'short'    => ['video/mp4', 'video/webm', 'video/ogg', 'video/x-matroska', 'video/quicktime'],
        'image'    => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
        'audio'    => ['audio/mpeg', 'audio/mp3', 'audio/aac', 'audio/wav', 'audio/ogg', 'audio/flac'],
        'document' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain'
        ],
        'live'     => ['video/mp2t', 'application/octet-stream', 'application/x-mpegURL'],
        'blog'     => ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
    ];
    
    /** @var array */
    private $defaultMaxSizes = [
        'video'    => 1024 * 1024 * 1024, // 1GB
        'short'    => 500 * 1024 * 1024,  // 500MB
        'image'    => 50 * 1024 * 1024,   // 50MB
        'audio'    => 300 * 1024 * 1024,  // 300MB
        'document' => 100 * 1024 * 1024,  // 100MB
        'live'     => 2048 * 1024 * 1024, // 2GB
        'blog'     => 25 * 1024 * 1024    // 25MB
    ];
    
    /**
     * Singleton accessor
     * @return VContent
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct()
    {
        global $cfg, $class_database;
        
        $this->cfg      = $cfg;
        $this->db       = $class_database ?: new VDatabase();
        $this->logger   = VLogger::getInstance();
        $this->security = VSecurity::getInstance();
        $this->redis    = $this->connectRedis();
        
        $baseUploadDir      = $this->cfg['upload_files_dir'] ?? ($this->cfg['main_dir'] . '/f_data/data_userfiles/user_uploads');
        $this->progressDir  = $baseUploadDir . '/progress-meta';
        
        $this->ensureDirectory($baseUploadDir);
        $this->ensureDirectory($this->progressDir);
        $this->configureNativeProgressTracking();
    }
    
    /**
     * Handle an uploaded file with validation and storage
     * @param array $file
     * @param int $userId
     * @param array $options
     * @return array
     */
    public function handleUpload(array $file, $userId, array $options = [])
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return ['success' => false, 'message' => 'Invalid user ID'];
        }
        
        $options = array_merge([
            'type'              => 'video',
            'allowed_mime'      => null,
            'max_size'          => null,
            'scan_for_malware'  => true,
            'storage_dir'       => null,
            'progress_id'       => null,
            'store_metadata'    => true,
            'rate_limit_key'    => null
        ], $options);
        
        $type = $this->normalizeType($options['type']);
        if (!$this->isUploadEnabled($type)) {
            return ['success' => false, 'message' => ucfirst($type) . ' uploads are currently disabled'];
        }
        
        $allowedMime = $options['allowed_mime'] ?: $this->defaultAllowedTypes[$type];
        $maxSize     = $options['max_size'] ?: $this->getMaxSizeForType($type);
        
        $rateLimitKey = $options['rate_limit_key'] ?: 'upload_user_' . $userId;
        if (!VSecurity::checkRateLimit($rateLimitKey, 10, 300, 'content_upload')) {
            return ['success' => false, 'message' => 'Upload rate limit exceeded. Please wait before uploading again.'];
        }
        
        $validation = $this->validateUpload($file, $allowedMime, $maxSize, $options['scan_for_malware']);
        if (!$validation['valid']) {
            $this->logger->warning('Upload validation failed', [
                'user_id'    => $userId,
                'type'       => $type,
                'error'      => $validation['error'] ?? 'unknown',
                'file_name'  => $file['name'] ?? null,
                'file_size'  => $file['size'] ?? null
            ]);
            return ['success' => false, 'message' => $validation['error'] ?? 'Upload validation failed'];
        }
        
        $storageDir = $options['storage_dir'] ?: $this->getStorageDirectory($type, $userId);
        $this->ensureDirectory($storageDir);
        
        $uploadId = $options['progress_id'] ?: $this->generateUploadId($userId);
        $this->initProgress($uploadId, (int) ($file['size'] ?? 0));
        
        $storedName  = $this->generateStoredFilename($file['name'] ?? 'upload', $type);
        $destination = rtrim($storageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $storedName;
        
        if (!$this->moveUploadedFile($file['tmp_name'], $destination)) {
            $this->failProgress($uploadId, 'Unable to move uploaded file');
            $this->logger->error('Failed to move uploaded file', [
                'user_id'   => $userId,
                'type'      => $type,
                'tmp_name'  => $file['tmp_name'] ?? null,
                'target'    => $destination
            ]);
            return ['success' => false, 'message' => 'Failed to store uploaded file'];
        }
        
        $hash       = hash_file('sha256', $destination);
        $fileSize   = filesize($destination);
        $mimeType   = $validation['mime_type'] ?? mime_content_type($destination);
        $relative   = $this->relativeToUploadBase($destination);
        $fileUrl    = $this->buildFileUrl($relative);
        
        $this->completeProgress($uploadId);
        
        $this->logger->info('File uploaded successfully', [
            'user_id'    => $userId,
            'type'       => $type,
            'file_name'  => $storedName,
            'file_size'  => $fileSize,
            'hash'       => $hash,
            'upload_id'  => $uploadId
        ]);
        
        if (!empty($options['store_metadata'])) {
            $this->persistUploadMetadata($userId, $type, [
                'stored_name'   => $storedName,
                'stored_path'   => $relative,
                'mime_type'     => $mimeType,
                'hash'          => $hash,
                'size'          => $fileSize,
                'original_name' => $file['name'] ?? $storedName,
                'upload_id'     => $uploadId
            ]);
        }
        
        return [
            'success' => true,
            'message' => 'File uploaded successfully',
            'data'    => [
                'upload_id'     => $uploadId,
                'type'          => $type,
                'stored_name'   => $storedName,
                'stored_path'   => $relative,
                'file_url'      => $fileUrl,
                'mime_type'     => $mimeType,
                'hash'          => $hash,
                'size'          => $fileSize
            ]
        ];
    }
    
    /**
     * Get upload progress information
     * @param string $uploadId
     * @return array
     */
    public function getUploadProgress($uploadId)
    {
        if (!$uploadId) {
            return ['status' => 'unknown', 'progress' => 0];
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionKey = $this->progressPrefix . $uploadId;
        if (isset($_SESSION[$sessionKey]) && is_array($_SESSION[$sessionKey])) {
            $info = $_SESSION[$sessionKey];
            $progress = 0;
            if (!empty($info['bytes_total'])) {
                $progress = (int) round(($info['bytes_processed'] / $info['bytes_total']) * 100);
            }
            
            return [
                'status'           => ($progress >= 100) ? 'completed' : 'in_progress',
                'progress'         => min(100, max(0, $progress)),
                'bytes_processed'  => (int) ($info['bytes_processed'] ?? 0),
                'bytes_total'      => (int) ($info['bytes_total'] ?? 0),
                'started_at'       => $info['start_time'] ?? null,
                'updated_at'       => gmdate('c')
            ];
        }
        
        // Redis cache fallback
        if ($this->redis instanceof Redis) {
            try {
                $raw = $this->redis->get($this->progressPrefix . $uploadId);
                if ($raw) {
                    $decoded = json_decode($raw, true);
                    if (is_array($decoded)) {
                        return $decoded;
                    }
                }
            } catch (Exception $e) {
                $this->logger->warning('Failed to fetch upload progress from Redis', [
                    'upload_id' => $uploadId,
                    'error'     => $e->getMessage()
                ]);
            }
        }
        
        // File-based fallback
        $fileData = $this->readProgressFile($uploadId);
        if ($fileData) {
            return $fileData;
        }
        
        return ['status' => 'unknown', 'progress' => 0];
    }
    
    /**
     * Initialise upload progress metadata
     * @param string $uploadId
     * @param int $totalBytes
     */
    public function initProgress($uploadId, $totalBytes = 0)
    {
        $data = [
            'status'          => 'in_progress',
            'progress'        => 0,
            'bytes_processed' => 0,
            'bytes_total'     => (int) $totalBytes,
            'upload_id'       => $uploadId,
            'started_at'      => gmdate('c'),
            'updated_at'      => gmdate('c')
        ];
        
        $this->writeProgress($uploadId, $data);
    }
    
    /**
     * Update progress checkpoint
     * @param string $uploadId
     * @param int $bytesProcessed
     * @param int $totalBytes
     */
    public function updateProgress($uploadId, $bytesProcessed, $totalBytes)
    {
        $totalBytes     = max(0, (int) $totalBytes);
        $bytesProcessed = max(0, (int) $bytesProcessed);
        $progress       = ($totalBytes > 0) ? (int) round(($bytesProcessed / $totalBytes) * 100) : 0;
        
        $data = [
            'status'          => ($progress >= 100) ? 'completed' : 'in_progress',
            'progress'        => min(100, $progress),
            'bytes_processed' => $bytesProcessed,
            'bytes_total'     => $totalBytes,
            'upload_id'       => $uploadId,
            'started_at'      => null,
            'updated_at'      => gmdate('c')
        ];
        
        $this->writeProgress($uploadId, $data);
    }
    
    /**
     * Mark upload as completed
     * @param string $uploadId
     */
    public function completeProgress($uploadId)
    {
        $data = [
            'status'          => 'completed',
            'progress'        => 100,
            'bytes_processed' => null,
            'bytes_total'     => null,
            'upload_id'       => $uploadId,
            'started_at'      => null,
            'updated_at'      => gmdate('c')
        ];
        
        $this->writeProgress($uploadId, $data);
    }
    
    /**
     * Mark upload as failed with message
     * @param string $uploadId
     * @param string $message
     */
    public function failProgress($uploadId, $message)
    {
        $data = [
            'status'          => 'failed',
            'progress'        => 0,
            'bytes_processed' => null,
            'bytes_total'     => null,
            'upload_id'       => $uploadId,
            'message'         => $message,
            'updated_at'      => gmdate('c')
        ];
        
        $this->writeProgress($uploadId, $data);
    }
    
    /**
     * Remove stored progress metadata
     * @param string $uploadId
     */
    public function cleanupProgress($uploadId)
    {
        $path = $this->getProgressPath($uploadId);
        if (is_file($path)) {
            @unlink($path);
        }
        
        if ($this->redis instanceof Redis) {
            try {
                $this->redis->del($this->progressPrefix . $uploadId);
            } catch (Exception $e) {
                $this->logger->warning('Failed to delete upload progress key from Redis', [
                    'upload_id' => $uploadId,
                    'error'     => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Get the list of allowed MIME types
     * @param string|null $type
     * @return array
     */
    public function getAllowedMimeTypes($type = null)
    {
        if ($type === null) {
            return $this->defaultAllowedTypes;
        }
        
        $type = $this->normalizeType($type);
        return $this->defaultAllowedTypes[$type] ?? [];
    }
    
    /**
     * Get maximum size in bytes
     * @param string|null $type
     * @return int|array
     */
    public function getMaxUploadSize($type = null)
    {
        if ($type === null) {
            $sizes = [];
            foreach (array_keys($this->defaultAllowedTypes) as $t) {
                $sizes[$t] = $this->getMaxSizeForType($t);
            }
            return $sizes;
        }
        
        return $this->getMaxSizeForType($type);
    }
    
    /**
     * Connect to Redis if available
     * @return Redis|null
     */
    private function connectRedis()
    {
        try {
            if (!class_exists('Redis')) {
                return null;
            }
            
            $redis = new Redis();
            $host  = getenv('REDIS_HOST') ?: 'redis';
            $port  = (int) (getenv('REDIS_PORT') ?: 6379);
            $db    = (int) (getenv('REDIS_DB') ?: 0);
            $timeout = (float) (getenv('REDIS_TIMEOUT') ?: 2.0);
            
            if (!$redis->connect($host, $port, $timeout)) {
                return null;
            }
            
            $password = getenv('REDIS_PASSWORD');
            if (!empty($password)) {
                $redis->auth($password);
            }
            
            if ($db > 0) {
                $redis->select($db);
            }
            
            return $redis;
        } catch (Exception $e) {
            $this->logger->warning('Redis connection failed for VContent', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * Configure PHP native upload progress tracking
     */
    private function configureNativeProgressTracking()
    {
        ini_set('session.upload_progress.enabled', '1');
        ini_set('session.upload_progress.cleanup', '1');
        ini_set('session.upload_progress.name', 'UPLOAD_IDENTIFIER');
        ini_set('session.upload_progress.prefix', $this->progressPrefix);
    }
    
    /**
     * Validate upload with fallback for testing environments
     * @param array $file
     * @param array $allowedMime
     * @param int $maxSize
     * @param bool $scanForMalware
     * @return array
     */
    private function validateUpload(array $file, array $allowedMime, $maxSize, $scanForMalware)
    {
        $validation = VSecurity::validateFileUploadAdvanced($file, $allowedMime, $maxSize, $scanForMalware);
        
        if (!$validation['valid'] && defined('TESTING') && TESTING === true) {
            // Allow CLI tests to bypass is_uploaded_file restriction
            if (($validation['error'] ?? '') === 'No file uploaded or invalid upload') {
                $validation = $this->validateUploadForTests($file, $allowedMime, $maxSize, $scanForMalware);
            }
        }
        
        return $validation;
    }
    
    /**
     * Validation helper used during automated tests
     */
    private function validateUploadForTests(array $file, array $allowedMime, $maxSize, $scanForMalware)
    {
        $tmpName = $file['tmp_name'] ?? null;
        if (!$tmpName || !is_file($tmpName)) {
            return ['valid' => false, 'error' => 'Testing upload: temporary file missing'];
        }
        
        if (($file['size'] ?? 0) > $maxSize) {
            return ['valid' => false, 'error' => 'File too large'];
        }
        
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);
        
        if (!empty($allowedMime) && !in_array($mimeType, $allowedMime)) {
            return ['valid' => false, 'error' => 'Invalid file type'];
        }
        
        if ($scanForMalware) {
            $content = file_get_contents($tmpName, false, null, 0, 8192);
            $patterns = [
                '/<\?php/i',
                '/<script/i',
                '/eval\s*\(/i'
            ];
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    return ['valid' => false, 'error' => 'File contains potentially malicious content'];
                }
            }
        }
        
        return ['valid' => true, 'mime_type' => $mimeType];
    }
    
    /**
     * Determine storage directory for uploads
     */
    private function getStorageDirectory($type, $userId)
    {
        $userId       = (int) $userId;
        $baseDir      = $this->cfg['upload_files_dir'] ?? ($this->cfg['main_dir'] . '/f_data/data_userfiles/user_uploads');
        $typeDir      = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $type;
        $userDir      = $typeDir . DIRECTORY_SEPARATOR . $userId;
        $datedDir     = $userDir . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR . date('d');
        
        return $datedDir;
    }
    
    /**
     * Generate stored filename
     */
    private function generateStoredFilename($originalName, $type)
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!$extension) {
            $guessed = $this->defaultExtensionForType($type);
            if ($guessed) {
                $extension = $guessed;
            }
        }
        
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $basename = strtolower($basename);
        $basename = preg_replace('/[^a-z0-9_\-]/', '_', $basename);
        $basename = trim($basename, '_');
        
        if (strlen($basename) < 3) {
            $basename = 'upload_' . bin2hex(random_bytes(4));
        }
        
        $timestamp = gmdate('Ymd_His');
        $random    = bin2hex(random_bytes(4));
        
        $filename = $basename . '_' . $timestamp . '_' . $random;
        if ($extension) {
            $filename .= '.' . $extension;
        }
        
        return $filename;
    }
    
    /**
     * Move uploaded file to destination
     */
    private function moveUploadedFile($tmpName, $destination)
    {
        if (!$tmpName || !$destination) {
            return false;
        }
        
        if (@move_uploaded_file($tmpName, $destination)) {
            return true;
        }
        
        // Testing fallback (CLI) or non-SAPI invocations
        if (@rename($tmpName, $destination)) {
            return true;
        }
        
        if (@copy($tmpName, $destination)) {
            @unlink($tmpName);
            return true;
        }
        
        return false;
    }
    
    /**
     * Persist upload metadata if a tracking table exists
     */
    private function persistUploadMetadata($userId, $type, array $data)
    {
        $tableName = 'db_upload_sessions';
        if (!$this->tableExists($tableName)) {
            // Log instead of failing when table is missing (legacy installations)
            $this->logger->debug('Upload metadata table missing, logging only', [
                'user_id' => $userId,
                'type'    => $type,
                'data'    => $data
            ]);
            return;
        }
        
        try {
            $sql = "
                INSERT INTO `{$tableName}` 
                (`usr_id`, `upload_type`, `stored_name`, `stored_path`, `file_hash`, `file_size`, `mime_type`, `original_name`, `upload_identifier`, `created_at`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ";
            $params = [
                $userId,
                $type,
                $data['stored_name'],
                $data['stored_path'],
                $data['hash'],
                (int) $data['size'],
                $data['mime_type'],
                $data['original_name'],
                $data['upload_id']
            ];
            
            $this->db->dbConnection()->Execute($sql, $params);
        } catch (Exception $e) {
            $this->logger->error('Failed to persist upload metadata', [
                'user_id' => $userId,
                'type'    => $type,
                'error'   => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Check if table exists in database
     */
    private function tableExists($tableName)
    {
        if (isset($this->tableExistsCache[$tableName])) {
            return $this->tableExistsCache[$tableName];
        }
        
        try {
            $sql    = "SHOW TABLES LIKE '{$tableName}'";
            $result = $this->db->dbConnection()->Execute($sql);
            $exists = ($result && !$result->EOF);
        } catch (Exception $e) {
            $exists = false;
            $this->logger->warning('Failed to determine table existence', [
                'table' => $tableName,
                'error' => $e->getMessage()
            ]);
        }
        
        $this->tableExistsCache[$tableName] = $exists;
        return $exists;
    }
    
    /**
     * Write progress data to storage layers
     */
    private function writeProgress($uploadId, array $data)
    {
        if (!isset($data['started_at'])) {
            $existing = $this->readProgressFile($uploadId);
            if ($existing && isset($existing['started_at'])) {
                $data['started_at'] = $existing['started_at'];
            }
        }
        
        $data['upload_id']  = $uploadId;
        $data['updated_at'] = gmdate('c');
        
        if ($this->redis instanceof Redis) {
            try {
                $this->redis->setex($this->progressPrefix . $uploadId, $this->progressTtl, json_encode($data));
            } catch (Exception $e) {
                $this->logger->warning('Failed to write upload progress to Redis', [
                    'upload_id' => $uploadId,
                    'error'     => $e->getMessage()
                ]);
            }
        }
        
        $path = $this->getProgressPath($uploadId);
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    }
    
    /**
     * Read progress data from file fallback
     */
    private function readProgressFile($uploadId)
    {
        $path = $this->getProgressPath($uploadId);
        if (!is_file($path)) {
            return null;
        }
        
        $raw = file_get_contents($path);
        $decoded = json_decode($raw, true);
        
        if (!is_array($decoded)) {
            return null;
        }
        
        return $decoded;
    }
    
    /**
     * Determine progress metadata path
     */
    private function getProgressPath($uploadId)
    {
        return rtrim($this->progressDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $uploadId . '.json';
    }
    
    /**
     * Generate unique upload identifier
     */
    private function generateUploadId($userId)
    {
        return $userId . '_' . bin2hex(random_bytes(8));
    }
    
    /**
     * Ensure directory exists
     */
    private function ensureDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
    
    /**
     * Build public URL for stored file
     */
    private function buildFileUrl($relativePath)
    {
        $baseUrl = $this->cfg['upload_files_url'] ?? ($this->cfg['main_url'] . '/f_data/data_userfiles/user_uploads');
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        
        return rtrim($baseUrl, '/') . '/' . $relativePath;
    }
    
    /**
     * Convert absolute path to path relative to upload base
     */
    private function relativeToUploadBase($path)
    {
        $baseDir = rtrim($this->cfg['upload_files_dir'] ?? ($this->cfg['main_dir'] . '/f_data/data_userfiles/user_uploads'), DIRECTORY_SEPARATOR);
        $normalizedBase = str_replace('\\', '/', $baseDir);
        $normalizedPath = str_replace('\\', '/', $path);
        
        if (strpos($normalizedPath, $normalizedBase) === 0) {
            return ltrim(substr($normalizedPath, strlen($normalizedBase)), '/');
        }
        
        return basename($path);
    }
    
    /**
     * Resolve default extension per type
     */
    private function defaultExtensionForType($type)
    {
        switch ($type) {
            case 'video':
            case 'short':
            case 'live':
                return 'mp4';
            case 'audio':
                return 'mp3';
            case 'image':
                return 'jpg';
            case 'document':
                return 'pdf';
            case 'blog':
                return 'jpg';
            default:
                return null;
        }
    }
    
    /**
     * Normalise upload type
     */
    private function normalizeType($type)
    {
        $type = strtolower(trim((string) $type));
        $map = [
            'videos'    => 'video',
            'video'     => 'video',
            'shorts'    => 'short',
            'short'     => 'short',
            'images'    => 'image',
            'image'     => 'image',
            'audios'    => 'audio',
            'audio'     => 'audio',
            'documents' => 'document',
            'document'  => 'document',
            'live'      => 'live',
            'blogs'     => 'blog',
            'blog'      => 'blog'
        ];
        
        return $map[$type] ?? 'video';
    }
    
    /**
     * Determine maximum size for type
     */
    private function getMaxSizeForType($type)
    {
        $type = $this->normalizeType($type);
        $configKeyMap = [
            'video'    => 'video_limit',
            'short'    => 'short_limit',
            'image'    => 'image_limit',
            'audio'    => 'audio_limit',
            'document' => 'document_limit',
            'live'     => 'live_limit',
            'blog'     => 'blog_limit'
        ];
        
        if (isset($configKeyMap[$type]) && isset($this->cfg[$configKeyMap[$type]])) {
            $megabytes = (int) $this->cfg[$configKeyMap[$type]];
            if ($megabytes > 0) {
                return $megabytes * 1024 * 1024;
            }
        }
        
        return $this->defaultMaxSizes[$type] ?? (100 * 1024 * 1024);
    }
    
    /**
     * Check whether upload type is enabled by configuration
     */
    private function isUploadEnabled($type)
    {
        $type = $this->normalizeType($type);
        $configKeyMap = [
            'video'    => 'video_uploads',
            'short'    => 'short_uploads',
            'image'    => 'image_uploads',
            'audio'    => 'audio_uploads',
            'document' => 'document_uploads',
            'live'     => 'live_uploads',
            'blog'     => 'blog_uploads'
        ];
        
        if (!isset($configKeyMap[$type])) {
            return true;
        }
        
        $key = $configKeyMap[$type];
        if (!isset($this->cfg[$key])) {
            return true;
        }
        
        return (int) $this->cfg[$key] === 1;
    }
}
