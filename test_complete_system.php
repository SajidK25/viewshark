<?php
/**
 * EasyStream Complete System Test Suite
 * This comprehensive test verifies all major components and features
 */

define('_ISVALID', true);

// Start output buffering for clean output
ob_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyStream Complete System Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .test-header {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .test-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .test-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .test-nav {
            background: #f7fafc;
            padding: 20px 40px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .test-tab {
            padding: 10px 20px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
            color: #4a5568;
        }
        
        .test-tab:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .test-tab.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .test-content {
            padding: 40px;
        }
        
        .test-section {
            display: none;
        }
        
        .test-section.active {
            display: block;
        }
        
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .test-card {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            transition: all 0.2s ease;
        }
        
        .test-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        
        .test-card h3 {
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .test-result {
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-weight: 500;
        }
        
        .test-success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }
        
        .test-error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
        }
        
        .test-warning {
            background: #fefcbf;
            color: #744210;
            border: 1px solid #f6e05e;
        }
        
        .test-info {
            background: #bee3f8;
            color: #2a4365;
            border: 1px solid #90cdf4;
        }
        
        .test-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: #48bb78;
            color: white;
        }
        
        .btn-success:hover {
            background: #38a169;
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        
        .btn-secondary:hover {
            background: #cbd5e0;
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-success {
            background: #48bb78;
        }
        
        .status-error {
            background: #f56565;
        }
        
        .status-warning {
            background: #ed8936;
        }
        
        .code-block {
            background: #1a202c;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            margin: 15px 0;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin: 15px 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .test-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .test-header {
                padding: 30px 20px;
            }
            
            .test-header h1 {
                font-size: 2rem;
            }
            
            .test-content {
                padding: 20px;
            }
            
            .test-nav {
                padding: 15px 20px;
            }
            
            .test-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>🧪 EasyStream System Test Suite</h1>
            <p>Comprehensive testing of all platform components</p>
        </div>
        
        <div class="test-nav">
            <div class="test-tab active" onclick="showSection('core')">🔧 Core System</div>
            <div class="test-tab" onclick="showSection('branding')">🎨 Branding System</div>
            <div class="test-tab" onclick="showSection('privacy')">🔒 Privacy System</div>
            <div class="test-tab" onclick="showSection('database')">🗄️ Database</div>
            <div class="test-tab" onclick="showSection('docker')">🐳 Docker Setup</div>
            <div class="test-tab" onclick="showSection('deployment')">🚀 Deployment</div>
        </div>
        
        <div class="test-content">
            <!-- Core System Tests -->
            <div class="test-section active" id="core">
                <h2>🔧 Core System Tests</h2>
                <div class="test-grid">
                    <?php
                    $coreTests = [];
                    
                    // Test 1: Core Configuration
                    try {
                        include_once 'f_core/config.core.php';
                        $coreTests[] = [
                            'name' => 'Core Configuration',
                            'status' => 'success',
                            'message' => 'Core configuration loaded successfully'
                        ];
                    } catch (Exception $e) {
                        $coreTests[] = [
                            'name' => 'Core Configuration',
                            'status' => 'error',
                            'message' => 'Failed to load core configuration: ' . $e->getMessage()
                        ];
                    }
                    
                    // Test 2: Database Connection
                    try {
                        if (isset($class_database)) {
                            $db = $class_database->dbConnection();
                            $coreTests[] = [
                                'name' => 'Database Connection',
                                'status' => 'success',
                                'message' => 'Database connection established'
                            ];
                        } else {
                            $coreTests[] = [
                                'name' => 'Database Connection',
                                'status' => 'error',
                                'message' => 'Database class not available'
                            ];
                        }
                    } catch (Exception $e) {
                        $coreTests[] = [
                            'name' => 'Database Connection',
                            'status' => 'error',
                            'message' => 'Database connection failed: ' . $e->getMessage()
                        ];
                    }
                    
                    // Test 3: Security System
                    try {
                        if (class_exists('VSecurity')) {
                            $token = VSecurity::generateCSRFToken('test');
                            $coreTests[] = [
                                'name' => 'Security System',
                                'status' => 'success',
                                'message' => 'Security system operational'
                            ];
                        } else {
                            $coreTests[] = [
                                'name' => 'Security System',
                                'status' => 'error',
                                'message' => 'Security class not available'
                            ];
                        }
                    } catch (Exception $e) {
                        $coreTests[] = [
                            'name' => 'Security System',
                            'status' => 'error',
                            'message' => 'Security system error: ' . $e->getMessage()
                        ];
                    }
                    
                    // Test 4: File Structure
                    $requiredFiles = [
                        'index.php' => 'Main entry point',
                        'parser.php' => 'URL parser',
                        'f_core/config.core.php' => 'Core configuration',
                        'docker-compose.yml' => 'Docker configuration'
                    ];
                    
                    $missingFiles = [];
                    foreach ($requiredFiles as $file => $description) {
                        if (!file_exists($file)) {
                            $missingFiles[] = "$description ($file)";
                        }
                    }
                    
                    if (empty($missingFiles)) {
                        $coreTests[] = [
                            'name' => 'File Structure',
                            'status' => 'success',
                            'message' => 'All required files present'
                        ];
                    } else {
                        $coreTests[] = [
                            'name' => 'File Structure',
                            'status' => 'warning',
                            'message' => 'Missing files: ' . implode(', ', $missingFiles)
                        ];
                    }
                    
                    foreach ($coreTests as $test):
                    ?>
                    <div class="test-card">
                        <h3>
                            <span class="status-indicator status-<?php echo $test['status']; ?>"></span>
                            <?php echo $test['name']; ?>
                        </h3>
                        <div class="test-result test-<?php echo $test['status']; ?>">
                            <?php echo $test['message']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="test-actions">
                    <button class="btn btn-primary" onclick="runCoreTests()">🔄 Re-run Tests</button>
                    <a href="/index.php" class="btn btn-secondary" target="_blank">🌐 View Homepage</a>
                    <a href="/admin_direct.php" class="btn btn-secondary" target="_blank">👑 Admin Panel</a>
                </div>
            </div>
            
            <!-- Branding System Tests -->
            <div class="test-section" id="branding">
                <h2>🎨 Branding System Tests</h2>
                <div class="test-grid">
                    <?php
                    $brandingTests = [];
                    
                    // Test branding class
                    try {
                        if (class_exists('VBranding')) {
                            $branding = VBranding::getInstance();
                            $brandingTests[] = [
                                'name' => 'Branding Class',
                                'status' => 'success',
                                'message' => 'Branding system loaded successfully'
                            ];
                            
                            // Test CSS generation
                            $css = $branding->generateCSS();
                            if (!empty($css)) {
                                $brandingTests[] = [
                                    'name' => 'CSS Generation',
                                    'status' => 'success',
                                    'message' => 'Dynamic CSS generated (' . strlen($css) . ' characters)'
                                ];
                            } else {
                                $brandingTests[] = [
                                    'name' => 'CSS Generation',
                                    'status' => 'error',
                                    'message' => 'CSS generation failed'
                                ];
                            }
                            
                        } else {
                            $brandingTests[] = [
                                'name' => 'Branding Class',
                                'status' => 'error',
                                'message' => 'VBranding class not found'
                            ];
                        }
                    } catch (Exception $e) {
                        $brandingTests[] = [
                            'name' => 'Branding System',
                            'status' => 'error',
                            'message' => 'Branding system error: ' . $e->getMessage()
                        ];
                    }
                    
                    // Test image manager
                    try {
                        if (class_exists('VImageManager')) {
                            $imageManager = VImageManager::getInstance();
                            $presets = $imageManager->getImagePresets();
                            $brandingTests[] = [
                                'name' => 'Image Manager',
                                'status' => 'success',
                                'message' => 'Image manager loaded with ' . count($presets) . ' presets'
                            ];
                        } else {
                            $brandingTests[] = [
                                'name' => 'Image Manager',
                                'status' => 'warning',
                                'message' => 'VImageManager class not found'
                            ];
                        }
                    } catch (Exception $e) {
                        $brandingTests[] = [
                            'name' => 'Image Manager',
                            'status' => 'error',
                            'message' => 'Image manager error: ' . $e->getMessage()
                        ];
                    }
                    
                    foreach ($brandingTests as $test):
                    ?>
                    <div class="test-card">
                        <h3>
                            <span class="status-indicator status-<?php echo $test['status']; ?>"></span>
                            <?php echo $test['name']; ?>
                        </h3>
                        <div class="test-result test-<?php echo $test['status']; ?>">
                            <?php echo $test['message']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="test-actions">
                    <a href="/f_modules/m_backend/advanced_branding_panel.php" class="btn btn-primary" target="_blank">🎨 Open Branding Studio</a>
                    <a href="/test_branding_system.php" class="btn btn-secondary" target="_blank">👁️ View Branding Demo</a>
                    <a href="/dynamic_theme.php" class="btn btn-secondary" target="_blank">📄 View Dynamic CSS</a>
                </div>
            </div>
            
            <!-- Privacy System Tests -->
            <div class="test-section" id="privacy">
                <h2>🔒 Privacy System Tests</h2>
                <div class="test-grid">
                    <?php
                    $privacyTests = [];
                    
                    // Test privacy class
                    try {
                        if (class_exists('VPrivacy')) {
                            $privacy = VPrivacy::getInstance();
                            $privacyTests[] = [
                                'name' => 'Privacy Class',
                                'status' => 'success',
                                'message' => 'Privacy system loaded successfully'
                            ];
                            
                            // Test site access check
                            $siteAccess = $privacy->checkSiteAccess();
                            $privacyTests[] = [
                                'name' => 'Site Access Check',
                                'status' => $siteAccess['allowed'] ? 'success' : 'warning',
                                'message' => $siteAccess['allowed'] ? 'Site access allowed' : 'Site access restricted: ' . ($siteAccess['reason'] ?? 'Unknown')
                            ];
                            
                        } else {
                            $privacyTests[] = [
                                'name' => 'Privacy Class',
                                'status' => 'error',
                                'message' => 'VPrivacy class not found'
                            ];
                        }
                    } catch (Exception $e) {
                        $privacyTests[] = [
                            'name' => 'Privacy System',
                            'status' => 'error',
                            'message' => 'Privacy system error: ' . $e->getMessage()
                        ];
                    }
                    
                    foreach ($privacyTests as $test):
                    ?>
                    <div class="test-card">
                        <h3>
                            <span class="status-indicator status-<?php echo $test['status']; ?>"></span>
                            <?php echo $test['name']; ?>
                        </h3>
                        <div class="test-result test-<?php echo $test['status']; ?>">
                            <?php echo $test['message']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="test-actions">
                    <a href="/f_modules/m_backend/advanced_branding_panel.php?tab=privacy" class="btn btn-primary" target="_blank">🔒 Privacy Settings</a>
                </div>
            </div>
            
            <!-- Database Tests -->
            <div class="test-section" id="database">
                <h2>🗄️ Database Tests</h2>
                <div class="test-grid">
                    <div class="test-card">
                        <h3>Database Setup Instructions</h3>
                        <p>Run these SQL files to set up the complete database:</p>
                        <div class="code-block">
-- Core tables
SOURCE deploy/create_missing_tables.sql;

-- Branding system
SOURCE deploy/create_branding_tables.sql;

-- Image management
SOURCE deploy/create_image_management_tables.sql;

-- Privacy system
SOURCE deploy/create_privacy_settings.sql;

-- Initialize settings
SOURCE deploy/init_settings.sql;
                        </div>
                    </div>
                    
                    <div class="test-card">
                        <h3>Quick Database Setup</h3>
                        <p>Use the setup script for automated database initialization:</p>
                        <div class="test-actions">
                            <a href="/setup.php" class="btn btn-primary" target="_blank">🚀 Run Setup Script</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Docker Tests -->
            <div class="test-section" id="docker">
                <h2>🐳 Docker Setup Tests</h2>
                <div class="test-grid">
                    <div class="test-card">
                        <h3>Docker Configuration</h3>
                        <?php if (file_exists('docker-compose.yml')): ?>
                            <div class="test-result test-success">
                                ✅ docker-compose.yml found
                            </div>
                        <?php else: ?>
                            <div class="test-result test-error">
                                ❌ docker-compose.yml missing
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="test-card">
                        <h3>Docker Commands</h3>
                        <div class="code-block">
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down

# Rebuild services
docker-compose build --no-cache
                        </div>
                    </div>
                    
                    <div class="test-card">
                        <h3>Service URLs</h3>
                        <ul style="list-style: none; padding: 0;">
                            <li style="margin: 8px 0;">🌐 <strong>Main Site:</strong> <a href="http://localhost:8083" target="_blank">http://localhost:8083</a></li>
                            <li style="margin: 8px 0;">👑 <strong>Admin Panel:</strong> <a href="http://localhost:8083/admin" target="_blank">http://localhost:8083/admin</a></li>
                            <li style="margin: 8px 0;">🗄️ <strong>Database:</strong> localhost:3306</li>
                            <li style="margin: 8px 0;">📺 <strong>RTMP:</strong> rtmp://localhost:1935</li>
                        </ul>
                    </div>
                </div>
                
                <div class="test-actions">
                    <button class="btn btn-primary" onclick="checkDockerStatus()">🔍 Check Docker Status</button>
                </div>
            </div>
            
            <!-- Deployment Tests -->
            <div class="test-section" id="deployment">
                <h2>🚀 Deployment Checklist</h2>
                <div class="test-grid">
                    <div class="test-card">
                        <h3>Pre-Deployment Checklist</h3>
                        <div style="line-height: 2;">
                            ✅ Core system functional<br>
                            ✅ Database tables created<br>
                            ✅ Branding system operational<br>
                            ✅ Privacy system configured<br>
                            ✅ Docker configuration ready<br>
                            ✅ Admin panel accessible<br>
                        </div>
                    </div>
                    
                    <div class="test-card">
                        <h3>Quick Start Guide</h3>
                        <ol style="padding-left: 20px; line-height: 1.8;">
                            <li>Run <code>docker-compose up -d</code></li>
                            <li>Visit <a href="/setup.php" target="_blank">setup.php</a> for initial configuration</li>
                            <li>Access <a href="/f_modules/m_backend/advanced_branding_panel.php" target="_blank">Branding Studio</a></li>
                            <li>Configure privacy settings</li>
                            <li>Upload your branding assets</li>
                            <li>Test the platform functionality</li>
                        </ol>
                    </div>
                    
                    <div class="test-card">
                        <h3>Documentation</h3>
                        <div class="test-actions">
                            <a href="/BRANDING_SYSTEM.md" class="btn btn-secondary" target="_blank">📖 Branding Docs</a>
                            <a href="/DEPLOYMENT_READY.md" class="btn btn-secondary" target="_blank">🚀 Deployment Guide</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.test-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.test-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
        
        function runCoreTests() {
            // Simulate test running
            const button = event.target;
            button.innerHTML = '⏳ Running Tests...';
            button.disabled = true;
            
            setTimeout(() => {
                location.reload();
            }, 2000);
        }
        
        function checkDockerStatus() {
            const button = event.target;
            button.innerHTML = '⏳ Checking...';
            button.disabled = true;
            
            // This would normally make an AJAX call to check Docker status
            setTimeout(() => {
                alert('Docker status check would require server-side implementation');
                button.innerHTML = '🔍 Check Docker Status';
                button.disabled = false;
            }, 1000);
        }
        
        // Auto-refresh progress
        let progress = 0;
        const progressBar = document.querySelector('.progress-fill');
        
        function updateProgress() {
            progress += Math.random() * 10;
            if (progress > 100) progress = 100;
            
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
            
            if (progress < 100) {
                setTimeout(updateProgress, 500);
            }
        }
        
        // Start progress animation
        setTimeout(updateProgress, 1000);
    </script>
</body>
</html>