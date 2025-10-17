<?php
/*******************************************************************************************************************
| Video Processing Job
| Handles video transcoding and processing in the background
|*******************************************************************************************************************/

class VideoProcessingJob extends BaseJob
{
    /**
     * Handle video processing
     * @param array $data Video processing data
     * @return array Processing result
     */
    public function handle($data)
    {
        $this->validateData($data, ['video_id', 'input_file', 'output_dir']);
        
        $videoId = $data['video_id'];
        $inputFile = $data['input_file'];
        $outputDir = $data['output_dir'];
        $formats = $data['formats'] ?? ['720p', '480p', '360p'];
        
        $this->logProgress('Starting video processing', [
            'video_id' => $videoId,
            'input_file' => $inputFile,
            'formats' => $formats
        ]);
        
        try {
            // Validate input file exists
            if (!file_exists($inputFile)) {
                throw new Exception("Input file not found: {$inputFile}");
            }
            
            // Create output directory
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            
            // Update video status to processing
            $this->updateVideoStatus($videoId, 'processing');
            
            $results = [];
            $totalFormats = count($formats);
            
            foreach ($formats as $index => $format) {
                $this->updateProgress($index + 1, $totalFormats, "Processing {$format}");
                
                $result = $this->processVideoFormat($inputFile, $outputDir, $format, $videoId);
                $results[$format] = $result;
                
                if (!$result['success']) {
                    $this->logError("Failed to process {$format}", $result);
                }
            }
            
            // Generate thumbnail
            $thumbnailResult = $this->generateThumbnail($inputFile, $outputDir, $videoId);
            $results['thumbnail'] = $thumbnailResult;
            
            // Update video status based on results
            $allSuccessful = true;
            foreach ($results as $result) {
                if (!$result['success']) {
                    $allSuccessful = false;
                    break;
                }
            }
            
            $finalStatus = $allSuccessful ? 'completed' : 'failed';
            $this->updateVideoStatus($videoId, $finalStatus);
            
            $this->logProgress('Video processing completed', [
                'video_id' => $videoId,
                'status' => $finalStatus,
                'results' => $results
            ]);
            
            // Send notification
            $this->sendNotification('Video processing completed', [
                'video_id' => $videoId,
                'status' => $finalStatus
            ]);
            
            return [
                'video_id' => $videoId,
                'status' => $finalStatus,
                'results' => $results
            ];
            
        } catch (Exception $e) {
            $this->updateVideoStatus($videoId, 'failed');
            
            $this->logError('Video processing failed', [
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Process video to specific format
     * @param string $inputFile Input file path
     * @param string $outputDir Output directory
     * @param string $format Format (720p, 480p, etc.)
     * @param string $videoId Video ID
     * @return array Processing result
     */
    private function processVideoFormat($inputFile, $outputDir, $format, $videoId)
    {
        $outputFile = $outputDir . '/' . $videoId . '_' . $format . '.mp4';
        
        // Define format settings
        $formatSettings = [
            '1080p' => ['width' => 1920, 'height' => 1080, 'bitrate' => '5000k'],
            '720p'  => ['width' => 1280, 'height' => 720,  'bitrate' => '2500k'],
            '480p'  => ['width' => 854,  'height' => 480,  'bitrate' => '1000k'],
            '360p'  => ['width' => 640,  'height' => 360,  'bitrate' => '750k'],
            '240p'  => ['width' => 426,  'height' => 240,  'bitrate' => '400k']
        ];
        
        if (!isset($formatSettings[$format])) {
            return [
                'success' => false,
                'error' => "Unknown format: {$format}",
                'output_file' => null
            ];
        }
        
        $settings = $formatSettings[$format];
        
        // Build FFmpeg command
        $ffmpegCmd = sprintf(
            'ffmpeg -i %s -vf "scale=%d:%d:force_original_aspect_ratio=decrease,pad=%d:%d:(ow-iw)/2:(oh-ih)/2" -c:v libx264 -b:v %s -c:a aac -b:a 128k -movflags +faststart %s 2>&1',
            escapeshellarg($inputFile),
            $settings['width'],
            $settings['height'],
            $settings['width'],
            $settings['height'],
            $settings['bitrate'],
            escapeshellarg($outputFile)
        );
        
        $this->logProgress("Executing FFmpeg for {$format}", [
            'command' => $ffmpegCmd,
            'output_file' => $outputFile
        ]);
        
        // Execute FFmpeg
        $output = [];
        $returnCode = 0;
        exec($ffmpegCmd, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($outputFile)) {
            return [
                'success' => true,
                'output_file' => $outputFile,
                'file_size' => filesize($outputFile),
                'format' => $format
            ];
        } else {
            return [
                'success' => false,
                'error' => 'FFmpeg processing failed',
                'return_code' => $returnCode,
                'output' => implode("\n", $output),
                'output_file' => $outputFile
            ];
        }
    }
    
    /**
     * Generate video thumbnail
     * @param string $inputFile Input file path
     * @param string $outputDir Output directory
     * @param string $videoId Video ID
     * @return array Generation result
     */
    private function generateThumbnail($inputFile, $outputDir, $videoId)
    {
        $thumbnailFile = $outputDir . '/' . $videoId . '_thumb.jpg';
        
        // Generate thumbnail at 10% of video duration
        $ffmpegCmd = sprintf(
            'ffmpeg -i %s -ss 00:00:10 -vframes 1 -vf "scale=320:240:force_original_aspect_ratio=decrease,pad=320:240:(ow-iw)/2:(oh-ih)/2" %s 2>&1',
            escapeshellarg($inputFile),
            escapeshellarg($thumbnailFile)
        );
        
        $output = [];
        $returnCode = 0;
        exec($ffmpegCmd, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($thumbnailFile)) {
            return [
                'success' => true,
                'thumbnail_file' => $thumbnailFile,
                'file_size' => filesize($thumbnailFile)
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Thumbnail generation failed',
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ];
        }
    }
    
    /**
     * Update video processing status in database
     * @param string $videoId Video ID
     * @param string $status Status (processing, completed, failed)
     */
    private function updateVideoStatus($videoId, $status)
    {
        try {
            $db = $this->getDatabase();
            
            $updateData = [
                'processing_status' => $status,
                'processed_at' => date('Y-m-d H:i:s')
            ];
            
            // Update video record
            $db->doUpdate('db_videofiles', 'file_key', $updateData, $videoId);
            
            $this->logProgress("Updated video status to {$status}", [
                'video_id' => $videoId,
                'status' => $status
            ]);
            
        } catch (Exception $e) {
            $this->logError('Failed to update video status', [
                'video_id' => $videoId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
        }
    }
}