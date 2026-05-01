<?php
/**
 * AI-style Image Generator for Courses
 * Uses Pollinations.ai for AI generation with local download
 */

class ImageGenerator {
    
    private string $uploadDir;
    private string $uploadUrl;
    
    public function __construct() {
        $this->uploadDir = 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/uploads/images/';
        $this->uploadUrl = 'uploads/images/';
    }

    public function generateForCourse($courseTitle, $categoryName = '') {
        return $this->generateAIPrompt($courseTitle, $categoryName);
    }

    public function generateAIPrompt($courseTitle, $categoryName = '') {
        $searchTerm = trim($courseTitle);
        
        // Build a better prompt with category context
        $categoryContext = $categoryName ? " in $categoryName" : '';
        $prompt = "$searchTerm$categoryContext, professional educational course thumbnail, modern flat design, vibrant colors, clean composition, high quality, no text";
        
        $seed = preg_replace('/[^a-zA-Z0-9]/', '', $searchTerm);
        $seed = substr($seed, 0, 15);
        if (empty($seed)) $seed = 'course';
        
        // Generate the Pollinations.ai URL
        $imageUrl = "https://image.pollinations.ai/prompt/" . rawurlencode($prompt) . "?width=800&height=400&nologo=true&seed=" . $seed . "&enhance=true";
        
        // Download and save the image locally
        return $this->downloadImage($imageUrl, $seed);
    }
    
    /**
     * Download image from URL and save locally
     * @param string $imageUrl The URL to download from
     * @param string $seed The seed for filename generation
     * @return string|null The local path or null on failure
     */
    private function downloadImage($imageUrl, $seed) {
        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                error_log("Failed to create upload directory: " . $this->uploadDir);
                return null;
            }
        }
        
        // Generate unique filename
        $filename = 'ai_' . $seed . '_' . time() . '.jpg';
        $localPath = $this->uploadDir . $filename;
        $relativePath = $this->uploadUrl . $filename;
        
        // Download image using cURL
        $ch = curl_init($imageUrl);
        if (!$ch) {
            error_log("Failed to initialize cURL for image download");
            return null;
        }
        
        $fp = fopen($localPath, 'wb');
        if (!$fp) {
            error_log("Failed to open file for writing: " . $localPath);
            curl_close($ch);
            return null;
        }
        
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        fclose($fp);
        
        // Check if download was successful
        if (!$success || $httpCode !== 200) {
            error_log("Failed to download image. HTTP code: " . $httpCode);
            @unlink($localPath);
            return null;
        }
        
        // Verify the downloaded file is a valid image
        $imageInfo = getimagesize($localPath);
        if ($imageInfo === false) {
            error_log("Downloaded file is not a valid image");
            @unlink($localPath);
            return null;
        }
        
        // Return the relative path for storage in database
        return $relativePath;
    }
}