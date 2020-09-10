<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('CIAM_Authentication_Register')) {
    
    class CIAM_Authentication_Register { 
        
        /*
         * Class constructor
         */
        public function __construct() {
           add_action('init',array($this,'init'));
            
        }
        
        /*
         * load all the required dependencies
         */
        public function init(){
            global $ciam_setting;
            add_shortcode('ciam_registration_form', array($this, 'ciam_registration_form'));
            
            if (!empty($ciam_setting)) {
                
                add_filter('register_url', array($this, 'custom_registration_page'), 100);
                add_action('wp_head', array('CIAM_Authentication_Commonmethods', 'ciam_hook_loader'));
              }
        }
        
        /*
         * Function for Registration Form
         */
       
        public function ciam_registration_form() {
            global $ciam_setting;  
            if(!empty($ciam_setting['registration_page_id'])){
            $url = get_permalink($ciam_setting['login_page_id']);
            if (!is_user_logged_in()) {
                ?>

                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        registration_hook('<?php echo $url ?>');
                        social('<?php echo $url ?>');
                    });
                </script> 
                
                <?php
                $message = '<div id="messageinfo" class="messageinfo"></div>';
                ob_start();
                $html = '<div class="ciam-user-reg-container">' . $message;
                $html .= '<span id="registration_message"></span><span id="loginmessage"></span><div id="sociallogin-container"></div><div id="interfacecontainerdiv" class="interfacecontainerdiv"></div><div id="registration-container" class="ciam-input-style"></div><div id="ciam_loading_gif" class="overlay" style="display:none;"><div class="lr_loading_screen"><div class="lr_loading_screen_center" style="position: fixed;"><img class="loading_circle ciam_loading_gif_align lr_loading_screen_spinner" src="' . CIAM_PLUGIN_URL . 'authentication/assets/images/loading-white.png' . '" alt="loding image" /></div></div></div>';
                $html .= '<span class="ciam-link"><a href="' . wp_login_url(). '">Login</a></span>';
                $html .= '<span class="ciam-link btn"><a href="' . wp_lostpassword_url() . '">Forgot Password</a></span></div>';
                add_action('wp_footer', array($this,'datepickerscript'));
                
                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $html);
                return $html . ob_get_clean();
            }
        } 
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }
        
        /*
         * Add datepicker
         */
        public function datepickerscript(){ 
            
            wp_enqueue_style('ciam-style-datepicker', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
            wp_enqueue_script('ciam-js-datepicker', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js', array('jquery'), CIAM_PLUGIN_VERSION, false);
             
                  /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }
        
        /*
         * change authentication link for registration page....
         */

        public function custom_registration_page() {
            global $ciam_setting;
            $register_page = get_permalink($ciam_setting['registration_page_id']);
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $register_page);
            return $register_page;
        }
    }
    new CIAM_Authentication_Register();
}



