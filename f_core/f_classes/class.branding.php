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
 * VBranding - Comprehensive Branding and Customization System
 * 
 * This class handles all aspects of platform branding including:
 * - Colors, fonts, and styling
 * - Logos and images
 * - Layout settings
 * - Badge configurations
 * - Theme presets
 * - Dynamic CSS generation
 * - Cache management
 */
class VBranding
{
    private static $instance = null;
    private $settings = [];
    private $cache_enabled = true;
    private $cache_duration = 3600; // 1 hour
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->loadSettings();
    }
    
    /**
     * Load all branding settings from database
     */
    private function loadSettings()
    {
        global $db;
        
        try {
            $sql = "SELECT setting_key, setting_value, setting_type FROM db_branding_settings ORDER BY category, setting_key";
            $result = $db->Execute($sql);
            
            if ($result) {
                while (!$result->EOF) {
                    $key = $result->fields['setting_key'];
                    $value = $result->fields['setting_value'];
                    $type = $result->fields['setting_type'];
                    
                    // Convert value based on type
                    $this->settings[$key] = $this->convertValue($value, $type);
                    $result->MoveNext();
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to load branding settings: ' . $e->getMessage());
            $this->loadDefaultSettings();
        }
    }
    
    /**
     * Load default settings if database fails
     */
    private function loadDefaultSettings()
    {
        $this->settings = [
            'site_name' => 'EasyStream',
            'site_tagline' => 'Your Video Streaming Platform',
            'color_primary' => '#007bff',
            'color_primary_dark' => '#0056b3',
            'color_primary_light' => '#66b3ff',
            'color_bg_main' => '#ffffff',
            'color_text_primary' => '#212529',
            'logo_main' => '/f_scripts/fe/img/logo-header-blue.svg',
            'font_family_primary' => '"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'
        ];
    }
    
    /**
     * Convert setting value based on type
     */
    private function convertValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'number':
                return is_numeric($value) ? (float) $value : 0;
            case 'json':
                return json_decode($value, true) ?: [];
            default:
                return $value;
        }
    }
    
    /**
     * Get a branding setting value
     */
    public function get($key, $default = null)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }
    
    /**
     * Set a branding setting value
     */
    public function set($key, $value, $type = 'text')
    {
        global $db;
        
        try {
            // Update in memory
            $this->settings[$key] = $this->convertValue($value, $type);
            
            // Update in database
            $sql = "INSERT INTO db_branding_settings (setting_key, setting_value, setting_type) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()";
            $result = $db->Execute($sql, [$key, $value, $type, $value]);
            
            if ($result) {
                // Clear CSS cache
                $this->clearCache('css');
                return true;
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to set branding setting: ' . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Get multiple settings by category
     */
    public function getByCategory($category)
    {
        global $db;
        $settings = [];
        
        try {
            $sql = "SELECT setting_key, setting_value, setting_type, description, default_value 
                    FROM db_branding_settings 
                    WHERE category = ? 
                    ORDER BY setting_key";
            $result = $db->Execute($sql, [$category]);
            
            if ($result) {
                while (!$result->EOF) {
                    $key = $result->fields['setting_key'];
                    $settings[$key] = [
                        'value' => $this->convertValue($result->fields['setting_value'], $result->fields['setting_type']),
                        'type' => $result->fields['setting_type'],
                        'description' => $result->fields['description'],
                        'default' => $result->fields['default_value']
                    ];
                    $result->MoveNext();
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get settings by category: ' . $e->getMessage());
        }
        
        return $settings;
    }
    
    /**
     * Get all categories
     */
    public function getCategories()
    {
        global $db;
        $categories = [];
        
        try {
            $sql = "SELECT DISTINCT category FROM db_branding_settings ORDER BY category";
            $result = $db->Execute($sql);
            
            if ($result) {
                while (!$result->EOF) {
                    $categories[] = $result->fields['category'];
                    $result->MoveNext();
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get categories: ' . $e->getMessage());
        }
        
        return $categories;
    }
    
    /**
     * Generate dynamic CSS based on current settings
     */
    public function generateCSS()
    {
        // Check cache first
        if ($this->cache_enabled) {
            $cached = $this->getCache('dynamic_css');
            if ($cached) {
                return $cached;
            }
        }
        
        $css = $this->buildCSS();
        
        // Cache the result
        if ($this->cache_enabled) {
            $this->setCache('dynamic_css', $css, $this->cache_duration);
        }
        
        return $css;
    }
    
    /**
     * Build the actual CSS content
     */
    private function buildCSS()
    {
        $css = "/* EasyStream Dynamic CSS - Generated " . date('Y-m-d H:i:s') . " */\n\n";
        
        // CSS Variables (Custom Properties)
        $css .= ":root {\n";
        $css .= "  /* Primary Colors */\n";
        $css .= "  --color-primary: " . $this->get('color_primary', '#007bff') . ";\n";
        $css .= "  --color-primary-dark: " . $this->get('color_primary_dark', '#0056b3') . ";\n";
        $css .= "  --color-primary-light: " . $this->get('color_primary_light', '#66b3ff') . ";\n";
        $css .= "  --color-secondary: " . $this->get('color_secondary', '#6c757d') . ";\n";
        $css .= "  --color-success: " . $this->get('color_success', '#28a745') . ";\n";
        $css .= "  --color-warning: " . $this->get('color_warning', '#ffc107') . ";\n";
        $css .= "  --color-danger: " . $this->get('color_danger', '#dc3545') . ";\n";
        $css .= "  --color-info: " . $this->get('color_info', '#17a2b8') . ";\n\n";
        
        $css .= "  /* Background Colors */\n";
        $css .= "  --color-bg-main: " . $this->get('color_bg_main', '#ffffff') . ";\n";
        $css .= "  --color-bg-secondary: " . $this->get('color_bg_secondary', '#f8f9fa') . ";\n";
        $css .= "  --color-bg-dark: " . $this->get('color_bg_dark', '#343a40') . ";\n";
        $css .= "  --color-bg-card: " . $this->get('color_bg_card', '#ffffff') . ";\n";
        $css .= "  --color-bg-header: " . $this->get('color_bg_header', '#ffffff') . ";\n";
        $css .= "  --color-bg-footer: " . $this->get('color_bg_footer', '#343a40') . ";\n\n";
        
        $css .= "  /* Text Colors */\n";
        $css .= "  --color-text-primary: " . $this->get('color_text_primary', '#212529') . ";\n";
        $css .= "  --color-text-secondary: " . $this->get('color_text_secondary', '#6c757d') . ";\n";
        $css .= "  --color-text-muted: " . $this->get('color_text_muted', '#868e96') . ";\n";
        $css .= "  --color-text-light: " . $this->get('color_text_light', '#ffffff') . ";\n";
        $css .= "  --color-text-link: " . $this->get('color_text_link', '#007bff') . ";\n";
        $css .= "  --color-text-link-hover: " . $this->get('color_text_link_hover', '#0056b3') . ";\n\n";
        
        $css .= "  /* Border and Shadow Colors */\n";
        $css .= "  --color-border: " . $this->get('color_border', '#dee2e6') . ";\n";
        $css .= "  --color-border-light: " . $this->get('color_border_light', '#e9ecef') . ";\n";
        $css .= "  --color-border-dark: " . $this->get('color_border_dark', '#adb5bd') . ";\n";
        $css .= "  --color-shadow: " . $this->get('color_shadow', 'rgba(0,0,0,0.1)') . ";\n\n";
        
        $css .= "  /* Typography */\n";
        $css .= "  --font-family-primary: " . $this->get('font_family_primary', '"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif') . ";\n";
        $css .= "  --font-family-secondary: " . $this->get('font_family_secondary', 'Georgia, "Times New Roman", serif') . ";\n";
        $css .= "  --font-size-base: " . $this->get('font_size_base', 16) . "px;\n";
        $css .= "  --font-size-small: " . $this->get('font_size_small', 14) . "px;\n";
        $css .= "  --font-size-large: " . $this->get('font_size_large', 18) . "px;\n";
        $css .= "  --line-height-base: " . $this->get('line_height_base', '1.5') . ";\n\n";
        
        $css .= "  /* Layout */\n";
        $css .= "  --layout-max-width: " . $this->get('layout_max_width', 1200) . "px;\n";
        $css .= "  --layout-sidebar-width: " . $this->get('layout_sidebar_width', 250) . "px;\n";
        $css .= "  --layout-header-height: " . $this->get('layout_header_height', 60) . "px;\n";
        $css .= "  --layout-footer-height: " . $this->get('layout_footer_height', 80) . "px;\n";
        $css .= "  --layout-border-radius: " . $this->get('layout_border_radius', 8) . "px;\n";
        $css .= "  --layout-spacing-small: " . $this->get('layout_spacing_small', 8) . "px;\n";
        $css .= "  --layout-spacing-medium: " . $this->get('layout_spacing_medium', 16) . "px;\n";
        $css .= "  --layout-spacing-large: " . $this->get('layout_spacing_large', 24) . "px;\n\n";
        
        $css .= "  /* Buttons */\n";
        $css .= "  --button-border-radius: " . $this->get('button_border_radius', 4) . "px;\n";
        $css .= "  --button-padding-x: " . $this->get('button_padding_x', 12) . "px;\n";
        $css .= "  --button-padding-y: " . $this->get('button_padding_y', 8) . "px;\n";
        $css .= "  --button-font-weight: " . $this->get('button_font_weight', 500) . ";\n\n";
        
        $css .= "  /* Badges */\n";
        $css .= "  --badge-border-radius: " . $this->get('badge_border_radius', 12) . "px;\n";
        $css .= "  --badge-font-size: " . $this->get('badge_font_size', 12) . "px;\n";
        $css .= "  --badge-padding-x: " . $this->get('badge_padding_x', 8) . "px;\n";
        $css .= "  --badge-padding-y: " . $this->get('badge_padding_y', 4) . "px;\n";
        $css .= "  --badge-verified-color: " . $this->get('badge_verified_color', '#28a745') . ";\n";
        $css .= "  --badge-premium-color: " . $this->get('badge_premium_color', '#ffc107') . ";\n";
        $css .= "  --badge-live-color: " . $this->get('badge_live_color', '#dc3545') . ";\n";
        $css .= "  --badge-new-color: " . $this->get('badge_new_color', '#17a2b8') . ";\n\n";
        
        $css .= "  /* Animation */\n";
        $css .= "  --animation-duration: " . $this->get('animation_duration', 300) . "ms;\n";
        $css .= "}\n\n";
        
        // Base styles using CSS variables
        $css .= $this->generateBaseStyles();
        
        // Component styles
        $css .= $this->generateComponentStyles();
        
        // Badge styles
        $css .= $this->generateBadgeStyles();
        
        // Button styles
        $css .= $this->generateButtonStyles();
        
        // Custom CSS
        $customCSS = $this->get('custom_css', '');
        if (!empty($customCSS)) {
            $css .= "\n/* Custom CSS */\n" . $customCSS . "\n";
        }
        
        return $css;
    }
    
    /**
     * Generate base styles
     */
    private function generateBaseStyles()
    {
        return "
/* Base Styles */
body {
    font-family: var(--font-family-primary);
    font-size: var(--font-size-base);
    line-height: var(--line-height-base);
    color: var(--color-text-primary);
    background-color: var(--color-bg-main);
    margin: 0;
    padding: 0;
}

.container {
    max-width: var(--layout-max-width);
    margin: 0 auto;
    padding: 0 var(--layout-spacing-medium);
}

.header {
    background-color: var(--color-bg-header);
    height: var(--layout-header-height);
    border-bottom: 1px solid var(--color-border);
    box-shadow: 0 2px 4px var(--color-shadow);
}

.footer {
    background-color: var(--color-bg-footer);
    color: var(--color-text-light);
    min-height: var(--layout-footer-height);
    padding: var(--layout-spacing-large) 0;
}

.card {
    background-color: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: var(--layout-border-radius);
    box-shadow: 0 2px 4px var(--color-shadow);
    padding: var(--layout-spacing-medium);
    margin-bottom: var(--layout-spacing-medium);
}

a {
    color: var(--color-text-link);
    text-decoration: none;
    transition: color var(--animation-duration) ease;
}

a:hover {
    color: var(--color-text-link-hover);
}

";
    }
    
    /**
     * Generate component styles
     */
    private function generateComponentStyles()
    {
        return "
/* Component Styles */
.video-player {
    border-radius: " . $this->get('player_border_radius', 8) . "px;
    background-color: " . $this->get('player_background_color', '#000000') . ";
    overflow: hidden;
}

.video-thumbnail {
    border-radius: var(--layout-border-radius);
    overflow: hidden;
    position: relative;
}

.video-thumbnail::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, var(--color-primary) 100%);
    opacity: 0;
    transition: opacity var(--animation-duration) ease;
}

.video-thumbnail:hover::after {
    opacity: 0.1;
}

.sidebar {
    width: var(--layout-sidebar-width);
    background-color: var(--color-bg-secondary);
    border-right: 1px solid var(--color-border);
    min-height: calc(100vh - var(--layout-header-height));
}

.main-content {
    margin-left: var(--layout-sidebar-width);
    padding: var(--layout-spacing-large);
}

";
    }
    
    /**
     * Generate badge styles
     */
    private function generateBadgeStyles()
    {
        return "
/* Badge Styles */
.badge {
    display: inline-block;
    padding: var(--badge-padding-y) var(--badge-padding-x);
    font-size: var(--badge-font-size);
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: var(--badge-border-radius);
    transition: all var(--animation-duration) ease;
}

.badge-verified {
    background-color: var(--badge-verified-color);
    color: white;
}

.badge-premium {
    background-color: var(--badge-premium-color);
    color: #212529;
}

.badge-live {
    background-color: var(--badge-live-color);
    color: white;
    animation: pulse 2s infinite;
}

.badge-new {
    background-color: var(--badge-new-color);
    color: white;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

";
    }
    
    /**
     * Generate button styles
     */
    private function generateButtonStyles()
    {
        return "
/* Button Styles */
.btn {
    display: inline-block;
    padding: var(--button-padding-y) var(--button-padding-x);
    font-size: var(--font-size-base);
    font-weight: var(--button-font-weight);
    line-height: 1.5;
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: var(--button-border-radius);
    transition: all var(--animation-duration) ease;
    user-select: none;
}

.btn-primary {
    color: white;
    background-color: var(--color-primary);
    border-color: var(--color-primary);
}

.btn-primary:hover {
    background-color: var(--color-primary-dark);
    border-color: var(--color-primary-dark);
}

.btn-secondary {
    color: white;
    background-color: var(--color-secondary);
    border-color: var(--color-secondary);
}

.btn-success {
    color: white;
    background-color: var(--color-success);
    border-color: var(--color-success);
}

.btn-warning {
    color: #212529;
    background-color: var(--color-warning);
    border-color: var(--color-warning);
}

.btn-danger {
    color: white;
    background-color: var(--color-danger);
    border-color: var(--color-danger);
}

.btn-info {
    color: white;
    background-color: var(--color-info);
    border-color: var(--color-info);
}

.btn-outline-primary {
    color: var(--color-primary);
    background-color: transparent;
    border-color: var(--color-primary);
}

.btn-outline-primary:hover {
    color: white;
    background-color: var(--color-primary);
}

";
    }
    
    /**
     * Apply a preset theme
     */
    public function applyPreset($presetName)
    {
        global $db;
        
        try {
            $sql = "SELECT preset_data FROM db_branding_presets WHERE preset_name = ?";
            $result = $db->Execute($sql, [$presetName]);
            
            if ($result && !$result->EOF) {
                $presetData = json_decode($result->fields['preset_data'], true);
                
                if ($presetData) {
                    foreach ($presetData as $key => $value) {
                        $this->set($key, $value);
                    }
                    return true;
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to apply preset: ' . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Save current settings as a preset
     */
    public function savePreset($name, $description, $settings)
    {
        global $db;
        
        try {
            $presetData = json_encode($settings);
            $sql = "INSERT INTO db_branding_presets (preset_name, preset_description, preset_data) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE preset_description = ?, preset_data = ?, updated_at = NOW()";
            $result = $db->Execute($sql, [$name, $description, $presetData, $description, $presetData]);
            
            return (bool) $result;
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to save preset: ' . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Get all available presets
     */
    public function getPresets()
    {
        global $db;
        $presets = [];
        
        try {
            $sql = "SELECT preset_name, preset_description, is_default, created_at 
                    FROM db_branding_presets 
                    ORDER BY is_default DESC, preset_name";
            $result = $db->Execute($sql);
            
            if ($result) {
                while (!$result->EOF) {
                    $presets[] = [
                        'name' => $result->fields['preset_name'],
                        'description' => $result->fields['preset_description'],
                        'is_default' => (bool) $result->fields['is_default'],
                        'created_at' => $result->fields['created_at']
                    ];
                    $result->MoveNext();
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get presets: ' . $e->getMessage());
        }
        
        return $presets;
    }
    
    /**
     * Cache management
     */
    private function getCache($key)
    {
        global $db;
        
        try {
            $sql = "SELECT cache_data FROM db_branding_cache 
                    WHERE cache_key = ? AND (expires_at IS NULL OR expires_at > NOW())";
            $result = $db->Execute($sql, [$key]);
            
            if ($result && !$result->EOF) {
                return $result->fields['cache_data'];
            }
        } catch (Exception $e) {
            // Fail silently for cache
        }
        
        return null;
    }
    
    private function setCache($key, $data, $duration = 3600)
    {
        global $db;
        
        try {
            $expiresAt = date('Y-m-d H:i:s', time() + $duration);
            $sql = "INSERT INTO db_branding_cache (cache_key, cache_data, expires_at) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE cache_data = ?, expires_at = ?";
            $db->Execute($sql, [$key, $data, $expiresAt, $data, $expiresAt]);
        } catch (Exception $e) {
            // Fail silently for cache
        }
    }
    
    private function clearCache($type = null)
    {
        global $db;
        
        try {
            if ($type) {
                $sql = "DELETE FROM db_branding_cache WHERE cache_type = ?";
                $db->Execute($sql, [$type]);
            } else {
                $sql = "DELETE FROM db_branding_cache";
                $db->Execute($sql);
            }
        } catch (Exception $e) {
            // Fail silently for cache
        }
    }
    
    /**
     * Generate logo HTML with proper branding
     */
    public function getLogo($type = 'main', $class = '', $alt = null)
    {
        $logoPath = $this->get("logo_$type", '/f_scripts/fe/img/logo-header-blue.svg');
        $siteName = $this->get('site_name', 'EasyStream');
        $altText = $alt ?: $siteName;
        
        return "<img src=\"$logoPath\" alt=\"$altText\" class=\"logo $class\">";
    }
    
    /**
     * Generate badge HTML
     */
    public function getBadge($type, $text = null, $class = '')
    {
        $badges = [
            'verified' => ['text' => '✓ Verified', 'class' => 'badge-verified'],
            'premium' => ['text' => '★ Premium', 'class' => 'badge-premium'],
            'live' => ['text' => '● LIVE', 'class' => 'badge-live'],
            'new' => ['text' => 'NEW', 'class' => 'badge-new']
        ];
        
        if (!isset($badges[$type])) {
            return '';
        }
        
        $badgeText = $text ?: $badges[$type]['text'];
        $badgeClass = $badges[$type]['class'];
        
        return "<span class=\"badge $badgeClass $class\">$badgeText</span>";
    }
    
    /**
     * Get site information for templates
     */
    public function getSiteInfo()
    {
        return [
            'name' => $this->get('site_name', 'EasyStream'),
            'tagline' => $this->get('site_tagline', 'Your Video Streaming Platform'),
            'description' => $this->get('site_description', 'A powerful video streaming platform'),
            'footer_text' => $this->get('footer_text', '© 2025 EasyStream. All rights reserved.'),
            'logo_main' => $this->get('logo_main', '/f_scripts/fe/img/logo-header-blue.svg'),
            'logo_small' => $this->get('logo_small', '/f_scripts/fe/img/logo-small.png'),
            'favicon' => $this->get('favicon', '/favicon.ico')
        ];
    }
}