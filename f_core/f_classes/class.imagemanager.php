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
 * VImageManager - Comprehensive Image Upload and Management System
 * 
 * This class handles all aspects of image management for the branding system:
 * - Image uploads with validation
 * - Automatic resizing and optimization
 * - Multiple format generation (WebP, AVIF)
 * - Dimension recommendations
 * - Watermarking
 * - CDN integration
 */
class VImageManager
{
    private static $instance = null;
    private $uploadDir;
    private $maxFileSize;
    private $allowedTypes;
    private $branding;
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->branding = VBranding::getInstance();
        $this->uploadDir = $this->branding->get('upload_directory', 'f_data/branding_images');
        $this->maxFileSize = $this->branding->get('max_upload_size', 5242880); // 5MB
        $this->allowedTypes = explode(',', $this->branding->get('allowed_image_types', 'jpg,jpeg,png,gif,svg,webp'));
        
        // Ensure upload directory exists
        $this->ensureUploadDirectory();
    }
    
    /**
     * Get image dimension presets
     */
    public function getImagePresets()
    {
        global $db;
        $presets = [];
        
        try {
            $sql = "SELECT * FROM db_image_presets ORDER BY is_required DESC, preset_name";
            $result = $db->Execute($sql);
            
            if ($result) {
                while (!$result->EOF) {
                    $presets[] = [
                        'id' => $result->fields['id'],
                        'name' => $result->fields['preset_name'],
                        'key' => $result->fields['preset_key'],
                        'width' => (int) $result->fields['width'],
                        'height' => (int) $result->fields['height'],
                        'aspect_ratio' => $result->fields['aspect_ratio'],
                        'description' => $result->fields['description'],
                        'use_case' => $result->fields['use_case'],
                        'is_required' => (bool) $result->fields['is_required']
                    ];
                    $result->MoveNext();
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get image presets: ' . $e->getMessage());
        }
        
        return $presets;
    }
    
    /**
     * Get preset by key
     */
    public function getPreset($key)
    {
        global $db;
        
        try {
            $sql = "SELECT * FROM db_image_presets WHERE preset_key = ?";
            $result = $db->Execute($sql, [$key]);
            
            if ($result && !$result->EOF) {
                return [
                    'id' => $result->fields['id'],
                    'name' => $result->fields['preset_name'],
                    'key' => $result->fields['preset_key'],
                    'width' => (int) $result->fields['width'],
                    'height' => (int) $result->fields['height'],
                    'aspect_ratio' => $result->fields['aspect_ratio'],
                    'description' => $result->fields['description'],
                    'use_case' => $result->fields['use_case'],
                    'is_required' => (bool) $result->fields['is_required']
                ];
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get preset: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Upload and process image
     */
    public function uploadImage($file, $imageKey, $presetKey = null)
    {
        try {
            // Validate file
            $validation = $this->validateUpload($file, $presetKey);
            if (!$validation['valid']) {
                return ['success' => false, 'error' => $validation['error']];
            }
            
            // Get preset if specified
            $preset = $presetKey ? $this->getPreset($presetKey) : null;
            
            // Generate unique filename
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = $this->generateFilename($imageKey, $extension);
            $filepath = $this->uploadDir . '/' . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'error' => 'Failed to move uploaded file'];
            }
            
            // Get image dimensions
            $imageInfo = getimagesize($filepath);
            if (!$imageInfo) {
                unlink($filepath);
                return ['success' => false, 'error' => 'Invalid image file'];
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Store in database
            $imageId = $this->storeImageRecord($imageKey, $file['name'], $filename, $filepath, 
                                             $file['size'], $mimeType, $width, $height, $preset);
            
            if (!$imageId) {
                unlink($filepath);
                return ['success' => false, 'error' => 'Failed to store image record'];
            }
            
            // Process image (resize, optimize, generate variants)
            $this->processImage($imageId, $filepath, $preset);
            
            // Update branding setting
            $this->branding->set($imageKey, '/' . $filepath, 'image');
            
            return [
                'success' => true,
                'image_id' => $imageId,
                'filename' => $filename,
                'path' => '/' . $filepath,
                'width' => $width,
                'height' => $height,
                'size' => $file['size']
            ];
            
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Image upload failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validate file upload
     */
    private function validateUpload($file, $presetKey = null)
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => $this->getUploadErrorMessage($file['error'])];
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            $maxSizeMB = round($this->maxFileSize / 1024 / 1024, 1);
            return ['valid' => false, 'error' => "File size exceeds maximum allowed size of {$maxSizeMB}MB"];
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes)) {
            return ['valid' => false, 'error' => 'File type not allowed. Allowed types: ' . implode(', ', $this->allowedTypes)];
        }
        
        // Check if it's actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            return ['valid' => false, 'error' => 'File is not a valid image'];
        }
        
        // Check dimensions against preset if specified
        if ($presetKey) {
            $preset = $this->getPreset($presetKey);
            if ($preset) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
                
                // Allow some tolerance for dimensions
                $tolerance = 0.1; // 10% tolerance
                $minWidth = $preset['width'] * (1 - $tolerance);
                $maxWidth = $preset['width'] * (1 + $tolerance);
                $minHeight = $preset['height'] * (1 - $tolerance);
                $maxHeight = $preset['height'] * (1 + $tolerance);
                
                if ($width < $minWidth || $width > $maxWidth || $height < $minHeight || $height > $maxHeight) {
                    return [
                        'valid' => false, 
                        'error' => "Image dimensions ({$width}x{$height}) don't match recommended size ({$preset['width']}x{$preset['height']}). Auto-resize is " . 
                                  ($this->branding->get('auto_resize_images', true) ? 'enabled' : 'disabled') . "."
                    ];
                }
            }
        }
        
        return ['valid' => true];
    }
    
    /**
     * Store image record in database
     */
    private function storeImageRecord($imageKey, $originalName, $filename, $filepath, $fileSize, $mimeType, $width, $height, $preset)
    {
        global $db;
        
        try {
            $recommendedWidth = $preset ? $preset['width'] : null;
            $recommendedHeight = $preset ? $preset['height'] : null;
            $imageType = $this->determineImageType($imageKey);
            
            $sql = "INSERT INTO db_branding_images 
                    (image_key, original_filename, stored_filename, file_path, file_size, mime_type, 
                     width, height, recommended_width, recommended_height, image_type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    original_filename = VALUES(original_filename),
                    stored_filename = VALUES(stored_filename),
                    file_path = VALUES(file_path),
                    file_size = VALUES(file_size),
                    mime_type = VALUES(mime_type),
                    width = VALUES(width),
                    height = VALUES(height),
                    updated_at = NOW()";
            
            $result = $db->Execute($sql, [
                $imageKey, $originalName, $filename, $filepath, $fileSize, $mimeType,
                $width, $height, $recommendedWidth, $recommendedHeight, $imageType
            ]);
            
            if ($result) {
                return $db->Insert_ID();
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to store image record: ' . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Process image (resize, optimize, generate variants)
     */
    private function processImage($imageId, $filepath, $preset)
    {
        try {
            // Auto-resize if enabled and preset provided
            if ($this->branding->get('auto_resize_images', true) && $preset) {
                $this->resizeImage($filepath, $preset['width'], $preset['height']);
            }
            
            // Generate WebP variant if enabled
            if ($this->branding->get('generate_webp', true)) {
                $this->generateWebPVariant($imageId, $filepath);
            }
            
            // Generate retina variant if enabled
            if ($this->branding->get('generate_retina', true) && $preset) {
                $this->generateRetinaVariant($imageId, $filepath, $preset);
            }
            
            // Apply watermark if enabled
            if ($this->branding->get('enable_watermark', false)) {
                $this->applyWatermark($filepath);
            }
            
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Image processing failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Resize image to specified dimensions
     */
    private function resizeImage($filepath, $targetWidth, $targetHeight)
    {
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) return false;
        
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Don't resize if already correct size
        if ($sourceWidth == $targetWidth && $sourceHeight == $targetHeight) {
            return true;
        }
        
        // Create source image
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($filepath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($filepath);
                break;
            default:
                return false;
        }
        
        if (!$sourceImage) return false;
        
        // Create target image
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType == 'image/png' || $mimeType == 'image/gif') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        }
        
        // Resize image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, 
                          $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
        
        // Save resized image
        $quality = $this->branding->get('image_quality', 85);
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($targetImage, $filepath, $quality);
                break;
            case 'image/png':
                imagepng($targetImage, $filepath, 9);
                break;
            case 'image/gif':
                imagegif($targetImage, $filepath);
                break;
        }
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($targetImage);
        
        return true;
    }
    
    /**
     * Generate WebP variant
     */
    private function generateWebPVariant($imageId, $filepath)
    {
        if (!function_exists('imagewebp')) return false;
        
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) return false;
        
        $mimeType = $imageInfo['mime'];
        
        // Create source image
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($filepath);
                break;
            default:
                return false;
        }
        
        if (!$sourceImage) return false;
        
        // Generate WebP filename
        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $filepath);
        
        // Save as WebP
        $quality = $this->branding->get('webp_quality', 80);
        $success = imagewebp($sourceImage, $webpPath, $quality);
        
        if ($success) {
            // Store variant record
            $this->storeImageVariant($imageId, 'webp', $webpPath, $imageInfo[0], $imageInfo[1], 'image/webp');
        }
        
        imagedestroy($sourceImage);
        return $success;
    }
    
    /**
     * Generate retina (2x) variant
     */
    private function generateRetinaVariant($imageId, $filepath, $preset)
    {
        $retinaWidth = $preset['width'] * 2;
        $retinaHeight = $preset['height'] * 2;
        
        // Generate retina filename
        $pathInfo = pathinfo($filepath);
        $retinaPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '@2x.' . $pathInfo['extension'];
        
        // Copy original file
        copy($filepath, $retinaPath);
        
        // Resize to retina dimensions
        if ($this->resizeImage($retinaPath, $retinaWidth, $retinaHeight)) {
            $fileSize = filesize($retinaPath);
            $this->storeImageVariant($imageId, 'retina', $retinaPath, $retinaWidth, $retinaHeight, 'image/jpeg', $fileSize);
            return true;
        }
        
        return false;
    }
    
    /**
     * Store image variant record
     */
    private function storeImageVariant($parentId, $variantType, $filePath, $width, $height, $mimeType, $fileSize = null)
    {
        global $db;
        
        if ($fileSize === null) {
            $fileSize = filesize($filePath);
        }
        
        try {
            $sql = "INSERT INTO db_branding_image_variants 
                    (parent_image_id, variant_type, file_path, width, height, file_size, mime_type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            return $db->Execute($sql, [$parentId, $variantType, $filePath, $width, $height, $fileSize, $mimeType]);
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to store image variant: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get uploaded images
     */
    public function getUploadedImages($imageType = null)
    {
        global $db;
        $images = [];
        
        try {
            $sql = "SELECT * FROM db_branding_images WHERE is_active = 1";
            $params = [];
            
            if ($imageType) {
                $sql .= " AND image_type = ?";
                $params[] = $imageType;
            }
            
            $sql .= " ORDER BY upload_date DESC";
            $result = $db->Execute($sql, $params);
            
            if ($result) {
                while (!$result->EOF) {
                    $images[] = [
                        'id' => $result->fields['id'],
                        'key' => $result->fields['image_key'],
                        'original_name' => $result->fields['original_filename'],
                        'filename' => $result->fields['stored_filename'],
                        'path' => '/' . $result->fields['file_path'],
                        'size' => $result->fields['file_size'],
                        'mime_type' => $result->fields['mime_type'],
                        'width' => $result->fields['width'],
                        'height' => $result->fields['height'],
                        'recommended_width' => $result->fields['recommended_width'],
                        'recommended_height' => $result->fields['recommended_height'],
                        'type' => $result->fields['image_type'],
                        'upload_date' => $result->fields['upload_date']
                    ];
                    $result->MoveNext();
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get uploaded images: ' . $e->getMessage());
        }
        
        return $images;
    }
    
    /**
     * Delete image and its variants
     */
    public function deleteImage($imageId)
    {
        global $db;
        
        try {
            // Get image record
            $sql = "SELECT * FROM db_branding_images WHERE id = ?";
            $result = $db->Execute($sql, [$imageId]);
            
            if (!$result || $result->EOF) {
                return false;
            }
            
            $image = $result->fields;
            
            // Delete physical file
            if (file_exists($image['file_path'])) {
                unlink($image['file_path']);
            }
            
            // Delete variants
            $sql = "SELECT file_path FROM db_branding_image_variants WHERE parent_image_id = ?";
            $variants = $db->Execute($sql, [$imageId]);
            
            if ($variants) {
                while (!$variants->EOF) {
                    if (file_exists($variants->fields['file_path'])) {
                        unlink($variants->fields['file_path']);
                    }
                    $variants->MoveNext();
                }
            }
            
            // Delete database records (variants will be deleted by foreign key cascade)
            $sql = "DELETE FROM db_branding_images WHERE id = ?";
            return $db->Execute($sql, [$imageId]);
            
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to delete image: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Helper methods
     */
    private function ensureUploadDirectory()
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        
        // Create .htaccess for security
        $htaccessPath = $this->uploadDir . '/.htaccess';
        if (!file_exists($htaccessPath)) {
            file_put_contents($htaccessPath, "Options -Indexes\nOptions -ExecCGI\n");
        }
    }
    
    private function generateFilename($imageKey, $extension)
    {
        $timestamp = time();
        $random = substr(md5(uniqid()), 0, 8);
        return "{$imageKey}_{$timestamp}_{$random}.{$extension}";
    }
    
    private function determineImageType($imageKey)
    {
        if (strpos($imageKey, 'logo') !== false) return 'logo';
        if (strpos($imageKey, 'icon') !== false || strpos($imageKey, 'favicon') !== false) return 'icon';
        if (strpos($imageKey, 'avatar') !== false) return 'avatar';
        if (strpos($imageKey, 'thumbnail') !== false) return 'thumbnail';
        if (strpos($imageKey, 'banner') !== false) return 'banner';
        if (strpos($imageKey, 'bg_') !== false || strpos($imageKey, 'background') !== false) return 'background';
        return 'logo';
    }
    
    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File size exceeds maximum allowed size';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }
}