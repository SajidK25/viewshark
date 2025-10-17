<?php
/**
 * Dynamic Theme CSS Generator
 * Serves dynamically generated CSS based on branding settings
 */

define('_ISVALID', true);

// Set proper headers for CSS
header('Content-Type: text/css; charset=utf-8');
header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');

try {
    // Load core configuration
    include_once 'f_core/config.core.php';
    
    // Initialize branding system
    $branding = VBranding::getInstance();
    
    // Generate and output CSS
    echo $branding->generateCSS();
    
} catch (Exception $e) {
    // Fallback CSS in case of errors
    echo "/* Error generating dynamic CSS: " . $e->getMessage() . " */\n";
    echo ":root { --color-primary: #007bff; --color-bg-main: #ffffff; }\n";
    echo "body { font-family: Arial, sans-serif; color: #212529; background: #ffffff; }\n";
}
?>