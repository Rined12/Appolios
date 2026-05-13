<?php
$dir = 'c:\\xampp\\htdocs\\Appolios\\View\\BackOffice\\admin\\';
$files = scandir($dir);
$count = 0;

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($dir . $file);
        
        $hasDash = strpos($content, 'class="dashboard"') !== false;
        $hasLayout = strpos($content, 'class="admin-layout"') !== false;
        
        if ($hasDash && $hasLayout) {
            $newContent = preg_replace('~<div\s+class="dashboard">\s*<div\s+class="container[^>]*>\s*<div\s+class="admin-layout">\s*(<\?php[^>]*require[^>]*partials/sidebar\.php[^>]*\?'.'>)?~is', '', $content);
            
            if ($newContent !== $content) {
                $newContent = preg_replace('~</div>\s*</div>\s*</div>(\s*<(style|script)>|\s*$)~is', '$1', $newContent);
                $newContent = preg_replace('~<\?php\s+(?:.*?;)?\s*require[^>]*partials/sidebar\.php\';\s*\?'.'>~is', '', $newContent);
                
                file_put_contents($dir . $file, $newContent);
                echo "Stripped wrappers from " . $file . "<br>";
                $count++;
            }
        }
    }
}
echo "Total files stripped: " . $count;
