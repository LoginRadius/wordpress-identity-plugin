<?php

/**
 * Plugin Name: LoginRadius CIAM
 * Plugin URI: http://www.loginradius.com
 * Description: LoginRadius Customer Identity and Access Management
 * Version: 4.4.0
 * Author: LoginRadius Team
 * Created by LoginRadius Development Team on 26/05/2017
 * Copyright: 2017 LoginRadius Inc. All rights reserved
 * Author URI: http://www.loginradius.com
 * Text Domain: loginradius-ciam
 * License: GPL2+
 */
defined('ABSPATH') or die();

define('CIAM_PLUGIN_PATH', __FILE__);
define('CIAM_PLUGIN_DIR', plugin_dir_path(CIAM_PLUGIN_PATH));
define('CIAM_PLUGIN_URL', plugin_dir_url(CIAM_PLUGIN_PATH));
define('CIAM_PLUGIN_VERSION', '4.4.0');
define('CIAM_SETTING_LINK', plugin_basename(__FILE__));

// Initialize Modules in specific order
include_once CIAM_PLUGIN_DIR . 'auto-loader.php';
new CIAM_Plugin_Auto_Loader();


register_activation_hook( __FILE__, 'loginradius_ciam_activate' );

register_deactivation_hook(__FILE__, 'loginradius_ciam_deactivate');

function loginradius_ciam_activate() {
     $ciam_authentication_setting = array();
     $api_setting = array();
     $ciam_api_setting = get_option('ciam_api_settings');
     if(isset($ciam_api_setting['apikey']) && isset($ciam_api_setting['secret']))
     {
         if(!isset($ciam_api_setting['update_plugin']) || $ciam_api_setting['update_plugin'] == 'false')
         {
             $ciam_api_setting['update_plugin'] = 'true';
              update_option('ciam_api_settings',$ciam_api_setting);
              if(get_option('ciam_authentication_settings'))
              {
                  $ciam_authentication_setting = get_option('ciam_authentication_settings');
              }
              if(get_option('ciam_api_settings'))
              {
                  $api_setting = get_option('ciam_api_settings');
              }
                require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Clients/IHttpClientInterface.php';

                require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Clients/DefaultHttpClient.php';

                require_once CIAM_PLUGIN_DIR . 'authentication/lib/WPHttpClient.php';

                require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/LoginRadiusException.php';

                require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Utility/Functions.php';
                require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Advanced/ConfigurationAPI.php';
                $configAPI = new \LoginRadiusSDK\CustomerRegistration\Advanced\ConfigurationAPI();
                $config = json_decode($configAPI->getConfigurations(), TRUE);
                if(isset($config['AppName']))
                {
                $api_setting['sitename'] = $config['AppName'];
                }
                    if(isset($config['IsUserNameLogin']) && !isset($ciam_authentication_setting['login_type']))
                    {
                    $ciam_authentication_setting['login_type'] =  $config['IsUserNameLogin'];
                    }
                    if(isset($config['AskEmailIdForUnverifiedUserLogin']) && !isset($ciam_authentication_setting['askEmailForUnverifiedProfileAlways']))
                    {
                    $ciam_authentication_setting['askEmailForUnverifiedProfileAlways'] =  $config['AskEmailIdForUnverifiedUserLogin'];
                    }
                    if(isset($config['AskRequiredFieldsOnTraditionalLogin']) && !isset($ciam_authentication_setting['AskRequiredFieldsOnTraditionalLogin']))
                    {
                    $ciam_authentication_setting['AskRequiredFieldsOnTraditionalLogin'] =  $config['AskRequiredFieldsOnTraditionalLogin'];
                    }
                    if(isset($config['AskPasswordOnSocialLogin']) && !isset($ciam_authentication_setting['prompt_password']))
                    {
                    $ciam_authentication_setting['prompt_password'] =  $config['AskPasswordOnSocialLogin'];
                    }
                    if(isset($config['CheckPhoneNoAvailabilityOnRegistration']) && !isset($ciam_authentication_setting['existPhoneNumber']))
                    {
                    $ciam_authentication_setting['existPhoneNumber'] =  $config['CheckPhoneNoAvailabilityOnRegistration'];
                    }
                    if(isset($config['CheckPhoneNoAvailabilityOnRegistration']) && !isset($ciam_authentication_setting['existPhoneNumber']))
                    {
                    $ciam_authentication_setting['existPhoneNumber'] =  $config['CheckPhoneNoAvailabilityOnRegistration'];
                    }
                  
                 update_option('ciam_authentication_settings',$ciam_authentication_setting);
                 update_option('ciam_api_settings',$api_setting);
         }
     }     
}

function loginradius_ciam_deactivate(){
    global $ciam_credentials; 
    if(isset($ciam_credentials['update_plugin']) && $ciam_credentials['update_plugin'] == 'true'){
         $ciam_api_setting = $ciam_credentials;
         $ciam_api_setting['update_plugin'] = 'false';
         update_option('ciam_api_settings',$ciam_api_setting);
    }
}