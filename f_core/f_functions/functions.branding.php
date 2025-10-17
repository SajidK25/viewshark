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
 * Branding Helper Functions
 * These functions provide easy access to branding features in templates and modules
 */

/**
 * Get branding instance
 */
function getBranding() {
    return VBranding::getInstance();
}

/**
 * Get a branding setting value
 */
function brandingGet($key, $default = null) {
    return getBranding()->get($key, $default);
}

/**
 * Generate site logo HTML
 */
function siteLogo($type = 'main', $class = '', $alt = null) {
    return getBranding()->getLogo($type, $class, $alt);
}

/**
 * Generate badge HTML
 */
function badge($type, $text = null, $class = '') {
    return getBranding()->getBadge($type, $text, $class);
}

/**
 * Get site information array
 */
function siteInfo() {
    return getBranding()->getSiteInfo();
}

/**
 * Generate dynamic CSS link tag
 */
function dynamicCSSLink() {
    $timestamp = time(); // For cache busting
    return "<link rel=\"stylesheet\" href=\"/dynamic_theme.php?v=$timestamp\" id=\"dynamic-theme\">";
}

/**
 * Generate inline CSS variables for quick access
 */
function inlineCSSVariables() {
    $branding = getBranding();
    
    $css = "<style id=\"branding-vars\">\n:root {\n";
    $css .= "  --color-primary: " . $branding->get('color_primary', '#007bff') . ";\n";
    $css .= "  --color-primary-dark: " . $branding->get('color_primary_dark', '#0056b3') . ";\n";
    $css .= "  --color-bg-main: " . $branding->get('color_bg_main', '#ffffff') . ";\n";
    $css .= "  --color-text-primary: " . $branding->get('color_text_primary', '#212529') . ";\n";
    $css .= "  --font-family-primary: " . $branding->get('font_family_primary', 'Arial, sans-serif') . ";\n";
    $css .= "  --layout-max-width: " . $branding->get('layout_max_width', 1200) . "px;\n";
    $css .= "}\n</style>\n";
    
    return $css;
}

/**
 * Generate meta tags for branding
 */
function brandingMetaTags() {
    $siteInfo = siteInfo();
    
    $meta = "<meta name=\"theme-color\" content=\"" . brandingGet('color_primary', '#007bff') . "\">\n";
    $meta .= "<meta name=\"description\" content=\"" . htmlspecialchars($siteInfo['description']) . "\">\n";
    $meta .= "<meta property=\"og:site_name\" content=\"" . htmlspecialchars($siteInfo['name']) . "\">\n";
    $meta .= "<meta property=\"og:description\" content=\"" . htmlspecialchars($siteInfo['description']) . "\">\n";
    $meta .= "<link rel=\"icon\" href=\"" . htmlspecialchars($siteInfo['favicon']) . "\">\n";
    
    return $meta;
}

/**
 * Generate header HTML with branding
 */
function brandedHeader($includeNav = true) {
    $siteInfo = siteInfo();
    $branding = getBranding();
    
    $html = "<header class=\"site-header\" style=\"background-color: " . $branding->get('color_bg_header', '#ffffff') . ";\">\n";
    $html .= "  <div class=\"container\">\n";
    $html .= "    <div class=\"header-content\">\n";
    $html .= "      <div class=\"logo-section\">\n";
    $html .= "        " . siteLogo('main', 'main-logo') . "\n";
    $html .= "        <span class=\"site-name\">" . htmlspecialchars($siteInfo['name']) . "</span>\n";
    $html .= "      </div>\n";
    
    if ($includeNav) {
        $html .= "      <nav class=\"main-nav\">\n";
        $html .= "        <a href=\"/\" class=\"nav-link\">Home</a>\n";
        $html .= "        <a href=\"/videos\" class=\"nav-link\">Videos</a>\n";
        $html .= "        <a href=\"/live\" class=\"nav-link\">Live</a>\n";
        $html .= "        <a href=\"/upload\" class=\"nav-link btn btn-primary\">Upload</a>\n";
        $html .= "      </nav>\n";
    }
    
    $html .= "    </div>\n";
    $html .= "  </div>\n";
    $html .= "</header>\n";
    
    return $html;
}

/**
 * Generate footer HTML with branding
 */
function brandedFooter() {
    $siteInfo = siteInfo();
    $branding = getBranding();
    
    $html = "<footer class=\"site-footer\" style=\"background-color: " . $branding->get('color_bg_footer', '#343a40') . "; color: " . $branding->get('color_text_light', '#ffffff') . ";\">\n";
    $html .= "  <div class=\"container\">\n";
    $html .= "    <div class=\"footer-content\">\n";
    $html .= "      <div class=\"footer-brand\">\n";
    $html .= "        " . siteLogo('small', 'footer-logo') . "\n";
    $html .= "        <p>" . htmlspecialchars($siteInfo['tagline']) . "</p>\n";
    $html .= "      </div>\n";
    $html .= "      <div class=\"footer-links\">\n";
    $html .= "        <a href=\"/about\">About</a>\n";
    $html .= "        <a href=\"/privacy\">Privacy</a>\n";
    $html .= "        <a href=\"/terms\">Terms</a>\n";
    $html .= "        <a href=\"/contact\">Contact</a>\n";
    $html .= "      </div>\n";
    $html .= "    </div>\n";
    $html .= "    <div class=\"footer-bottom\">\n";
    $html .= "      <p>" . htmlspecialchars($siteInfo['footer_text']) . "</p>\n";
    $html .= "    </div>\n";
    $html .= "  </div>\n";
    $html .= "</footer>\n";
    
    return $html;
}

/**
 * Generate video card HTML with branding
 */
function videoCard($video, $showBadges = true) {
    $branding = getBranding();
    
    $html = "<div class=\"video-card\">\n";
    $html .= "  <div class=\"video-thumbnail\">\n";
    $html .= "    <img src=\"" . htmlspecialchars($video['thumbnail'] ?? brandingGet('default_thumbnail')) . "\" alt=\"" . htmlspecialchars($video['title']) . "\">\n";
    
    if ($showBadges && isset($video['badges'])) {
        $html .= "    <div class=\"video-badges\">\n";
        foreach ($video['badges'] as $badgeType) {
            $html .= "      " . badge($badgeType) . "\n";
        }
        $html .= "    </div>\n";
    }
    
    $html .= "  </div>\n";
    $html .= "  <div class=\"video-info\">\n";
    $html .= "    <h3 class=\"video-title\">" . htmlspecialchars($video['title']) . "</h3>\n";
    $html .= "    <p class=\"video-author\">" . htmlspecialchars($video['author'] ?? 'Unknown') . "</p>\n";
    $html .= "    <div class=\"video-stats\">\n";
    $html .= "      <span class=\"views\">" . number_format($video['views'] ?? 0) . " views</span>\n";
    $html .= "      <span class=\"date\">" . htmlspecialchars($video['date'] ?? '') . "</span>\n";
    $html .= "    </div>\n";
    $html .= "  </div>\n";
    $html .= "</div>\n";
    
    return $html;
}

/**
 * Generate user avatar with branding
 */
function userAvatar($user, $size = 'medium', $showBadges = true) {
    $branding = getBranding();
    $sizes = ['small' => 32, 'medium' => 48, 'large' => 64];
    $pixelSize = $sizes[$size] ?? 48;
    
    $avatar = $user['avatar'] ?? brandingGet('default_avatar');
    
    $html = "<div class=\"user-avatar user-avatar-$size\">\n";
    $html .= "  <img src=\"" . htmlspecialchars($avatar) . "\" alt=\"" . htmlspecialchars($user['username'] ?? 'User') . "\" width=\"$pixelSize\" height=\"$pixelSize\">\n";
    
    if ($showBadges && isset($user['verified']) && $user['verified']) {
        $html .= "  " . badge('verified') . "\n";
    }
    
    if ($showBadges && isset($user['premium']) && $user['premium']) {
        $html .= "  " . badge('premium') . "\n";
    }
    
    $html .= "</div>\n";
    
    return $html;
}

/**
 * Generate notification HTML with branding
 */
function notification($message, $type = 'info', $dismissible = true) {
    $typeClasses = [
        'success' => 'notification-success',
        'error' => 'notification-error',
        'warning' => 'notification-warning',
        'info' => 'notification-info'
    ];
    
    $class = $typeClasses[$type] ?? 'notification-info';
    
    $html = "<div class=\"notification $class\">\n";
    $html .= "  <div class=\"notification-content\">\n";
    $html .= "    " . htmlspecialchars($message) . "\n";
    $html .= "  </div>\n";
    
    if ($dismissible) {
        $html .= "  <button class=\"notification-close\" onclick=\"this.parentElement.remove()\">&times;</button>\n";
    }
    
    $html .= "</div>\n";
    
    return $html;
}

/**
 * Generate button HTML with branding
 */
function button($text, $type = 'primary', $href = null, $attributes = []) {
    $tag = $href ? 'a' : 'button';
    $class = "btn btn-$type";
    
    if (isset($attributes['class'])) {
        $class .= ' ' . $attributes['class'];
        unset($attributes['class']);
    }
    
    $attrs = '';
    foreach ($attributes as $key => $value) {
        $attrs .= " $key=\"" . htmlspecialchars($value) . "\"";
    }
    
    if ($href) {
        $attrs .= " href=\"" . htmlspecialchars($href) . "\"";
    }
    
    return "<$tag class=\"$class\"$attrs>" . htmlspecialchars($text) . "</$tag>";
}

/**
 * Generate form input with branding
 */
function formInput($name, $type = 'text', $value = '', $attributes = []) {
    $class = 'form-input';
    
    if (isset($attributes['class'])) {
        $class .= ' ' . $attributes['class'];
        unset($attributes['class']);
    }
    
    $attrs = "class=\"$class\" name=\"" . htmlspecialchars($name) . "\" type=\"$type\"";
    
    if ($value) {
        $attrs .= " value=\"" . htmlspecialchars($value) . "\"";
    }
    
    foreach ($attributes as $key => $val) {
        $attrs .= " $key=\"" . htmlspecialchars($val) . "\"";
    }
    
    return "<input $attrs>";
}

/**
 * Check if dark mode is enabled
 */
function isDarkMode() {
    return brandingGet('enable_dark_mode', true);
}

/**
 * Get current theme preset name
 */
function getCurrentTheme() {
    // This could be enhanced to track the current active theme
    return brandingGet('current_theme', 'Default Blue');
}

/**
 * Generate theme switcher HTML
 */
function themeSwitcher() {
    if (!isDarkMode()) {
        return '';
    }
    
    $html = "<div class=\"theme-switcher\">\n";
    $html .= "  <button class=\"theme-toggle\" onclick=\"toggleTheme()\" title=\"Toggle Dark Mode\">\n";
    $html .= "    <span class=\"theme-icon\">🌙</span>\n";
    $html .= "  </button>\n";
    $html .= "</div>\n";
    
    $html .= "<script>\n";
    $html .= "function toggleTheme() {\n";
    $html .= "  document.body.classList.toggle('dark-theme');\n";
    $html .= "  const icon = document.querySelector('.theme-icon');\n";
    $html .= "  icon.textContent = document.body.classList.contains('dark-theme') ? '☀️' : '🌙';\n";
    $html .= "  localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');\n";
    $html .= "}\n";
    $html .= "// Load saved theme\n";
    $html .= "if (localStorage.getItem('theme') === 'dark') {\n";
    $html .= "  document.body.classList.add('dark-theme');\n";
    $html .= "  document.querySelector('.theme-icon').textContent = '☀️';\n";
    $html .= "}\n";
    $html .= "</script>\n";
    
    return $html;
}
?>