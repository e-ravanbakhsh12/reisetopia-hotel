<?php
/*
 * Plugin Name:           Reisetopia Hotel Challenge
 * Description:           Reisetopia Hotel Challenge Plugin
 * Plugin URI:            https://www.Reisetopia.de/
 * Author URI:            https://www.linkedin.com/in/ehsan-ravanbakhsh/
 * Version:               1.0.0
 * Author:                Ehsan Ravanbakhsh
 * License:               GPL-2.0+
 * License URI:           http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:           RHC
 * Domain Path:           /languages
 */

use RHC\includes\RHC;  
use RHC\includes\ActiveAction;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;  // If this file is accessed directly, abort.
}

// Define plugin version constant
if (!defined('RHC_VERSION')) {
    define('RHC_VERSION', '1.0.0');
}

// Define plugin directory path constant
if (!defined('RHC_DIR')) {
    define('RHC_DIR', plugin_dir_path(__FILE__));
}

// Define plugin URL constant
if (!defined('RHC_URL')) {
    define('RHC_URL', plugin_dir_url(__FILE__));
}

// Require Composer's autoload file to load dependencies
require_once RHC_DIR . '/vendor/autoload.php';

// Initialize the ActiveAction class
$ActiveAction = new ActiveAction();

// Register activation and deactivation hooks
// @param string $file: The main plugin file
// @param callable $callback: The method to call when the plugin is activated/deactivated
register_activation_hook(__FILE__, array($ActiveAction, 'activate'));
register_deactivation_hook(__FILE__, array($ActiveAction, 'deactivate'));


/**
 * Function to initialize the plugin
 * 
 * @return void
 */
function executePlugin(): void
{
    // Check if the ACF (Advanced Custom Fields) plugin is active
    if (class_exists('ACF')) {
        new RHC();  // Instantiate the main plugin class
    }
}

// Hook to check for ACF plugin on the plugins_loaded action
add_action('plugins_loaded', 'checkForACF');

// Hook to run the plugin on the plugins_loaded action
add_action('plugins_loaded', 'executePlugin');

