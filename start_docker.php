<?php
// Docker startup helper
echo "<h1>🐳 Docker Startup Helper</h1>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>⚠️ Docker Issue Detected</h2>";
echo "<p>The diagnostic shows that Docker Compose is not running. This is the main cause of your 404 errors.</p>";
echo "</div>";

echo "<h2>🚀 How to Start Docker</h2>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>Method 1: Command Line (Recommended)</h3>";
echo "<ol>";
echo "<li>Open terminal/command prompt</li>";
echo "<li>Navigate to your EasyStream directory (where docker-compose.yml is located)</li>";
echo "<li>Run this command:</li>";
echo "</ol>";
echo "<div style='background: #000; color: #0f0; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0;'>";
echo "docker-compose up -d";
echo "</div>";
echo "<p><strong>Wait 2-3 minutes</strong> for all services to start, then try accessing your site again.</p>";
echo "</div>";

echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>Method 2: Docker Desktop (If you have it installed)</h3>";
echo "<ol>";
echo "<li>Open Docker Desktop application</li>";
echo "<li>Make sure Docker Desktop is running</li>";
echo "<li>Navigate to your EasyStream directory in terminal</li>";
echo "<li>Run: <code>docker-compose up -d</code></li>";
echo "</ol>";
echo "</div>";

echo "<h2>🔍 Check Docker Status</h2>";
echo "<p>After starting Docker, you can check if it's working:</p>";

// Try to check Docker status
$docker_status = "Unknown";
$docker_output = [];

if (function_exists('exec')) {
    // Try to get Docker status
    @exec('docker --version 2>&1', $docker_version_output, $docker_version_return);
    @exec('docker-compose --version 2>&1', $compose_version_output, $compose_version_return);
    @exec('docker-compose ps 2>&1', $docker_output, $docker_return);
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    
    if ($docker_version_return === 0 && !empty($docker_version_output)) {
        echo "<p>✅ Docker installed: " . htmlspecialchars(implode(' ', $docker_version_output)) . "</p>";
    } else {
        echo "<p>❌ Docker not found or not installed</p>";
    }
    
    if ($compose_version_return === 0 && !empty($compose_version_output)) {
        echo "<p>✅ Docker Compose installed: " . htmlspecialchars(implode(' ', $compose_version_output)) . "</p>";
    } else {
        echo "<p>❌ Docker Compose not found or not installed</p>";
    }
    
    if ($docker_return === 0 && !empty($docker_output)) {
        echo "<h4>Current Docker Status:</h4>";
        echo "<pre style='background: white; padding: 10px; border-radius: 3px;'>";
        echo htmlspecialchars(implode("\n", $docker_output));
        echo "</pre>";
    } else {
        echo "<p>❌ Docker Compose services not running</p>";
    }
    
    echo "</div>";
} else {
    echo "<p>⚠️ Cannot check Docker status (exec function disabled)</p>";
}

echo "<h2>📋 What Happens When Docker Starts</h2>";
echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 5px;'>";
echo "<ul>";
echo "<li><strong>Database (MariaDB):</strong> Starts on port 3306</li>";
echo "<li><strong>Web Server (Caddy):</strong> Starts on port 8083</li>";
echo "<li><strong>Redis:</strong> Starts on port 6379</li>";
echo "<li><strong>SRS (Streaming):</strong> Starts on port 1935</li>";
echo "</ul>";
echo "<p>All environment variables will be automatically set, and the database will be initialized.</p>";
echo "</div>";

echo "<h2>🧪 Test After Starting Docker</h2>";
echo "<p>Once Docker is running, test these links:</p>";
echo "<ul>";
echo "<li><a href='/' target='_blank'>Main Site (http://localhost:8083)</a></li>";
echo "<li><a href='/admin' target='_blank'>Admin Panel</a></li>";
echo "<li><a href='/working_index.php' target='_blank'>Working Index</a></li>";
echo "<li><a href='/debug_404_final.php' target='_blank'>Run Diagnostics Again</a></li>";
echo "</ul>";

echo "<h2>❓ Don't Have Docker?</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<p>If you don't have Docker installed:</p>";
echo "<ol>";
echo "<li>Download Docker Desktop from <a href='https://www.docker.com/products/docker-desktop' target='_blank'>docker.com</a></li>";
echo "<li>Install and start Docker Desktop</li>";
echo "<li>Then run the <code>docker-compose up -d</code> command</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🔄 Alternative: Manual Environment Setup</h2>";
echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Advanced users only:</strong> If you can't use Docker, you'll need to:</p>";
echo "<ul>";
echo "<li>Install MariaDB/MySQL manually</li>";
echo "<li>Install Redis manually</li>";
echo "<li>Set up environment variables manually</li>";
echo "<li>Configure a web server (Apache/Nginx)</li>";
echo "</ul>";
echo "<p>Docker is much easier and is the recommended approach.</p>";
echo "</div>";
?>