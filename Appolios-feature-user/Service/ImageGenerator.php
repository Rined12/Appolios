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
        
        // Different style variations for uniqueness
        $styles = [
            'futuristic technology blue and cyan colors, 3D render',
            'warm sunset gradient orange and pink, abstract art',
            'dark mode cyberpunk neon purple and green, digital art',
            'minimalist white and gold elegant design, luxury',
            'nature green forest and mountains, landscape photography',
            'ocean deep blue and teal, underwater theme',
            'geometric shapes pattern, modern abstract',
            'gradient pink purple blue, colorful gradient background',
            'dark mysterious purple and black, elegant dark theme',
            'bright sunshine yellow and orange, energetic vibe'
        ];
        
        // Pick random style
        $style = $styles[array_rand($styles)];
        
        // Build unique prompt based on course content
        $keywords = $this->extractKeywords($searchTerm);
        
        // More descriptive prompt for accurate imagery
        $prompt = "$keywords, professional course thumbnail, clean modern design, high quality, educational, no text, detailed illustration, vibrant colors";
        
        // Use timestamp + random for unique seed
        $seed = substr(preg_replace('/[^a-zA-Z]/', '', $searchTerm), 0, 10) . rand(1000, 9999);
        
        // Try multiple AI services in sequence
        $services = [
            // Pollinations with Flux model (often less busy)
            "https://image.pollinations.ai/prompt/" . rawurlencode($prompt) . "?width=1024&height=512&nologo=true&seed=" . $seed . "&model=flux&enhance=true",
            // Pollinations standard
            "https://image.pollinations.ai/prompt/" . rawurlencode($prompt) . "?width=1024&height=512&nologo=true&seed=" . $seed . "&enhance=true",
            // Pollinations turbo
            "https://image.pollinations.ai/prompt/" . rawurlencode($prompt) . "?width=1024&height=512&nologo=true&seed=" . $seed . "&model=turbo&enhance=true"
        ];
        
        foreach ($services as $imageUrl) {
            $result = $this->downloadImage($imageUrl, $seed);
            if ($result) {
                return $result;
            }
            sleep(1); // Wait between attempts
        }
        
        // Final fallback to picsum
        $picsumId = abs(crc32($seed));
        $fallbackUrl = "https://picsum.photos/seed/{$picsumId}/800/400";
        return $this->downloadImage($fallbackUrl, 'picsum_' . $seed);
    }
    
    private function extractKeywords($title) {
        // Specific keywords with visual elements for accurate images
        $keywords = [
            'Java' => 'Java programming language logo blue coffee cup code developer, software development',
            'Python' => 'Python programming language logo yellow snake code data science',
            'Web Development' => 'HTML CSS JavaScript code web design developer laptop screen',
            'AI' => 'artificial intelligence robot brain neural network machine learning technology',
            'Machine Learning' => 'neural network AI algorithm data science deep learning futuristic',
            'Data Science' => 'data analytics charts graphs database visualization statistics',
            'Programming' => 'computer code programming developer software algorithm',
            'React' => 'React JS logo blue atom web development javascript',
            'Node' => 'Node.js logo green hexagon server backend development',
            'Database' => 'database server storage cylinders technology data',
            'Mobile' => 'smartphone mobile app development android ios',
            'Game' => 'game controller gaming pixel art video game',
            'Cyber' => 'cybersecurity hacker lock shield digital security',
            'Cloud' => 'cloud computing network server data center technology',
            'Design' => 'graphic design creativity art colors illustration',
            'Marketing' => 'marketing business growth strategy analytics',
            'English' => 'language learning books education vocabulary',
            'Business' => 'business meeting office professional corporate',
            'Finance' => 'finance money chart investment banking'
        ];
        
        foreach ($keywords as $key => $value) {
            if (stripos($title, $key) !== false) {
                return $value;
            }
        }
        
        return 'online learning education course';
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