<?php
/**
 * Plugin Name: Advanced User Registration and Management
 * Plugin URI: http://www.loginradius.com
 * Description: Advanced User Registration and Management
 * Version: 2.5
 * Author: LoginRadius Team
 * Author URI: http://www.loginradius.com
 * Text Domain: advanced-user-registration-and-management
 * License: GPL2+
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) or die();

define( 'LR_PLUGIN_VERSION', 2.4 );
define( 'LR_MIN_WP_VERSION', '3.5' );

// Type of Plugin ADV, SL, SS
define( 'LR_PLUGIN_PKG', 'ADV' );

define( 'LR_ROOT_DIR', plugin_dir_path(__FILE__) );
define( 'LR_ROOT_URL', plugin_dir_url(__FILE__) );
define( 'LR_ROOT_SETTING_LINK', plugin_basename(__FILE__) );

// Initialize Modules in specific order
include_once LR_ROOT_DIR . 'module-loader.php';
new LR_Modules_Loader();