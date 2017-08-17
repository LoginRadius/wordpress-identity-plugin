<?php

/**
 * Plugin Name: LoginRadius CIAM
 * Plugin URI: http://www.loginradius.com
 * Description: LoginRadius Customer Identity and Access Management
 * Version: 3.0.1
 * Author: LoginRadius Team
 * Created by LoginRadius Development Team on 26/05/2017
 * Copyright � 2017 LoginRadius Inc. All rights reserved
 * Author URI: http://www.loginradius.com
 * Text Domain: loginradius-ciam
 * License: GPL2+
 */
defined('ABSPATH') or die();

define('CIAM_PLUGIN_PATH', __FILE__);
define('CIAM_PLUGIN_DIR', plugin_dir_path(CIAM_PLUGIN_PATH));
define('CIAM_PLUGIN_URL', plugin_dir_url(CIAM_PLUGIN_PATH));
define('CIAM_PLUGIN_VERSION', '3.0.1');
define('CIAM_SETTING_LINK', plugin_basename(__FILE__));

// Initialize Modules in specific order
include_once CIAM_PLUGIN_DIR . 'auto-loader.php';
new CIAM_Plugin_Auto_Loader();
