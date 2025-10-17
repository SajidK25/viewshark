<?php
/**
 * Test page for the EasyStream Branding System
 * This page demonstrates all the branding features and customization options
 */

define('_ISVALID', true);

try {
    // Load core system
    include_once 'f_core/config.core.php';
    include_once 'f_core/f_functions/functions.branding.php';
    
    // Get branding instance
    $branding = getBranding();
    $siteInfo = siteInfo();
    
    // Sample data for demonstration
    $sampleVideo = [
        'title' => 'Amazing Video Content',
        'author' => 'Content Creator',
        'views' => 12500,
        'date' => '2 days ago',
        'thumbnail' => '/f_scripts/fe/img/default-thumbnail.jpg',
        'badges' => ['verified', 'premium']
    ];
    
    $sampleUser = [
        'username' => 'johndoe',
        'avatar' => '/f_scripts/fe/img/default-avatar.png',
        'verified' => true,
        'premium' => true
    ];
    
} catch (Exception $e) {
    die("Error loading branding system: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branding System Test - <?php echo htmlspecialchars($siteInfo['name']); ?></title>
    
    <?php echo brandingMetaTags(); ?>
    <?php echo dynamicCSSLink(); ?>
    <?php echo inlineCSSVariables(); ?>
    
    <style>
        /* Additional test styles */
        .test-container {
            max-width: var(--layout-max-width);
            margin: 0 auto;
            padding: var(--layout-spacing-large);
        }
        
        .test-section {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--layout-border-radius);
            padding: var(--layout-spacing-large);
            margin-bottom: var(--layout-spacing-large);
        }
        
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--layout-spacing-medium);
        }
        
        .color-palette {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: var(--layout-spacing-small);
        }
        
        .color-swatch {
            text-align: center;
            padding: var(--layout-spacing-small);
            border-radius: var(--layout-border-radius);
            color: white;
            font-size: var(--font-size-small);
            font-weight: 600;
        }
        
        .badge-showcase {
            display: flex;
            gap: var(--layout-spacing-small);
            flex-wrap: wrap;
            align-items: center;
        }
        
        .button-showcase {
            display: flex;
            gap: var(--layout-spacing-small);
            flex-wrap: wrap;
            align-items: center;
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--layout-spacing-medium);
        }
        
        .video-card {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--layout-border-radius);
            overflow: hidden;
            transition: transform var(--animation-duration) ease;
        }
        
        .video-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px var(--color-shadow);
        }
        
        .video-thumbnail {
            position: relative;
            width: 100%;
            height: 140px;
            background: var(--color-bg-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .video-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .video-badges {
            position: absolute;
            top: var(--layout-spacing-small);
            right: var(--layout-spacing-small);
            display: flex;
            gap: 4px;
        }
        
        .video-info {
            padding: var(--layout-spacing-medium);
        }
        
        .video-title {
            margin: 0 0 var(--layout-spacing-small) 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--color-text-primary);
        }
        
        .video-author {
            margin: 0 0 var(--layout-spacing-small) 0;
            color: var(--color-text-secondary);
            font-size: var(--font-size-small);
        }
        
        .video-stats {
            display: flex;
            justify-content: space-between;
            font-size: var(--font-size-small);
            color: var(--color-text-muted);
        }
        
        .user-avatar {
            display: inline-flex;
            align-items: center;
            gap: var(--layout-spacing-small);
        }
        
        .user-avatar img {
            border-radius: 50%;
            border: 2px solid var(--color-border);
        }
        
        .notification {
            padding: var(--layout-spacing-medium);
            border-radius: var(--layout-border-radius);
            margin-bottom: var(--layout-spacing-medium);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .notification-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .notification-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .notification-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .notification-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.7;
        }
        
        .notification-close:hover {
            opacity: 1;
        }
        
        .form-input {
            width: 100%;
            padding: var(--button-padding-y) var(--button-padding-x);
            border: 1px solid var(--color-border);
            border-radius: var(--button-border-radius);
            font-size: var(--font-size-base);
            transition: border-color var(--animation-duration) ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        .theme-switcher {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .theme-toggle {
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all var(--animation-duration) ease;
        }
        
        .theme-toggle:hover {
            background: var(--color-primary-dark);
            transform: scale(1.1);
        }
        
        /* Dark theme styles */
        .dark-theme {
            --color-bg-main: #121212;
            --color-bg-secondary: #1e1e1e;
            --color-bg-card: #2d2d2d;
            --color-text-primary: #ffffff;
            --color-text-secondary: #b3b3b3;
            --color-text-muted: #888888;
            --color-border: #404040;
        }
    </style>
</head>
<body>
    <?php echo themeSwitcher(); ?>
    
    <?php echo brandedHeader(); ?>
    
    <div class="test-container">
        <div class="test-section">
            <h1>🎨 EasyStream Branding System Test</h1>
            <p>This page demonstrates the comprehensive branding and customization system.</p>
            
            <?php echo notification('Branding system loaded successfully!', 'success'); ?>
            <?php echo notification('You can customize every aspect of the platform.', 'info'); ?>
        </div>
        
        <div class="test-section">
            <h2>Site Information</h2>
            <div class="test-grid">
                <div>
                    <h3>Current Settings</h3>
                    <ul>
                        <li><strong>Site Name:</strong> <?php echo htmlspecialchars($siteInfo['name']); ?></li>
                        <li><strong>Tagline:</strong> <?php echo htmlspecialchars($siteInfo['tagline']); ?></li>
                        <li><strong>Description:</strong> <?php echo htmlspecialchars($siteInfo['description']); ?></li>
                        <li><strong>Primary Color:</strong> <?php echo brandingGet('color_primary'); ?></li>
                        <li><strong>Font Family:</strong> <?php echo brandingGet('font_family_primary'); ?></li>
                    </ul>
                </div>
                <div>
                    <h3>Logo Display</h3>
                    <div style="text-align: center; padding: 20px; background: var(--color-bg-secondary); border-radius: var(--layout-border-radius);">
                        <?php echo siteLogo('main', 'test-logo', 'Main Logo'); ?>
                        <p>Main Logo</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="test-section">
            <h2>Color Palette</h2>
            <div class="color-palette">
                <div class="color-swatch" style="background-color: <?php echo brandingGet('color_primary'); ?>;">
                    Primary<br><?php echo brandingGet('color_primary'); ?>
                </div>
                <div class="color-swatch" style="background-color: <?php echo brandingGet('color_success'); ?>;">
                    Success<br><?php echo brandingGet('color_success'); ?>
                </div>
                <div class="color-swatch" style="background-color: <?php echo brandingGet('color_warning'); ?>; color: #000;">
                    Warning<br><?php echo brandingGet('color_warning'); ?>
                </div>
                <div class="color-swatch" style="background-color: <?php echo brandingGet('color_danger'); ?>;">
                    Danger<br><?php echo brandingGet('color_danger'); ?>
                </div>
                <div class="color-swatch" style="background-color: <?php echo brandingGet('color_info'); ?>;">
                    Info<br><?php echo brandingGet('color_info'); ?>
                </div>
                <div class="color-swatch" style="background-color: <?php echo brandingGet('color_secondary'); ?>;">
                    Secondary<br><?php echo brandingGet('color_secondary'); ?>
                </div>
            </div>
        </div>
        
        <div class="test-section">
            <h2>Badges</h2>
            <div class="badge-showcase">
                <?php echo badge('verified'); ?>
                <?php echo badge('premium'); ?>
                <?php echo badge('live'); ?>
                <?php echo badge('new'); ?>
                <?php echo badge('verified', '✓ Custom Text'); ?>
            </div>
        </div>
        
        <div class="test-section">
            <h2>Buttons</h2>
            <div class="button-showcase">
                <?php echo button('Primary Button', 'primary'); ?>
                <?php echo button('Secondary Button', 'secondary'); ?>
                <?php echo button('Success Button', 'success'); ?>
                <?php echo button('Warning Button', 'warning'); ?>
                <?php echo button('Danger Button', 'danger'); ?>
                <?php echo button('Info Button', 'info'); ?>
                <?php echo button('Link Button', 'primary', '/test'); ?>
            </div>
        </div>
        
        <div class="test-section">
            <h2>Video Cards</h2>
            <div class="video-grid">
                <?php echo videoCard($sampleVideo); ?>
                <?php echo videoCard(array_merge($sampleVideo, ['title' => 'Another Great Video', 'badges' => ['live']])); ?>
                <?php echo videoCard(array_merge($sampleVideo, ['title' => 'New Upload', 'badges' => ['new']])); ?>
            </div>
        </div>
        
        <div class="test-section">
            <h2>User Avatars</h2>
            <div style="display: flex; gap: 20px; align-items: center;">
                <?php echo userAvatar($sampleUser, 'small'); ?>
                <?php echo userAvatar($sampleUser, 'medium'); ?>
                <?php echo userAvatar($sampleUser, 'large'); ?>
            </div>
        </div>
        
        <div class="test-section">
            <h2>Form Elements</h2>
            <div class="test-grid">
                <div>
                    <label>Text Input:</label>
                    <?php echo formInput('test_text', 'text', 'Sample text', ['placeholder' => 'Enter text here']); ?>
                </div>
                <div>
                    <label>Email Input:</label>
                    <?php echo formInput('test_email', 'email', '', ['placeholder' => 'Enter email']); ?>
                </div>
            </div>
        </div>
        
        <div class="test-section">
            <h2>Notifications</h2>
            <?php echo notification('This is a success message!', 'success'); ?>
            <?php echo notification('This is an error message!', 'error'); ?>
            <?php echo notification('This is a warning message!', 'warning'); ?>
            <?php echo notification('This is an info message!', 'info'); ?>
        </div>
        
        <div class="test-section">
            <h2>Admin Links</h2>
            <p>Customize your branding:</p>
            <div class="button-showcase">
                <?php echo button('Branding Management', 'primary', '/f_modules/m_backend/branding_management.php'); ?>
                <?php echo button('Download CSS', 'info', '/dynamic_theme.php'); ?>
                <?php echo button('View Database', 'secondary', '#'); ?>
            </div>
        </div>
    </div>
    
    <?php echo brandedFooter(); ?>
    
    <script>
        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Color swatch click to copy color
            document.querySelectorAll('.color-swatch').forEach(swatch => {
                swatch.addEventListener('click', function() {
                    const color = this.textContent.match(/#[0-9a-fA-F]{6}/);
                    if (color) {
                        navigator.clipboard.writeText(color[0]).then(() => {
                            alert('Color copied: ' + color[0]);
                        });
                    }
                });
            });
            
            // Video card hover effects
            document.querySelectorAll('.video-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>