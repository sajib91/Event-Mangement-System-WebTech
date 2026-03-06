<?php
/**
 * Centralized Path Configuration
 * This file defines all project paths using __DIR__ magic constant
 * This ensures paths work correctly regardless of where files are included from
 */

// Define the root directory of the project
define('PROJECT_ROOT', dirname(dirname(__FILE__)));

// Define project directories
define('PARTS_DIR', PROJECT_ROOT . '/PARTS');
define('ASSETS_DIR', PROJECT_ROOT . '/ASSETS');
define('CSS_DIR', ASSETS_DIR . '/CSS');
define('JS_DIR', ASSETS_DIR . '/JS');
define('IMG_DIR', ASSETS_DIR . '/IMG');
define('FONTS_DIR', ASSETS_DIR . '/FONTS');
define('WEBFONTS_DIR', ASSETS_DIR . '/webfonts');
define('EVENT_IMG_DIR', ASSETS_DIR . '/event-image');
define('UPLOADS_DIR', PROJECT_ROOT . '/UPLOADS');
define('UPLOADS_IMG_DIR', UPLOADS_DIR . '/img');
define('EMS_DIR', PROJECT_ROOT . '/EMS');
define('ADMIN_DIR', PROJECT_ROOT . '/ADMIN');
define('USER_DIR', PROJECT_ROOT . '/USER');
define('SVG_DIR', PROJECT_ROOT . '/SVG');
define('VENDOR_DIR', PROJECT_ROOT . '/vendor');

// Define path to database connection settings
define('DB_CONFIG_PATH', PARTS_DIR . '/db_connection_settings.php');

// Define path to configuration file
define('CONFIG_PATH', PARTS_DIR . '/config.php');

// Define path to sanitize input file
define('SANITIZE_PATH', PARTS_DIR . '/sanitize_input.php');

// Define path to background worker file
define('BACKGROUND_WORKER_PATH', PARTS_DIR . '/background_worker.php');
?>
