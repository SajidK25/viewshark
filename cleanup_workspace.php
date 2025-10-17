<?php
/**
 * EasyStream Workspace Cleanup Script
 * This script organizes files and removes unnecessary debug/test files
 */

define('_ISVALID', true);

echo "🧹 EasyStream Workspace Cleanup\n";
echo "================================\n\n";

// Files to delete (debug/test files that are no longer needed)
$filesToDelete = [
    // Debug files
    'debug.php',
    'debug2.php', 
    'debug3.php',
    'debug4.php',
    'debug_database_error.php',
    'debug_404_final.php',
    'debug_parser.php',
    
    // Test files (keeping only the main test suite)
    'test_parser.php',
    'test_parser_clean.php',
    'test_parser_final.php',
    'test_parser_fixed.php',
    'test_parser_comprehensive.php',
    'test_routing.php',
    'test_rewrite.php',
    'test_fixed_routing.php',
    'test_core.php',
    'test_fixes_docker.php',
    
    // Fix files (temporary fixes that are now integrated)
    'fix_admin_panel_now.php',
    'fix_watch_routing.php',
    'fix_test_error.php',
    'fix_watch_url_parsing.php',
    'fix_404_now.php',
    'fix_routing_issues.php',
    'fix_php8_compatibility.php',
    'fix_home_page.php',
    
    // Diagnostic files
    'diagnose_404.php',
    'full_diagnostic.php',
    'monitor_parser_errors.php',
    'systematic_reconnection_plan.php',
    
    // Temporary files
    'quick_parser_test.php',
    'quick_setup.php',
    'simple_test.php',
    'simple_setup.php',
    'emergency_fix.php',
    'bypass_routing.php',
    'cleanup_urls.php',
    'configure_url.php',
    'working_index.php',
    'start_docker.php',
    
    // Old branding management (replaced by advanced panel)
    'f_modules/m_backend/branding_management.php'
];

// Files to move to archive folder
$filesToArchive = [
    'PARSER_ROADMAP.md',
    'ADMIN_PANEL_ROADMAP.md',
    'URL_CONFIGURATION.md',
    'create_missing_modules.php'
];

// Create archive directory
$archiveDir = 'archive';
if (!is_dir($archiveDir)) {
    mkdir($archiveDir, 0755, true);
    echo "✅ Created archive directory\n";
}

// Delete unnecessary files
$deletedCount = 0;
foreach ($filesToDelete as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "🗑️  Deleted: $file\n";
            $deletedCount++;
        } else {
            echo "❌ Failed to delete: $file\n";
        }
    }
}

// Archive files
$archivedCount = 0;
foreach ($filesToArchive as $file) {
    if (file_exists($file)) {
        $archivePath = $archiveDir . '/' . basename($file);
        if (rename($file, $archivePath)) {
            echo "📦 Archived: $file → $archivePath\n";
            $archivedCount++;
        } else {
            echo "❌ Failed to archive: $file\n";
        }
    }
}

echo "\n";

// Consolidate SQL files
echo "📋 Consolidating SQL files...\n";

$sqlFiles = [
    'deploy/create_missing_tables.sql',
    'deploy/create_branding_tables.sql', 
    'deploy/create_image_management_tables.sql',
    'deploy/create_privacy_settings.sql',
    'deploy/init_settings.sql'
];

$consolidatedSQL = "-- EasyStream Complete Database Setup\n";
$consolidatedSQL .= "-- Generated on " . date('Y-m-d H:i:s') . "\n";
$consolidatedSQL .= "-- This file contains all database tables and initial data\n\n";

foreach ($sqlFiles as $sqlFile) {
    if (file_exists($sqlFile)) {
        $consolidatedSQL .= "-- ============================================\n";
        $consolidatedSQL .= "-- " . strtoupper(basename($sqlFile, '.sql')) . "\n";
        $consolidatedSQL .= "-- ============================================\n\n";
        $consolidatedSQL .= file_get_contents($sqlFile) . "\n\n";
    }
}

file_put_contents('deploy/complete_database_setup.sql', $consolidatedSQL);
echo "✅ Created consolidated database setup: deploy/complete_database_setup.sql\n";

// Create organized file structure documentation
$fileStructure = "# EasyStream File Structure\n\n";
$fileStructure .= "## Core Files\n";
$fileStructure .= "- `index.php` - Main entry point\n";
$fileStructure .= "- `parser.php` - URL routing and parsing\n";
$fileStructure .= "- `dynamic_theme.php` - Dynamic CSS generation\n";
$fileStructure .= "- `docker-compose.yml` - Docker configuration\n\n";

$fileStructure .= "## Setup & Testing\n";
$fileStructure .= "- `setup.php` - Initial platform setup\n";
$fileStructure .= "- `test_complete_system.php` - Comprehensive test suite\n";
$fileStructure .= "- `test_branding_system.php` - Branding system demo\n";
$fileStructure .= "- `start_easystream.php` - Docker startup helper\n\n";

$fileStructure .= "## Core Classes (`f_core/f_classes/`)\n";
$fileStructure .= "- `class.branding.php` - Branding and theming system\n";
$fileStructure .= "- `class.imagemanager.php` - Image upload and management\n";
$fileStructure .= "- `class.privacy.php` - Privacy and access control\n";
$fileStructure .= "- `class.database.php` - Database operations\n";
$fileStructure .= "- `class.security.php` - Security and CSRF protection\n\n";

$fileStructure .= "## Admin Panels (`f_modules/m_backend/`)\n";
$fileStructure .= "- `advanced_branding_panel.php` - Professional branding studio\n";
$fileStructure .= "- `admin_dashboard.php` - Main admin dashboard\n";
$fileStructure .= "- `admin_direct.php` - Direct admin access\n\n";

$fileStructure .= "## Database Setup (`deploy/`)\n";
$fileStructure .= "- `complete_database_setup.sql` - All-in-one database setup\n";
$fileStructure .= "- Individual SQL files for modular setup\n\n";

$fileStructure .= "## Documentation\n";
$fileStructure .= "- `BRANDING_SYSTEM.md` - Complete branding documentation\n";
$fileStructure .= "- `DEPLOYMENT_READY.md` - Deployment guide\n";
$fileStructure .= "- `FIXES_COMPLETED.md` - Summary of completed fixes\n\n";

file_put_contents('FILE_STRUCTURE.md', $fileStructure);
echo "✅ Created file structure documentation: FILE_STRUCTURE.md\n";

// Summary
echo "\n📊 Cleanup Summary:\n";
echo "==================\n";
echo "🗑️  Files deleted: $deletedCount\n";
echo "📦 Files archived: $archivedCount\n";
echo "📋 SQL files consolidated: " . count($sqlFiles) . "\n";
echo "📁 Archive directory created\n";
echo "📖 Documentation updated\n";

echo "\n🎯 Next Steps:\n";
echo "==============\n";
echo "1. Run the complete test suite: test_complete_system.php\n";
echo "2. Set up the database: deploy/complete_database_setup.sql\n";
echo "3. Start Docker: docker-compose up -d\n";
echo "4. Access the branding studio: f_modules/m_backend/advanced_branding_panel.php\n";
echo "5. Configure your platform settings\n";

echo "\n✨ Workspace cleanup completed!\n";
?>