<?php
/**
 * Directory Setup Script
 * 
 * This script ensures all required writable directories exist
 * with proper permissions for the PuninarLogistic application.
 * 
 * Run this script after deploying to a new environment.
 */

// Define required directories
$requiredDirectories = [
    'writable',
    'writable/cache',
    'writable/cache/assets',
    'writable/logs',
    'writable/session',
    'writable/uploads',
    'writable/uploads/qrcodes',
    'writable/uploads/delivery_photos',
    'writable/debugbar',
    'writable/backups',
    'writable/validation_reports',
    'writable/uat_reports',
    'writable/complete_validation'
];

// Get the project root directory
$projectRoot = __DIR__;

echo "=== PuninarLogistic Directory Setup ===\n";
echo "Project Root: {$projectRoot}\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$created = 0;
$existed = 0;
$errors = 0;

foreach ($requiredDirectories as $dir) {
    $fullPath = $projectRoot . DIRECTORY_SEPARATOR . $dir;
    
    if (!is_dir($fullPath)) {
        // Create directory with proper permissions
        if (mkdir($fullPath, 0755, true)) {
            echo "✓ Created: {$dir}\n";
            $created++;
        } else {
            echo "✗ Failed to create: {$dir}\n";
            $errors++;
        }
    } else {
        echo "- Already exists: {$dir}\n";
        $existed++;
        
        // Check and fix permissions if needed
        $currentPerms = fileperms($fullPath) & 0777;
        if ($currentPerms !== 0755) {
            if (chmod($fullPath, 0755)) {
                echo "  ✓ Fixed permissions for: {$dir}\n";
            } else {
                echo "  ✗ Failed to fix permissions for: {$dir}\n";
                $errors++;
            }
        }
    }
}

// Create .htaccess files to protect writable directories
$htaccessContent = "Deny from all\n";
$htaccessDirs = [
    'writable',
    'writable/cache',
    'writable/logs',
    'writable/session',
    'writable/backups'
];

echo "\nCreating .htaccess protection files...\n";
foreach ($htaccessDirs as $dir) {
    $htaccessPath = $projectRoot . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . '.htaccess';
    $dirPath = $projectRoot . DIRECTORY_SEPARATOR . $dir;
    
    if (is_dir($dirPath)) {
        if (!file_exists($htaccessPath)) {
            if (file_put_contents($htaccessPath, $htaccessContent)) {
                echo "✓ Created .htaccess in: {$dir}\n";
            } else {
                echo "✗ Failed to create .htaccess in: {$dir}\n";
                $errors++;
            }
        }
    }
}

// Create index.html files for additional protection
$indexContent = "<!DOCTYPE html>\n<html><head><title>403 Forbidden</title></head><body><h1>Directory access is forbidden.</h1></body></html>\n";
echo "\nCreating index.html protection files...\n";
foreach ($htaccessDirs as $dir) {
    $indexPath = $projectRoot . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'index.html';
    $dirPath = $projectRoot . DIRECTORY_SEPARATOR . $dir;
    
    if (is_dir($dirPath)) {
        if (!file_exists($indexPath)) {
            if (file_put_contents($indexPath, $indexContent)) {
                echo "✓ Created index.html in: {$dir}\n";
            } else {
                echo "✗ Failed to create index.html in: {$dir}\n";
                $errors++;
            }
        }
    }
}

echo "\n=== Setup Summary ===\n";
echo "Directories created: {$created}\n";
echo "Directories existed: {$existed}\n";
echo "Errors: {$errors}\n";

if ($errors === 0) {
    echo "\n✓ Directory setup completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Ensure your web server has read/write access to the writable/ directory\n";
    echo "2. Check that the database configuration is correct\n";
    echo "3. Import the database if not already done\n";
    echo "4. Test the application\n";
} else {
    echo "\n✗ Some errors occurred during setup.\n";
    echo "Please check file permissions and try again.\n";
    echo "You may need to run this script with elevated privileges.\n";
}

echo "\n=== Environment Information ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Operating System: " . PHP_OS . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' . "\n";
echo "Current User: " . get_current_user() . "\n";

// Check if running via web or CLI
if (php_sapi_name() === 'cli') {
    echo "Execution Mode: Command Line\n";
} else {
    echo "Execution Mode: Web Browser\n";
    echo "Web Server: " . $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' . "\n";
}

echo "\nSetup script completed.\n";
?>