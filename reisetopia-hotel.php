<?php
/*
 * Plugin Name:           Reisetopia Hotel Challenge
 * Description:           Reisetopia Hotel Challeng Plugin
 * Plugin URI:            https://www.Reisetopia.de/
 * Author URI:            https://www.linkedin.com/in/ehsan-ravanbakhsh/
 * Version:               1.0.0
 * Author:                Ehsan Ravanbakhsh
 * License:               GPL-2.0+
 * License URI:           http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:           RHC
 * Domain Path:          /languages
 */

use RHC\includes\RHC;
use RHC\includes\ActiveAction;

// Exit if accessed directly
if (!defined('ABSPATH')) {
     exit;
}

if (!defined('RHC_VERSION')) {
     define('RHC_VERSION', '1.0.0');
}

if (!defined('RHC_DIR')) {
     define('RHC_DIR', plugin_dir_path(__FILE__));
}

if (!defined('RHC_URL')) {
     define('RHC_URL', plugin_dir_url(__FILE__));
}
// plugin requires
require_once RHC_DIR . '/vendor/autoload.php';

$ActiveAction = new ActiveAction();
register_activation_hook(__FILE__, array($ActiveAction, 'activate'));
register_deactivation_hook(__FILE__, array($ActiveAction, 'deactivate'));


/**
 * Running plugin
 */
add_action('plugins_loaded', 'checkForACF');
add_action('plugins_loaded', 'executePlugin');
function executePlugin()
{
     if (class_exists('ACF')) {
          new RHC();
     }
}
