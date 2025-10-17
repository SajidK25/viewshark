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

// Initialize systems
$branding = VBranding::getInstance();
$imageManager = VImageManager::getInstance();

// Handle AJAX requests
if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['ajax_action']) {
        case 'update_color':
            if (VSecurity::validateCSRFFromPost('color_update')) {
                $key = $_POST['key'] ?? '';
                $value = $_POST['value'] ?? '';
                
                if ($branding->set($key, $value, 'color')) {
                    echo json_encode(['success' => true, 'message' => 'Color updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update color']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Security validation failed']);
            }
            exit;
            
        case 'update_setting':
            if (VSecurity::validateCSRFFromPost('setting_update')) {
                $key = $_POST['key'] ?? '';
                $value = $_POST['value'] ?? '';
                $type = $_POST['type'] ?? 'text';
                
                if ($branding->set($key, $value, $type)) {
                    echo json_encode(['success' => true, 'message' => 'Setting updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update setting']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Security validation failed']);
            }
            exit;
            
        case 'generate_palette':
            $baseColor = $_POST['base_color'] ?? '#007bff';
            $palette = $this->generateColorPalette($baseColor);
            echo json_encode(['success' => true, 'palette' => $palette]);
            exit;
            
        case 'save_custom_preset':
            if (VSecurity::validateCSRFFromPost('preset_save')) {
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $settings = json_decode($_POST['settings'] ?? '{}', true);
                
                if ($branding->savePreset($name, $description, $settings)) {
                    echo json_encode(['success' => true, 'message' => 'Preset saved successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to save preset']);
                }
            }
            exit;
    }
}

// Get data for interface
$currentTab = $_GET['tab'] ?? 'colors';
$categories = $branding->getCategories();
$presets = $branding->getPresets();
$siteInfo = $branding->getSiteInfo();
$imagePresets = $imageManager->getImagePresets();
$uploadedImages = $imageManager->getUploadedImages();

// Get all color settings
$colorSettings = $branding->getByCategory('colors');
$backgroundSettings = $branding->getByCategory('backgrounds');
$textSettings = $branding->getByCategory('text');
$borderSettings = $branding->getByCategory('borders');

// Merge all color-related settings
$allColorSettings = array_merge($colorSettings, $backgroundSettings, $textSettings, $borderSettings);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Branding Studio - <?php echo htmlspecialchars($siteInfo['name']); ?></title>
    
    <!-- Color picker libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            color: #2d3748;
            line-height: 1.6;
        }
        
        .studio-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        /* Sidebar */
        .studio-sidebar {
            width: 280px;
            background: #1a202c;
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .studio-header {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            text-align: center;
        }
        
        .studio-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .studio-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .studio-nav {
            flex: 1;
            padding: 20px 0;
        }
        
        .nav-section {
            margin-bottom: 30px;
        }
        
        .nav-section-title {
            padding: 0 20px 10px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.7;
            font-weight: 600;
        }
        
        .nav-item {
            display: block;
            padding: 12px 20px;
            color: #cbd5e0;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-item.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #667eea;
        }
        
        .nav-item i {
            width: 20px;
            margin-right: 10px;
        }
        
        /* Main content */
        .studio-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .studio-toolbar {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .toolbar-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
        }
        
        .toolbar-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        
        .btn-secondary:hover {
            background: #cbd5e0;
        }
        
        .btn-success {
            background: #48bb78;
            color: white;
        }
        
        .btn-success:hover {
            background: #38a169;
        }
        
        .studio-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background: #f7fafc;
        }
        
        /* Color Studio */
        .color-studio {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            height: 100%;
        }
        
        .color-main {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .color-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .color-panel {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .panel-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2d3748;
        }
        
        /* Color Grid */
        .color-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .color-group {
            background: #f7fafc;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }
        
        .color-group-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2d3748;
            text-transform: capitalize;
        }
        
        .color-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .color-item:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
        }
        
        .color-swatch {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        
        .color-swatch:hover {
            transform: scale(1.05);
            border-color: #667eea;
        }
        
        .color-swatch::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .color-swatch:hover::after {
            opacity: 1;
        }
        
        .color-info {
            flex: 1;
        }
        
        .color-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
            text-transform: capitalize;
        }
        
        .color-value {
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.85rem;
            color: #718096;
            background: #f7fafc;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .color-description {
            font-size: 0.8rem;
            color: #a0aec0;
            margin-top: 4px;
        }
        
        /* Color Palette Generator */
        .palette-generator {
            text-align: center;
        }
        
        .base-color-picker {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid #e2e8f0;
            margin: 0 auto 20px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .base-color-picker:hover {
            transform: scale(1.05);
            border-color: #667eea;
        }
        
        .generated-palette {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 20px 0;
        }
        
        .palette-color {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .palette-color:hover {
            transform: scale(1.1);
            border-color: #667eea;
        }
        
        /* Live Preview */
        .live-preview {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            background: white;
        }
        
        .preview-header {
            background: var(--color-bg-header, #ffffff);
            padding: 15px 20px;
            border-bottom: 1px solid var(--color-border, #e2e8f0);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .preview-logo {
            font-weight: 700;
            color: var(--color-primary, #667eea);
            font-size: 1.2rem;
        }
        
        .preview-nav {
            display: flex;
            gap: 20px;
        }
        
        .preview-nav a {
            color: var(--color-text-primary, #2d3748);
            text-decoration: none;
            font-weight: 500;
        }
        
        .preview-content {
            padding: 20px;
            background: var(--color-bg-main, #ffffff);
        }
        
        .preview-card {
            background: var(--color-bg-card, #ffffff);
            border: 1px solid var(--color-border, #e2e8f0);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .preview-button {
            background: var(--color-primary, #667eea);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .preview-button.secondary {
            background: var(--color-secondary, #718096);
        }
        
        .preview-button.success {
            background: var(--color-success, #48bb78);
        }
        
        .preview-button.warning {
            background: var(--color-warning, #ed8936);
            color: #1a202c;
        }
        
        .preview-button.danger {
            background: var(--color-danger, #f56565);
        }
        
        /* Preset Gallery */
        .preset-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .preset-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .preset-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .preset-preview {
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .preset-preview::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(to top, rgba(0,0,0,0.3), transparent);
        }
        
        .preset-info {
            padding: 20px;
        }
        
        .preset-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #2d3748;
        }
        
        .preset-description {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .preset-colors {
            display: flex;
            gap: 4px;
            margin-bottom: 15px;
        }
        
        .preset-color {
            width: 20px;
            height: 20px;
            border-radius: 3px;
            border: 1px solid #e2e8f0;
        }
        
        .preset-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .color-studio {
                grid-template-columns: 1fr;
            }
            
            .color-sidebar {
                flex-direction: row;
                overflow-x: auto;
            }
            
            .color-panel {
                min-width: 300px;
            }
        }
        
        @media (max-width: 768px) {
            .studio-container {
                flex-direction: column;
            }
            
            .studio-sidebar {
                width: 100%;
                height: auto;
            }
            
            .studio-nav {
                display: flex;
                overflow-x: auto;
                padding: 10px 0;
            }
            
            .nav-section {
                display: flex;
                margin: 0;
                min-width: max-content;
            }
            
            .nav-item {
                white-space: nowrap;
                border-left: none;
                border-bottom: 3px solid transparent;
            }
            
            .nav-item.active {
                border-left: none;
                border-bottom-color: #667eea;
            }
            
            .color-grid {
                grid-template-columns: 1fr;
            }
            
            .studio-content {
                padding: 20px;
            }
        }
        
        /* Animations */
        @keyframes colorPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .color-swatch.updating {
            animation: colorPulse 0.6s ease-in-out;
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            background: #48bb78;
        }
        
        .notification.error {
            background: #f56565;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>
</head>
<body>
    <div class="studio-container">
        <!-- Sidebar -->
        <div class="studio-sidebar">
            <div class="studio-header">
                <h1>🎨 Branding Studio</h1>
                <p>Professional Theme Designer</p>
            </div>
            
            <nav class="studio-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Design</div>
                    <a href="?tab=colors" class="nav-item <?php echo $currentTab === 'colors' ? 'active' : ''; ?>">
                        <i>🎨</i> Color Palette
                    </a>
                    <a href="?tab=typography" class="nav-item <?php echo $currentTab === 'typography' ? 'active' : ''; ?>">
                        <i>📝</i> Typography
                    </a>
                    <a href="?tab=layout" class="nav-item <?php echo $currentTab === 'layout' ? 'active' : ''; ?>">
                        <i>📐</i> Layout & Spacing
                    </a>
                    <a href="?tab=images" class="nav-item <?php echo $currentTab === 'images' ? 'active' : ''; ?>">
                        <i>🖼️</i> Images & Logos
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Components</div>
                    <a href="?tab=buttons" class="nav-item <?php echo $currentTab === 'buttons' ? 'active' : ''; ?>">
                        <i>🔘</i> Buttons
                    </a>
                    <a href="?tab=badges" class="nav-item <?php echo $currentTab === 'badges' ? 'active' : ''; ?>">
                        <i>🏷️</i> Badges
                    </a>
                    <a href="?tab=player" class="nav-item <?php echo $currentTab === 'player' ? 'active' : ''; ?>">
                        <i>▶️</i> Video Player
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="?tab=presets" class="nav-item <?php echo $currentTab === 'presets' ? 'active' : ''; ?>">
                        <i>💾</i> Theme Presets
                    </a>
                    <a href="?tab=privacy" class="nav-item <?php echo $currentTab === 'privacy' ? 'active' : ''; ?>">
                        <i>🔒</i> Privacy & Access
                    </a>
                    <a href="?tab=export" class="nav-item <?php echo $currentTab === 'export' ? 'active' : ''; ?>">
                        <i>📤</i> Export & Import
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="studio-main">
            <div class="studio-toolbar">
                <div class="toolbar-title">
                    <?php
                    $titles = [
                        'colors' => '🎨 Color Palette Studio',
                        'typography' => '📝 Typography Designer',
                        'layout' => '📐 Layout & Spacing',
                        'images' => '🖼️ Image Manager',
                        'buttons' => '🔘 Button Designer',
                        'badges' => '🏷️ Badge Studio',
                        'player' => '▶️ Video Player Theme',
                        'presets' => '💾 Theme Presets',
                        'privacy' => '🔒 Privacy & Access Control',
                        'export' => '📤 Export & Import'
                    ];
                    echo $titles[$currentTab] ?? '🎨 Branding Studio';
                    ?>
                </div>
                
                <div class="toolbar-actions">
                    <button class="btn btn-secondary" onclick="resetToDefaults()">
                        <i>🔄</i> Reset
                    </button>
                    <button class="btn btn-primary" onclick="saveChanges()">
                        <i>💾</i> Save Changes
                    </button>
                    <button class="btn btn-success" onclick="previewChanges()">
                        <i>👁️</i> Preview
                    </button>
                </div>
            </div>
            
            <div class="studio-content">
                <?php if ($currentTab === 'colors'): ?>
                    <div class="color-studio">
                        <div class="color-main">
                            <div class="color-grid">
                                <?php
                                $colorGroups = [
                                    'Primary Colors' => ['color_primary', 'color_primary_dark', 'color_primary_light', 'color_secondary'],
                                    'Status Colors' => ['color_success', 'color_warning', 'color_danger', 'color_info'],
                                    'Background Colors' => ['color_bg_main', 'color_bg_secondary', 'color_bg_dark', 'color_bg_card', 'color_bg_header', 'color_bg_footer'],
                                    'Text Colors' => ['color_text_primary', 'color_text_secondary', 'color_text_muted', 'color_text_light', 'color_text_link', 'color_text_link_hover'],
                                    'Border Colors' => ['color_border', 'color_border_light', 'color_border_dark', 'color_shadow']
                                ];
                                
                                foreach ($colorGroups as $groupName => $colorKeys):
                                ?>
                                <div class="color-group">
                                    <div class="color-group-title"><?php echo $groupName; ?></div>
                                    <?php foreach ($colorKeys as $colorKey): ?>
                                        <?php if (isset($allColorSettings[$colorKey])): ?>
                                            <?php $setting = $allColorSettings[$colorKey]; ?>
                                            <div class="color-item">
                                                <div class="color-swatch" 
                                                     style="background-color: <?php echo htmlspecialchars($setting['value']); ?>"
                                                     data-color-key="<?php echo htmlspecialchars($colorKey); ?>"
                                                     data-current-color="<?php echo htmlspecialchars($setting['value']); ?>">
                                                </div>
                                                <div class="color-info">
                                                    <div class="color-name"><?php echo ucwords(str_replace(['color_', '_'], ['', ' '], $colorKey)); ?></div>
                                                    <div class="color-value"><?php echo htmlspecialchars($setting['value']); ?></div>
                                                    <?php if (!empty($setting['description'])): ?>
                                                        <div class="color-description"><?php echo htmlspecialchars($setting['description']); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="color-sidebar">
                            <!-- Color Palette Generator -->
                            <div class="color-panel">
                                <div class="panel-title">🎨 Palette Generator</div>
                                <div class="palette-generator">
                                    <div class="base-color-picker" id="basePicker" style="background-color: #667eea;"></div>
                                    <button class="btn btn-primary btn-small" onclick="generatePalette()">Generate Palette</button>
                                    <div class="generated-palette" id="generatedPalette">
                                        <!-- Generated colors will appear here -->
                                    </div>
                                    <button class="btn btn-secondary btn-small" onclick="applyGeneratedPalette()">Apply to Theme</button>
                                </div>
                            </div>
                            
                            <!-- Live Preview -->
                            <div class="color-panel">
                                <div class="panel-title">👁️ Live Preview</div>
                                <div class="live-preview">
                                    <div class="preview-header">
                                        <div class="preview-logo"><?php echo htmlspecialchars($siteInfo['name']); ?></div>
                                        <div class="preview-nav">
                                            <a href="#">Home</a>
                                            <a href="#">Videos</a>
                                            <a href="#">Live</a>
                                        </div>
                                    </div>
                                    <div class="preview-content">
                                        <div class="preview-card">
                                            <h3>Sample Content Card</h3>
                                            <p>This is how your content will look with the current color scheme.</p>
                                            <button class="preview-button">Primary</button>
                                            <button class="preview-button secondary">Secondary</button>
                                            <button class="preview-button success">Success</button>
                                            <button class="preview-button warning">Warning</button>
                                            <button class="preview-button danger">Danger</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Color History -->
                            <div class="color-panel">
                                <div class="panel-title">📚 Recent Colors</div>
                                <div id="colorHistory">
                                    <!-- Recent colors will appear here -->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Hidden CSRF token -->
    <input type="hidden" id="csrfToken" value="<?php echo VSecurity::generateCSRFToken('branding_studio'); ?>">
    
    <script>
        // Global variables
        let colorPickers = {};
        let colorHistory = JSON.parse(localStorage.getItem('colorHistory') || '[]');
        let currentChanges = {};
        
        // Initialize the studio
        document.addEventListener('DOMContentLoaded', function() {
            initializeColorPickers();
            loadColorHistory();
            setupEventListeners();
            updateLivePreview();
        });
        
        // Initialize color pickers for all color swatches
        function initializeColorPickers() {
            document.querySelectorAll('.color-swatch').forEach(swatch => {
                const colorKey = swatch.dataset.colorKey;
                const currentColor = swatch.dataset.currentColor;
                
                const pickr = Pickr.create({
                    el: swatch,
                    theme: 'classic',
                    default: currentColor,
                    swatches: [
                        'rgba(244, 67, 54, 1)',
                        'rgba(233, 30, 99, 1)',
                        'rgba(156, 39, 176, 1)',
                        'rgba(103, 58, 183, 1)',
                        'rgba(63, 81, 181, 1)',
                        'rgba(33, 150, 243, 1)',
                        'rgba(3, 169, 244, 1)',
                        'rgba(0, 188, 212, 1)',
                        'rgba(0, 150, 136, 1)',
                        'rgba(76, 175, 80, 1)',
                        'rgba(139, 195, 74, 1)',
                        'rgba(205, 220, 57, 1)',
                        'rgba(255, 235, 59, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(255, 152, 0, 1)',
                        'rgba(255, 87, 34, 1)'
                    ],
                    components: {
                        preview: true,
                        opacity: true,
                        hue: true,
                        interaction: {
                            hex: true,
                            rgba: true,
                            hsla: true,
                            hsva: true,
                            cmyk: true,
                            input: true,
                            clear: false,
                            save: true
                        }
                    }
                });
                
                pickr.on('change', (color, source, instance) => {
                    const hexColor = color.toHEXA().toString();
                    updateColorValue(colorKey, hexColor);
                });
                
                pickr.on('save', (color, instance) => {
                    if (color) {
                        const hexColor = color.toHEXA().toString();
                        saveColorChange(colorKey, hexColor);
                        addToColorHistory(hexColor);
                    }
                    instance.hide();
                });
                
                colorPickers[colorKey] = pickr;
            });
        }
        
        // Update color value in real-time
        function updateColorValue(colorKey, hexColor) {
            // Update the swatch
            const swatch = document.querySelector(`[data-color-key="${colorKey}"]`);
            if (swatch) {
                swatch.style.backgroundColor = hexColor;
                swatch.classList.add('updating');
                setTimeout(() => swatch.classList.remove('updating'), 600);
            }
            
            // Update the color value display
            const valueDisplay = swatch.parentElement.querySelector('.color-value');
            if (valueDisplay) {
                valueDisplay.textContent = hexColor;
            }
            
            // Update CSS variables for live preview
            document.documentElement.style.setProperty(`--${colorKey.replace('_', '-')}`, hexColor);
            
            // Track changes
            currentChanges[colorKey] = hexColor;
        }
        
        // Save color change to server
        function saveColorChange(colorKey, hexColor) {
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    ajax_action: 'update_color',
                    key: colorKey,
                    value: hexColor,
                    csrf_token: document.getElementById('csrfToken').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Color updated successfully!', 'success');
                } else {
                    showNotification('Failed to update color: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Network error occurred', 'error');
            });
        }
        
        // Generate color palette
        function generatePalette() {
            const baseColor = document.getElementById('basePicker').style.backgroundColor || '#667eea';
            
            // Convert to hex if needed
            const hex = rgbToHex(baseColor) || baseColor;
            
            // Generate complementary colors
            const palette = generateColorVariations(hex);
            
            // Display generated palette
            const paletteContainer = document.getElementById('generatedPalette');
            paletteContainer.innerHTML = '';
            
            palette.forEach(color => {
                const colorDiv = document.createElement('div');
                colorDiv.className = 'palette-color';
                colorDiv.style.backgroundColor = color;
                colorDiv.title = color;
                colorDiv.onclick = () => {
                    // Copy to clipboard
                    navigator.clipboard.writeText(color);
                    showNotification(`Copied ${color} to clipboard!`, 'success');
                };
                paletteContainer.appendChild(colorDiv);
            });
        }
        
        // Generate color variations
        function generateColorVariations(baseHex) {
            const hsl = hexToHsl(baseHex);
            const variations = [];
            
            // Lighter variations
            for (let i = 20; i <= 80; i += 20) {
                variations.push(hslToHex(hsl.h, hsl.s, Math.min(hsl.l + i, 95)));
            }
            
            // Base color
            variations.push(baseHex);
            
            // Darker variations
            for (let i = 20; i <= 80; i += 20) {
                variations.push(hslToHex(hsl.h, hsl.s, Math.max(hsl.l - i, 5)));
            }
            
            return variations;
        }
        
        // Color conversion utilities
        function hexToHsl(hex) {
            const r = parseInt(hex.slice(1, 3), 16) / 255;
            const g = parseInt(hex.slice(3, 5), 16) / 255;
            const b = parseInt(hex.slice(5, 7), 16) / 255;
            
            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;
            
            if (max === min) {
                h = s = 0;
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }
            
            return { h: h * 360, s: s * 100, l: l * 100 };
        }
        
        function hslToHex(h, s, l) {
            h /= 360;
            s /= 100;
            l /= 100;
            
            const hue2rgb = (p, q, t) => {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1/6) return p + (q - p) * 6 * t;
                if (t < 1/2) return q;
                if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                return p;
            };
            
            const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            const p = 2 * l - q;
            const r = hue2rgb(p, q, h + 1/3);
            const g = hue2rgb(p, q, h);
            const b = hue2rgb(p, q, h - 1/3);
            
            const toHex = (c) => {
                const hex = Math.round(c * 255).toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            };
            
            return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
        }
        
        function rgbToHex(rgb) {
            const match = rgb.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
            if (!match) return null;
            
            const r = parseInt(match[1]);
            const g = parseInt(match[2]);
            const b = parseInt(match[3]);
            
            return `#${((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)}`;
        }
        
        // Color history management
        function addToColorHistory(color) {
            if (!colorHistory.includes(color)) {
                colorHistory.unshift(color);
                if (colorHistory.length > 20) {
                    colorHistory = colorHistory.slice(0, 20);
                }
                localStorage.setItem('colorHistory', JSON.stringify(colorHistory));
                loadColorHistory();
            }
        }
        
        function loadColorHistory() {
            const historyContainer = document.getElementById('colorHistory');
            if (!historyContainer) return;
            
            historyContainer.innerHTML = '';
            
            colorHistory.forEach(color => {
                const colorDiv = document.createElement('div');
                colorDiv.className = 'palette-color';
                colorDiv.style.backgroundColor = color;
                colorDiv.style.margin = '2px';
                colorDiv.style.display = 'inline-block';
                colorDiv.title = color;
                colorDiv.onclick = () => {
                    navigator.clipboard.writeText(color);
                    showNotification(`Copied ${color} to clipboard!`, 'success');
                };
                historyContainer.appendChild(colorDiv);
            });
        }
        
        // Update live preview
        function updateLivePreview() {
            // This function updates the CSS variables for the live preview
            Object.keys(currentChanges).forEach(key => {
                document.documentElement.style.setProperty(`--${key.replace('_', '-')}`, currentChanges[key]);
            });
        }
        
        // Event listeners
        function setupEventListeners() {
            // Base color picker for palette generator
            const basePicker = document.getElementById('basePicker');
            if (basePicker) {
                basePicker.addEventListener('click', function() {
                    const input = document.createElement('input');
                    input.type = 'color';
                    input.value = rgbToHex(this.style.backgroundColor) || '#667eea';
                    input.onchange = (e) => {
                        this.style.backgroundColor = e.target.value;
                        generatePalette();
                    };
                    input.click();
                });
            }
        }
        
        // Utility functions
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => notification.classList.add('show'), 100);
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 3000);
        }
        
        function saveChanges() {
            if (Object.keys(currentChanges).length === 0) {
                showNotification('No changes to save', 'error');
                return;
            }
            
            showNotification('Saving all changes...', 'success');
            // Changes are already saved individually, this is just for user feedback
            currentChanges = {};
        }
        
        function resetToDefaults() {
            if (confirm('Are you sure you want to reset all colors to default values? This cannot be undone.')) {
                location.reload();
            }
        }
        
        function previewChanges() {
            window.open('/test_branding_system.php', '_blank');
        }
    </script>
</body>
</html> 
               <?php elseif ($currentTab === 'images'): ?>
                    <div class="image-studio">
                        <div class="image-upload-section">
                            <div class="panel-title">📤 Upload New Images</div>
                            
                            <div class="upload-grid">
                                <?php foreach ($imagePresets as $preset): ?>
                                    <div class="upload-card <?php echo $preset['is_required'] ? 'required' : ''; ?>">
                                        <div class="upload-header">
                                            <h3><?php echo htmlspecialchars($preset['name']); ?></h3>
                                            <?php if ($preset['is_required']): ?>
                                                <span class="required-badge">Required</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="dimension-info">
                                            <div class="dimensions">
                                                <strong><?php echo $preset['width']; ?> × <?php echo $preset['height']; ?>px</strong>
                                                <?php if ($preset['aspect_ratio']): ?>
                                                    <span class="ratio">(<?php echo $preset['aspect_ratio']; ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="use-case"><?php echo htmlspecialchars($preset['use_case']); ?></div>
                                        </div>
                                        
                                        <div class="upload-area" 
                                             data-preset="<?php echo htmlspecialchars($preset['key']); ?>"
                                             data-width="<?php echo $preset['width']; ?>"
                                             data-height="<?php echo $preset['height']; ?>">
                                            <div class="upload-placeholder">
                                                <i>📁</i>
                                                <p>Drop image here or click to browse</p>
                                                <small>Recommended: <?php echo $preset['width']; ?>×<?php echo $preset['height']; ?>px</small>
                                            </div>
                                            <input type="file" class="file-input" accept="image/*" style="display: none;">
                                        </div>
                                        
                                        <div class="upload-description">
                                            <?php echo htmlspecialchars($preset['description']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="uploaded-images-section">
                            <div class="panel-title">🖼️ Current Images</div>
                            
                            <div class="images-grid">
                                <?php foreach ($uploadedImages as $image): ?>
                                    <div class="image-card">
                                        <div class="image-preview">
                                            <img src="<?php echo htmlspecialchars($image['path']); ?>" 
                                                 alt="<?php echo htmlspecialchars($image['original_name']); ?>">
                                            <div class="image-overlay">
                                                <button class="btn btn-small btn-primary" onclick="editImage(<?php echo $image['id']; ?>)">
                                                    ✏️ Edit
                                                </button>
                                                <button class="btn btn-small btn-danger" onclick="deleteImage(<?php echo $image['id']; ?>)">
                                                    🗑️ Delete
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="image-info">
                                            <div class="image-name"><?php echo htmlspecialchars($image['original_name']); ?></div>
                                            <div class="image-details">
                                                <?php echo $image['width']; ?>×<?php echo $image['height']; ?>px
                                                <span class="file-size"><?php echo number_format($image['size'] / 1024, 1); ?>KB</span>
                                            </div>
                                            <div class="image-type"><?php echo ucfirst($image['type']); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif ($currentTab === 'typography'): ?>
                    <div class="typography-studio">
                        <div class="font-selector">
                            <div class="panel-title">📝 Font Selection</div>
                            
                            <div class="font-grid">
                                <div class="font-group">
                                    <h3>Primary Font</h3>
                                    <select class="font-dropdown" data-setting="font_family_primary">
                                        <option value='"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'>System Default</option>
                                        <option value='"Inter", sans-serif'>Inter</option>
                                        <option value='"Poppins", sans-serif'>Poppins</option>
                                        <option value='"Montserrat", sans-serif'>Montserrat</option>
                                        <option value='"Open Sans", sans-serif'>Open Sans</option>
                                        <option value='"Lato", sans-serif'>Lato</option>
                                        <option value='"Roboto", sans-serif'>Roboto</option>
                                        <option value='"Source Sans Pro", sans-serif'>Source Sans Pro</option>
                                    </select>
                                    <div class="font-preview" style="font-family: <?php echo $branding->get('font_family_primary', 'Arial, sans-serif'); ?>">
                                        The quick brown fox jumps over the lazy dog
                                    </div>
                                </div>
                                
                                <div class="font-group">
                                    <h3>Secondary Font (Headings)</h3>
                                    <select class="font-dropdown" data-setting="font_family_secondary">
                                        <option value='Georgia, "Times New Roman", serif'>Georgia</option>
                                        <option value='"Playfair Display", serif'>Playfair Display</option>
                                        <option value='"Merriweather", serif'>Merriweather</option>
                                        <option value='"Crimson Text", serif'>Crimson Text</option>
                                        <option value='"Libre Baskerville", serif'>Libre Baskerville</option>
                                    </select>
                                    <div class="font-preview" style="font-family: <?php echo $branding->get('font_family_secondary', 'Georgia, serif'); ?>">
                                        Beautiful Typography Matters
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="font-sizes">
                            <div class="panel-title">📏 Font Sizes</div>
                            
                            <div class="size-controls">
                                <div class="size-control">
                                    <label>Base Font Size</label>
                                    <input type="range" min="12" max="24" value="<?php echo $branding->get('font_size_base', 16); ?>" 
                                           data-setting="font_size_base" class="size-slider">
                                    <span class="size-value"><?php echo $branding->get('font_size_base', 16); ?>px</span>
                                </div>
                                
                                <div class="size-control">
                                    <label>Small Font Size</label>
                                    <input type="range" min="10" max="18" value="<?php echo $branding->get('font_size_small', 14); ?>" 
                                           data-setting="font_size_small" class="size-slider">
                                    <span class="size-value"><?php echo $branding->get('font_size_small', 14); ?>px</span>
                                </div>
                                
                                <div class="size-control">
                                    <label>Large Font Size</label>
                                    <input type="range" min="16" max="32" value="<?php echo $branding->get('font_size_large', 18); ?>" 
                                           data-setting="font_size_large" class="size-slider">
                                    <span class="size-value"><?php echo $branding->get('font_size_large', 18); ?>px</span>
                                </div>
                                
                                <div class="size-control">
                                    <label>Line Height</label>
                                    <input type="range" min="1" max="2" step="0.1" value="<?php echo $branding->get('line_height_base', 1.5); ?>" 
                                           data-setting="line_height_base" class="size-slider">
                                    <span class="size-value"><?php echo $branding->get('line_height_base', 1.5); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="typography-preview">
                            <div class="panel-title">👁️ Typography Preview</div>
                            
                            <div class="preview-content" id="typographyPreview">
                                <h1>Heading 1 - Main Title</h1>
                                <h2>Heading 2 - Section Title</h2>
                                <h3>Heading 3 - Subsection</h3>
                                <p>This is a paragraph of body text that demonstrates how your chosen typography will look in practice. It includes <strong>bold text</strong>, <em>italic text</em>, and <a href="#">linked text</a> to show the complete typography system.</p>
                                <p class="small-text">This is small text used for captions, metadata, and secondary information.</p>
                                <p class="large-text">This is large text used for emphasis and important callouts.</p>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif ($currentTab === 'presets'): ?>
                    <div class="presets-studio">
                        <div class="preset-gallery">
                            <?php foreach ($presets as $preset): ?>
                                <div class="preset-card <?php echo $preset['is_default'] ? 'default-preset' : ''; ?>">
                                    <div class="preset-preview" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <?php if ($preset['is_default']): ?>
                                            <div class="default-badge">Default</div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="preset-info">
                                        <div class="preset-name"><?php echo htmlspecialchars($preset['name']); ?></div>
                                        <div class="preset-description"><?php echo htmlspecialchars($preset['description']); ?></div>
                                        
                                        <div class="preset-colors">
                                            <div class="preset-color" style="background: #667eea;"></div>
                                            <div class="preset-color" style="background: #48bb78;"></div>
                                            <div class="preset-color" style="background: #ed8936;"></div>
                                            <div class="preset-color" style="background: #f56565;"></div>
                                            <div class="preset-color" style="background: #38b2ac;"></div>
                                        </div>
                                        
                                        <div class="preset-actions">
                                            <button class="btn btn-primary btn-small" onclick="applyPreset('<?php echo htmlspecialchars($preset['name']); ?>')">
                                                Apply Theme
                                            </button>
                                            <button class="btn btn-secondary btn-small" onclick="previewPreset('<?php echo htmlspecialchars($preset['name']); ?>')">
                                                Preview
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="create-preset-section">
                            <div class="panel-title">💾 Create Custom Preset</div>
                            
                            <form class="preset-form" onsubmit="saveCustomPreset(event)">
                                <div class="form-group">
                                    <label>Preset Name</label>
                                    <input type="text" name="preset_name" required placeholder="My Custom Theme">
                                </div>
                                
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="preset_description" placeholder="A beautiful custom theme for my platform"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Include Settings</label>
                                    <div class="settings-checklist">
                                        <label><input type="checkbox" name="include_colors" checked> Colors</label>
                                        <label><input type="checkbox" name="include_typography" checked> Typography</label>
                                        <label><input type="checkbox" name="include_layout"> Layout</label>
                                        <label><input type="checkbox" name="include_buttons"> Buttons</label>
                                        <label><input type="checkbox" name="include_badges"> Badges</label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-success">Save Preset</button>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($currentTab === 'privacy'): ?>
                    <div class="privacy-studio">
                        <div class="privacy-sections">
                            <!-- Site Access Control -->
                            <div class="privacy-section">
                                <div class="section-header">
                                    <h2>🌐 Site Access Control</h2>
                                    <p>Control who can access your platform</p>
                                </div>
                                
                                <div class="privacy-grid">
                                    <div class="privacy-card">
                                        <h3>Access Mode</h3>
                                        <select class="privacy-select" data-setting="site_access_mode">
                                            <option value="public" <?php echo $branding->get('site_access_mode') === 'public' ? 'selected' : ''; ?>>
                                                🌍 Public - Anyone can access
                                            </option>
                                            <option value="members_only" <?php echo $branding->get('site_access_mode') === 'members_only' ? 'selected' : ''; ?>>
                                                👥 Members Only - Registered users only
                                            </option>
                                            <option value="invite_only" <?php echo $branding->get('site_access_mode') === 'invite_only' ? 'selected' : ''; ?>>
                                                📧 Invite Only - Invitation required
                                            </option>
                                        </select>
                                        <p class="setting-description">Choose who can access your platform</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Guest Browsing</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="allow_guest_browsing" 
                                                   <?php echo $branding->get('allow_guest_browsing', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Allow non-registered users to browse public content</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Content Previews</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="show_content_previews" 
                                                   <?php echo $branding->get('show_content_previews', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Show content previews to non-logged-in users</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Login Required</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="require_login_to_view" 
                                                   <?php echo $branding->get('require_login_to_view', false) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Require login to view any content</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Content Privacy -->
                            <div class="privacy-section">
                                <div class="section-header">
                                    <h2>📹 Content Privacy</h2>
                                    <p>Set default privacy levels for content</p>
                                </div>
                                
                                <div class="privacy-grid">
                                    <div class="privacy-card">
                                        <h3>Default Video Privacy</h3>
                                        <select class="privacy-select" data-setting="default_video_privacy">
                                            <option value="public" <?php echo $branding->get('default_video_privacy') === 'public' ? 'selected' : ''; ?>>
                                                🌍 Public
                                            </option>
                                            <option value="members_only" <?php echo $branding->get('default_video_privacy') === 'members_only' ? 'selected' : ''; ?>>
                                                👥 Members Only
                                            </option>
                                            <option value="private" <?php echo $branding->get('default_video_privacy') === 'private' ? 'selected' : ''; ?>>
                                                🔒 Private
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Default Stream Privacy</h3>
                                        <select class="privacy-select" data-setting="default_stream_privacy">
                                            <option value="public" <?php echo $branding->get('default_stream_privacy') === 'public' ? 'selected' : ''; ?>>
                                                🌍 Public
                                            </option>
                                            <option value="members_only" <?php echo $branding->get('default_stream_privacy') === 'members_only' ? 'selected' : ''; ?>>
                                                👥 Members Only
                                            </option>
                                            <option value="private" <?php echo $branding->get('default_stream_privacy') === 'private' ? 'selected' : ''; ?>>
                                                🔒 Private
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Private Content</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="allow_private_content" 
                                                   <?php echo $branding->get('allow_private_content', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Allow users to create private content</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Unlisted Content</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="allow_unlisted_content" 
                                                   <?php echo $branding->get('allow_unlisted_content', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Allow users to create unlisted content</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Privacy -->
                            <div class="privacy-section">
                                <div class="section-header">
                                    <h2>👤 User Privacy</h2>
                                    <p>Control user profile and activity visibility</p>
                                </div>
                                
                                <div class="privacy-grid">
                                    <div class="privacy-card">
                                        <h3>User Profiles</h3>
                                        <select class="privacy-select" data-setting="show_user_profiles">
                                            <option value="public" <?php echo $branding->get('show_user_profiles') === 'public' ? 'selected' : ''; ?>>
                                                🌍 Public
                                            </option>
                                            <option value="members_only" <?php echo $branding->get('show_user_profiles') === 'members_only' ? 'selected' : ''; ?>>
                                                👥 Members Only
                                            </option>
                                            <option value="private" <?php echo $branding->get('show_user_profiles') === 'private' ? 'selected' : ''; ?>>
                                                🔒 Private
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>User Activity</h3>
                                        <select class="privacy-select" data-setting="show_user_activity">
                                            <option value="public" <?php echo $branding->get('show_user_activity') === 'public' ? 'selected' : ''; ?>>
                                                🌍 Public
                                            </option>
                                            <option value="members_only" <?php echo $branding->get('show_user_activity') === 'members_only' ? 'selected' : ''; ?>>
                                                👥 Members Only
                                            </option>
                                            <option value="private" <?php echo $branding->get('show_user_activity') === 'private' ? 'selected' : ''; ?>>
                                                🔒 Private
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>User Following</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="allow_user_following" 
                                                   <?php echo $branding->get('allow_user_following', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Allow users to follow other users</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Feature Access -->
                            <div class="privacy-section">
                                <div class="section-header">
                                    <h2>🔧 Feature Access</h2>
                                    <p>Control access to platform features</p>
                                </div>
                                
                                <div class="privacy-grid">
                                    <div class="privacy-card">
                                        <h3>Search</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="search_requires_login" 
                                                   <?php echo $branding->get('search_requires_login', false) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Require login to use search</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Trending</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="trending_requires_login" 
                                                   <?php echo $branding->get('trending_requires_login', false) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Require login to view trending</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Categories</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="categories_require_login" 
                                                   <?php echo $branding->get('categories_require_login', false) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Require login to browse categories</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Registration Settings -->
                            <div class="privacy-section">
                                <div class="section-header">
                                    <h2>📝 Registration & Accounts</h2>
                                    <p>Control user registration and account creation</p>
                                </div>
                                
                                <div class="privacy-grid">
                                    <div class="privacy-card">
                                        <h3>Public Registration</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="allow_public_registration" 
                                                   <?php echo $branding->get('allow_public_registration', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Allow anyone to register</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Email Verification</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="require_email_verification" 
                                                   <?php echo $branding->get('require_email_verification', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Require email verification</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Admin Approval</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="require_admin_approval" 
                                                   <?php echo $branding->get('require_admin_approval', false) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Require admin approval for accounts</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Invite System</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="enable_invite_system" 
                                                   <?php echo $branding->get('enable_invite_system', false) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Enable invite-only registration</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- GDPR Compliance -->
                            <div class="privacy-section">
                                <div class="section-header">
                                    <h2>⚖️ GDPR & Compliance</h2>
                                    <p>Privacy compliance and data protection</p>
                                </div>
                                
                                <div class="privacy-grid">
                                    <div class="privacy-card">
                                        <h3>GDPR Compliance</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="enable_gdpr_compliance" 
                                                   <?php echo $branding->get('enable_gdpr_compliance', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Enable GDPR compliance features</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Cookie Consent</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="require_cookie_consent" 
                                                   <?php echo $branding->get('require_cookie_consent', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Show cookie consent banner</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Data Export</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="enable_data_export" 
                                                   <?php echo $branding->get('enable_data_export', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Allow users to export their data</p>
                                    </div>
                                    
                                    <div class="privacy-card">
                                        <h3>Account Deletion</h3>
                                        <label class="toggle-switch">
                                            <input type="checkbox" data-setting="enable_account_deletion" 
                                                   <?php echo $branding->get('enable_account_deletion', true) ? 'checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="setting-description">Allow users to delete accounts</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Custom Messages -->
                            <div class="privacy-section">
                                <div class="section-header">
                                    <h2>💬 Custom Messages</h2>
                                    <p>Customize privacy-related messages</p>
                                </div>
                                
                                <div class="message-grid">
                                    <div class="message-card">
                                        <label>Login Required Message</label>
                                        <textarea class="message-input" data-setting="login_required_message" rows="2"><?php echo htmlspecialchars($branding->get('login_required_message', 'Please log in to view this content.')); ?></textarea>
                                    </div>
                                    
                                    <div class="message-card">
                                        <label>Members Only Message</label>
                                        <textarea class="message-input" data-setting="members_only_message" rows="2"><?php echo htmlspecialchars($branding->get('members_only_message', 'This content is available to members only.')); ?></textarea>
                                    </div>
                                    
                                    <div class="message-card">
                                        <label>Private Content Message</label>
                                        <textarea class="message-input" data-setting="private_content_message" rows="2"><?php echo htmlspecialchars($branding->get('private_content_message', 'This content is private.')); ?></textarea>
                                    </div>
                                    
                                    <div class="message-card">
                                        <label>Maintenance Mode Message</label>
                                        <textarea class="message-input" data-setting="maintenance_message" rows="3"><?php echo htmlspecialchars($branding->get('maintenance_message', 'Site is currently under maintenance. Please check back later.')); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Privacy Preview -->
                        <div class="privacy-preview">
                            <div class="panel-title">👁️ Privacy Preview</div>
                            
                            <div class="preview-modes">
                                <button class="preview-mode-btn active" data-mode="guest">Guest View</button>
                                <button class="preview-mode-btn" data-mode="member">Member View</button>
                                <button class="preview-mode-btn" data-mode="admin">Admin View</button>
                            </div>
                            
                            <div class="preview-content" id="privacyPreview">
                                <div class="preview-site">
                                    <div class="preview-header">
                                        <div class="preview-logo"><?php echo htmlspecialchars($siteInfo['name']); ?></div>
                                        <div class="preview-user-status" id="previewUserStatus">
                                            👤 Guest User
                                        </div>
                                    </div>
                                    
                                    <div class="preview-content-area">
                                        <div class="preview-video-card">
                                            <div class="video-thumbnail">🎬</div>
                                            <div class="video-info">
                                                <h4>Sample Video</h4>
                                                <p class="access-status" id="videoAccessStatus">✅ Accessible</p>
                                            </div>
                                        </div>
                                        
                                        <div class="preview-features">
                                            <div class="feature-item">
                                                <span>🔍 Search</span>
                                                <span class="feature-status" id="searchStatus">✅ Available</span>
                                            </div>
                                            <div class="feature-item">
                                                <span>📈 Trending</span>
                                                <span class="feature-status" id="trendingStatus">✅ Available</span>
                                            </div>
                                            <div class="feature-item">
                                                <span>📂 Categories</span>
                                                <span class="feature-status" id="categoriesStatus">✅ Available</span>
                                            </div>
                                            <div class="feature-item">
                                                <span>📤 Upload</span>
                                                <span class="feature-status" id="uploadStatus">❌ Login Required</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Additional CSS for new components -->
    <style>
        /* Image Studio Styles */
        .image-studio {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .upload-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .upload-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 2px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .upload-card.required {
            border-color: #f56565;
        }
        
        .upload-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .upload-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .upload-header h3 {
            margin: 0;
            color: #2d3748;
        }
        
        .required-badge {
            background: #f56565;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .dimension-info {
            margin-bottom: 15px;
        }
        
        .dimensions {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        
        .ratio {
            color: #718096;
            font-size: 0.9rem;
        }
        
        .use-case {
            color: #4a5568;
            font-size: 0.9rem;
        }
        
        .upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 15px;
        }
        
        .upload-area:hover {
            border-color: #667eea;
            background: #f7fafc;
        }
        
        .upload-placeholder i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .upload-placeholder p {
            margin: 0 0 5px 0;
            font-weight: 500;
        }
        
        .upload-placeholder small {
            color: #718096;
        }
        
        .upload-description {
            font-size: 0.85rem;
            color: #718096;
            line-height: 1.4;
        }
        
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .image-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        
        .image-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .image-preview {
            position: relative;
            height: 150px;
            overflow: hidden;
        }
        
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .image-preview:hover .image-overlay {
            opacity: 1;
        }
        
        .image-info {
            padding: 15px;
        }
        
        .image-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2d3748;
        }
        
        .image-details {
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 5px;
        }
        
        .file-size {
            margin-left: 10px;
        }
        
        .image-type {
            font-size: 0.8rem;
            color: #a0aec0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Typography Studio Styles */
        .typography-studio {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .font-grid {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .font-group {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .font-group h3 {
            margin: 0 0 15px 0;
            color: #2d3748;
        }
        
        .font-dropdown {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        
        .font-preview {
            padding: 15px;
            background: #f7fafc;
            border-radius: 6px;
            font-size: 1.1rem;
            border: 1px solid #e2e8f0;
        }
        
        .font-sizes {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .size-controls {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .size-control {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .size-control label {
            min-width: 120px;
            font-weight: 500;
        }
        
        .size-slider {
            flex: 1;
            height: 6px;
            border-radius: 3px;
            background: #e2e8f0;
            outline: none;
        }
        
        .size-value {
            min-width: 50px;
            text-align: right;
            font-weight: 600;
            color: #667eea;
        }
        
        .typography-preview {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            grid-column: 1 / -1;
        }
        
        #typographyPreview h1 {
            font-family: var(--font-family-secondary);
            font-size: calc(var(--font-size-base) * 2);
            margin-bottom: 15px;
        }
        
        #typographyPreview h2 {
            font-family: var(--font-family-secondary);
            font-size: calc(var(--font-size-base) * 1.5);
            margin-bottom: 12px;
        }
        
        #typographyPreview h3 {
            font-family: var(--font-family-secondary);
            font-size: calc(var(--font-size-base) * 1.25);
            margin-bottom: 10px;
        }
        
        #typographyPreview p {
            font-family: var(--font-family-primary);
            font-size: var(--font-size-base);
            line-height: var(--line-height-base);
            margin-bottom: 15px;
        }
        
        #typographyPreview .small-text {
            font-size: var(--font-size-small);
        }
        
        #typographyPreview .large-text {
            font-size: var(--font-size-large);
        }
        
        /* Presets Studio Styles */
        .presets-studio {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .default-preset {
            border: 2px solid #667eea;
        }
        
        .default-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .create-preset-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            height: fit-content;
        }
        
        .preset-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #2d3748;
        }
        
        .form-group input,
        .form-group textarea {
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .settings-checklist {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .settings-checklist label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: normal;
        }
        
        /* Privacy Studio Styles */
        .privacy-studio {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .privacy-sections {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .privacy-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .section-header {
            margin-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
        }
        
        .section-header h2 {
            margin: 0 0 8px 0;
            color: #2d3748;
            font-size: 1.3rem;
        }
        
        .section-header p {
            margin: 0;
            color: #718096;
            font-size: 0.95rem;
        }
        
        .privacy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .privacy-card {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.2s ease;
        }
        
        .privacy-card:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }
        
        .privacy-card h3 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 1rem;
        }
        
        .privacy-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .privacy-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .setting-description {
            margin: 0;
            font-size: 0.85rem;
            color: #718096;
            line-height: 1.4;
        }
        
        /* Toggle Switch Styles */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            margin-bottom: 10px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e0;
            transition: 0.3s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .toggle-switch input:checked + .toggle-slider {
            background-color: #667eea;
        }
        
        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        /* Message Grid */
        .message-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .message-card {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
        }
        
        .message-card label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .message-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.9rem;
            font-family: inherit;
            resize: vertical;
        }
        
        .message-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* Privacy Preview */
        .privacy-preview {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        
        .preview-modes {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            background: #f7fafc;
            padding: 4px;
            border-radius: 8px;
        }
        
        .preview-mode-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            background: transparent;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #718096;
        }
        
        .preview-mode-btn.active {
            background: #667eea;
            color: white;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        }
        
        .preview-site {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }
        
        .preview-header {
            background: #f7fafc;
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .preview-logo {
            font-weight: 700;
            color: #667eea;
        }
        
        .preview-user-status {
            font-size: 0.85rem;
            color: #718096;
        }
        
        .preview-content-area {
            padding: 20px;
        }
        
        .preview-video-card {
            display: flex;
            gap: 15px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .video-thumbnail {
            width: 60px;
            height: 40px;
            background: #e2e8f0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .video-info h4 {
            margin: 0 0 5px 0;
            font-size: 0.9rem;
        }
        
        .access-status {
            margin: 0;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .preview-features {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .feature-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: #f7fafc;
            border-radius: 6px;
            font-size: 0.85rem;
        }
        
        .feature-status {
            font-weight: 500;
        }
        
        .feature-status.available {
            color: #48bb78;
        }
        
        .feature-status.restricted {
            color: #f56565;
        }
        
        /* Responsive Privacy Studio */
        @media (max-width: 1200px) {
            .privacy-studio {
                grid-template-columns: 1fr;
            }
            
            .privacy-preview {
                position: static;
            }
        }
        
        @media (max-width: 768px) {
            .privacy-grid {
                grid-template-columns: 1fr;
            }
            
            .message-grid {
                grid-template-columns: 1fr;
            }
            
            .preview-modes {
                flex-direction: column;
            }
        }
    </style>
    
    <script>
        // Additional JavaScript for new components
        
        // Image upload functionality
        document.addEventListener('DOMContentLoaded', function() {
            setupImageUploads();
            setupTypographyControls();
        });
        
        function setupImageUploads() {
            document.querySelectorAll('.upload-area').forEach(area => {
                const fileInput = area.querySelector('.file-input');
                
                area.addEventListener('click', () => fileInput.click());
                
                area.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    area.style.borderColor = '#667eea';
                    area.style.background = '#f7fafc';
                });
                
                area.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    area.style.borderColor = '#cbd5e0';
                    area.style.background = 'transparent';
                });
                
                area.addEventListener('drop', (e) => {
                    e.preventDefault();
                    area.style.borderColor = '#cbd5e0';
                    area.style.background = 'transparent';
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleImageUpload(files[0], area.dataset.preset);
                    }
                });
                
                fileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        handleImageUpload(e.target.files[0], area.dataset.preset);
                    }
                });
            });
        }
        
        function handleImageUpload(file, presetKey) {
            const formData = new FormData();
            formData.append('image_file', file);
            formData.append('image_key', presetKey);
            formData.append('preset_key', presetKey);
            formData.append('action', 'upload_image');
            formData.append('csrf_token', document.getElementById('csrfToken').value);
            
            showNotification('Uploading image...', 'success');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('successfully')) {
                    showNotification('Image uploaded successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Upload failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Upload failed', 'error');
            });
        }
        
        function setupTypographyControls() {
            // Font dropdown changes
            document.querySelectorAll('.font-dropdown').forEach(dropdown => {
                dropdown.addEventListener('change', function() {
                    const setting = this.dataset.setting;
                    const value = this.value;
                    
                    // Update preview
                    const preview = this.parentElement.querySelector('.font-preview');
                    if (preview) {
                        preview.style.fontFamily = value;
                    }
                    
                    // Update CSS variable
                    document.documentElement.style.setProperty(`--${setting.replace('_', '-')}`, value);
                    
                    // Save to server
                    saveSetting(setting, value, 'text');
                });
            });
            
            // Font size sliders
            document.querySelectorAll('.size-slider').forEach(slider => {
                slider.addEventListener('input', function() {
                    const setting = this.dataset.setting;
                    const value = this.value;
                    
                    // Update display
                    const valueDisplay = this.parentElement.querySelector('.size-value');
                    if (valueDisplay) {
                        valueDisplay.textContent = setting.includes('line_height') ? value : value + 'px';
                    }
                    
                    // Update CSS variable
                    const cssValue = setting.includes('line_height') ? value : value + 'px';
                    document.documentElement.style.setProperty(`--${setting.replace('_', '-')}`, cssValue);
                    
                    // Save to server (debounced)
                    clearTimeout(this.saveTimeout);
                    this.saveTimeout = setTimeout(() => {
                        saveSetting(setting, value, 'number');
                    }, 500);
                });
            });
        }
        
        function saveSetting(key, value, type) {
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    ajax_action: 'update_setting',
                    key: key,
                    value: value,
                    type: type,
                    csrf_token: document.getElementById('csrfToken').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to save setting:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        function applyPreset(presetName) {
            if (confirm(`Apply the "${presetName}" theme? This will override your current settings.`)) {
                const formData = new FormData();
                formData.append('action', 'apply_preset');
                formData.append('preset_name', presetName);
                formData.append('csrf_token', document.getElementById('csrfToken').value);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    showNotification(`Applied "${presetName}" theme!`, 'success');
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to apply preset', 'error');
                });
            }
        }
        
        function previewPreset(presetName) {
            window.open(`/test_branding_system.php?preset=${encodeURIComponent(presetName)}`, '_blank');
        }
        
        function saveCustomPreset(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const settings = {};
            
            // Collect current settings based on checkboxes
            if (formData.get('include_colors')) {
                // Add color settings
                Object.keys(currentChanges).forEach(key => {
                    if (key.includes('color_')) {
                        settings[key] = currentChanges[key];
                    }
                });
            }
            
            formData.append('ajax_action', 'save_custom_preset');
            formData.append('name', formData.get('preset_name'));
            formData.append('description', formData.get('preset_description'));
            formData.append('settings', JSON.stringify(settings));
            formData.append('csrf_token', document.getElementById('csrfToken').value);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Custom preset saved!', 'success');
                    event.target.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Failed to save preset: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to save preset', 'error');
            });
        }
        
        function editImage(imageId) {
            // Implement image editing functionality
            showNotification('Image editing coming soon!', 'success');
        }
        
        function deleteImage(imageId) {
            if (confirm('Are you sure you want to delete this image? This cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete_image');
                formData.append('image_id', imageId);
                formData.append('csrf_token', document.getElementById('csrfToken').value);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('successfully')) {
                        showNotification('Image deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification('Failed to delete image', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to delete image', 'error');
                });
            }
        }
        
        // Privacy management functionality
        document.addEventListener('DOMContentLoaded', function() {
            setupPrivacyControls();
        });
        
        function setupPrivacyControls() {
            // Privacy select dropdowns
            document.querySelectorAll('.privacy-select').forEach(select => {
                select.addEventListener('change', function() {
                    const setting = this.dataset.setting;
                    const value = this.value;
                    
                    saveSetting(setting, value, 'text');
                    updatePrivacyPreview();
                });
            });
            
            // Toggle switches
            document.querySelectorAll('.toggle-switch input').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const setting = this.dataset.setting;
                    const value = this.checked ? '1' : '0';
                    
                    saveSetting(setting, value, 'boolean');
                    updatePrivacyPreview();
                });
            });
            
            // Message inputs
            document.querySelectorAll('.message-input').forEach(input => {
                input.addEventListener('blur', function() {
                    const setting = this.dataset.setting;
                    const value = this.value;
                    
                    saveSetting(setting, value, 'text');
                });
            });
            
            // Preview mode buttons
            document.querySelectorAll('.preview-mode-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.preview-mode-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    updatePrivacyPreview();
                });
            });
            
            // Initial preview update
            updatePrivacyPreview();
        }
        
        function updatePrivacyPreview() {
            const activeMode = document.querySelector('.preview-mode-btn.active').dataset.mode;
            const userStatus = document.getElementById('previewUserStatus');
            const videoAccess = document.getElementById('videoAccessStatus');
            const searchStatus = document.getElementById('searchStatus');
            const trendingStatus = document.getElementById('trendingStatus');
            const categoriesStatus = document.getElementById('categoriesStatus');
            const uploadStatus = document.getElementById('uploadStatus');
            
            // Update user status
            const statusMap = {
                'guest': '👤 Guest User',
                'member': '👥 Member User',
                'admin': '👑 Admin User'
            };
            userStatus.textContent = statusMap[activeMode];
            
            // Get current settings
            const siteAccessMode = getSettingValue('site_access_mode', 'public');
            const defaultVideoPrivacy = getSettingValue('default_video_privacy', 'public');
            const searchRequiresLogin = getSettingValue('search_requires_login', false);
            const trendingRequiresLogin = getSettingValue('trending_requires_login', false);
            const categoriesRequireLogin = getSettingValue('categories_require_login', false);
            
            // Update video access
            let videoAccessible = true;
            let videoMessage = '✅ Accessible';
            
            if (siteAccessMode === 'members_only' && activeMode === 'guest') {
                videoAccessible = false;
                videoMessage = '❌ Members Only';
            } else if (defaultVideoPrivacy === 'members_only' && activeMode === 'guest') {
                videoAccessible = false;
                videoMessage = '❌ Login Required';
            } else if (defaultVideoPrivacy === 'private') {
                videoAccessible = false;
                videoMessage = '🔒 Private';
            }
            
            videoAccess.textContent = videoMessage;
            videoAccess.className = 'access-status ' + (videoAccessible ? 'available' : 'restricted');
            
            // Update feature access
            updateFeatureStatus(searchStatus, searchRequiresLogin, activeMode);
            updateFeatureStatus(trendingStatus, trendingRequiresLogin, activeMode);
            updateFeatureStatus(categoriesStatus, categoriesRequireLogin, activeMode);
            
            // Upload always requires login
            updateFeatureStatus(uploadStatus, true, activeMode);
        }
        
        function updateFeatureStatus(element, requiresLogin, userMode) {
            let accessible = true;
            let message = '✅ Available';
            
            if (requiresLogin && userMode === 'guest') {
                accessible = false;
                message = '❌ Login Required';
            }
            
            element.textContent = message;
            element.className = 'feature-status ' + (accessible ? 'available' : 'restricted');
        }
        
        function getSettingValue(settingKey, defaultValue) {
            const element = document.querySelector(`[data-setting="${settingKey}"]`);
            if (!element) return defaultValue;
            
            if (element.type === 'checkbox') {
                return element.checked;
            } else {
                return element.value;
            }
        }
    </script>
</body>
</html>