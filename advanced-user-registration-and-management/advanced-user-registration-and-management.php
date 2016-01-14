<?php

/**
 * Plugin Name: LoginRadius Advanced Plugin
 * Plugin URI: http://www.loginradius.com
 * Description: LoginRadius Advanced Plugin
 * Version: 2.3
 * Author: LoginRadius Team
 * Author URI: http://www.loginradius.com
 * License: GPL2+
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) or die();

define( 'LR_ROOT_DIR', plugin_dir_path(__FILE__) );
define( 'LR_ROOT_URL', plugin_dir_url(__FILE__) );
define( 'LR_ROOT_SETTING_LINK', plugin_basename(__FILE__) );

// Initialize Modules in specific order
include_once LR_ROOT_DIR . 'module-loader.php';
new LR_Modules_Loader();