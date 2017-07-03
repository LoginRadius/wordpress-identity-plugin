<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CIAM_Activation')) {

    class CIAM_Activation {

        function __construct() {
            global $ciam_credencials;
          $ciam_credencials = get_option('Ciam_API_settings');
         
            /* adding action for ciam debug */
            add_action("ciam_debug", array($this, "debug_mode"), 10, 4);
            
            add_action('init', array($this, 'init'));
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        public function init() {
            add_filter("plugin_action_links_" . CIAM_SETTING_LINK, array($this, 'settings_link'));
            add_action('admin_menu', array($this, 'menu'));
            $this->load_dependencies();
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        function settings_link($links) {
            $settings_link = '<a href="admin.php?page=ciam-activation">' . __('Settings', 'ciam') . '</a>';
            array_unshift($links, $settings_link);
            return $links;
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /* Creating Plugin Admin Menu */

        public function menu() {
            add_menu_page('CIAM', 'CIAM', 'manage_options', 'ciam-activation', array('CIAM_Activation_Admin', 'options_page'), CIAM_PLUGIN_URL . 'activation/assets/images/favicon.ico');
            add_submenu_page('ciam-activation', 'Activation Settings', 'Activation', 'manage_options', 'ciam-activation', array('CIAM_Activation_Admin', 'options_page'));

            // Customize Menu based on do_action order
            do_action('ciam_admin_menu');
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global CIAM_activationsettings, loginRadiusObject
         */
        private function load_dependencies() {
            // Activation settings class.
            require_once( CIAM_PLUGIN_DIR . 'activation/admin/views/settings.php' );
            require_once( CIAM_PLUGIN_DIR . 'activation/admin/class-activation.php' );
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }
        
        
        
         /*
         * this function will create log file on the function will get called....
         */

        public function debug_mode($function_name, $args, $class_name, $output = "") {
            global $ciam_credencials, $ciam_setting;
            
            $ciam_setting = get_option('Ciam_Authentication_settings');
            if(!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])){ 
                 return;   
             }
           
            if (isset($ciam_setting['debug_enable']) && ($ciam_setting['debug_enable'] == 1)) { 
               
                /* removing the credencials value from the arg array. */
                for ($i = 0; $i <= count($args); $i++) {

                    if (isset($args[$i]) && is_array($args[$i])) {

                        if (in_array($ciam_credencials['sitename'], $args[$i])) {
                            $pos = array_search($ciam_credencials['sitename'], $args[$i]);
                            unset($args[$i][$pos]);
                        }

                        if (in_array($ciam_credencials['apikey'], $args[$i])) {
                            $pos = array_search($ciam_credencials['apikey'], $args[$i]);
                            unset($args[$i][$pos]);
                        }

                        if (in_array($ciam_credencials['secret'], $args[$i])) {
                            $pos = array_search($ciam_credencials['secret'], $args[$i]);
                            unset($args[$i][$pos]);
                        }
                    }
                }


                /* removing the credencials value from the return output. */
                if(isset($output) && !empty($output)){
                if (is_array($output)) {
                    for ($i = 0; $i <= count($output); $i++) {
                        if (isset($output[$i]) && is_array($output[$i])) {

                            if (in_array($ciam_credencials['sitename'], $output[$i])) {
                                $pos = array_search($ciam_credencials['sitename'], $output[$i]);
                                unset($output[$i][$pos]);
                            }

                            if (in_array($ciam_credencials['apikey'], $output[$i])) {
                                $pos = array_search($ciam_credencials['apikey'], $output[$i]);
                                unset($output[$i][$pos]);
                            }

                            if (in_array($ciam_credencials['secret'], $output[$i])) {
                                $pos = array_search($ciam_credencials['secret'], $output[$i]);
                                unset($output[$i][$pos]);
                            }
                        } else {

                            if (in_array($ciam_credencials['sitename'], $output)) {
                                $pos = array_search($ciam_credencials['sitename'], $output);
                                unset($output[$pos]);
                            }

                            if (in_array($ciam_credencials['apikey'], $output)) {
                                $pos = array_search($ciam_credencials['apikey'], $output);
                                unset($output[$pos]);
                            }

                            if (in_array($ciam_credencials['secret'], $output)) {
                                $pos = array_search($ciam_credencials['secret'], $output);
                                unset($output[$pos]);
                            }
                        }
                    }
                } elseif (($output === $ciam_credencials['sitename']) || ($output === $ciam_credencials['apikey']) || ($output === $ciam_credencials['secret'])) {
                    unset($output);
                }
            }else{
                
                $output = "";
            }
               $log_message = '['.date("F j, Y, g:i a e O").']'."Class Name :" . "\r\n" . $class_name . "\r\n" . "Function Name :" . "\r\n" . $function_name . "\r\n" . "Function Args :" . "\r\n" . json_encode($args) . "\r\n" . "Function Output :" . "\r\n" . json_encode($output)."\r\n"; 
               
                error_log($log_message , 3 , CIAM_PLUGIN_DIR.'ciam_debug.log');
            }
        }

    }

    new CIAM_Activation();
}

