<?php
// Clean up any remaining hardcoded URLs
echo "<h1>🧹 EasyStream URL Cleanup</h1>";

$files_checked = 0;
$replacements_made = 0;

// Files to check (excluding vendor/third-party files)
$files_to_check = [
    'f_core/config.set.php',
    'docker-compose.yml',
    'README.md',
    '__install/INSTALL.txt'
];

// URLs to replace
$url_replacements = [
    'https://test.watchmaji.com' => 'http://localhost:8083',
    'http://test.watchmaji.com' => 'http://localhost:8083',
    'test.watchmaji.com' => 'localhost:8083'
];

echo "<h2>Checking Files for Hardcoded URLs</h2>";

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $original_content = $content;
        $file_changes = 0;
        
        foreach ($url_replacements as $old_url => $new_url) {
            $count = 0;
            $content = str_replace($old_url, $new_url, $content, $count);
            if ($count > 0) {
                $file_changes += $count;
                $replacements_made += $count;
            }
        }
        
        if ($file_changes > 0) {
            file_put_contents($file, $content);
            echo "✅ $file: $file_changes replacements made<br>";
        } else {
            echo "✅ $file: No changes needed<br>";
        }
        
        $files_checked++;
    } else {
        echo "⚠️ $file: File not found<br>";
    }
}

echo "<h2>Summary</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; border-radius: 5px;'>";
echo "<strong>✅ Cleanup Complete!</strong><br>";
echo "Files checked: $files_checked<br>";
echo "Total replacements: $replacements_made<br>";
echo "</div>";

if ($replacements_made > 0) {
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; border-radius: 5px; margin-top: 15px;'>";
    echo "<strong>🔄 Restart Required</strong><br>";
    echo "If using Docker, restart your containers:<br>";
    echo "<code>docker-compose down && docker-compose up -d</code>";
    echo "</div>";
}

echo "<h2>Current Configuration</h2>";
if (file_exists('f_core/config.set.php')) {
    $config_content = file_get_contents('f_core/config.set.php');
    if (preg_match('/\$cfg\[\'main_url\'\]\s*=\s*getenv\(\'MAIN_URL\'\)\s*\?\:\s*\'([^\']*)\';/', $config_content, $matches)) {
        echo "<div style='background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; border-radius: 5px;'>";
        echo "<strong>Current Default URL:</strong> " . htmlspecialchars($matches[1]) . "<br>";
        echo "<strong>Environment Variable:</strong> MAIN_URL<br>";
        echo "</div>";
    }
}

echo "<h2>Next Steps</h2>";
echo "<ul>";
echo "<li>✅ All hardcoded URLs have been standardized to <code>http://localhost:8083</code></li>";
echo "<li>🔧 Users can customize the URL during setup at <a href='/setup.php'>/setup.php</a></li>";
echo "<li>⚙️ URL can be changed anytime at <a href='/configure_url.php'>/configure_url.php</a></li>";
echo "<li>📚 See <a href='/URL_CONFIGURATION.md'>URL_CONFIGURATION.md</a> for detailed instructions</li>";
echo "</ul>";
?>