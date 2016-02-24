<?php
// If this file is called directly, abort.
defined('ABSPATH') or die();

/**
 * The horizontal sharing class.
 */
if (!class_exists('LR_Vertical_Sharing')) {

    class LR_Vertical_Sharing {

        static $params;
        static $position;

        /**
         * Constructor
         * 
         * @global type $lr_js_in_footer
         */
        public function __construct() {
            global $lr_js_in_footer;
            // Enqueue main scripts in footer
            if ($lr_js_in_footer) {
                add_action('wp_footer', array($this, 'vertical_page_content'), 1);
            } else {
                add_action('wp_enqueue_scripts', array($this, 'vertical_page_content'), 2);
            }
        }

        /**
         * Get LoginRadius Vertical div container.
         * 
         * @param type $class
         * @param type $style
         * @return type
         */
        static function get_vertical_sharing($class, $style = '') {
            return '<div class="lr-share-vertical-fix ' . $class . '" ' . $style . '></div>';
        }

        /**
         * 
         * @global type $loginradius_share_settings
         * @param type $page
         * @param type $position
         * @return boolean
         */
        private static function get_vertical_position_option($page, $position) {
            global $loginradius_share_settings;
            if ( isset($loginradius_share_settings['vertical_position'][$page]['Top Left']) && $loginradius_share_settings['vertical_position'][$page]['Top Left'] == 'Top Left' ) {
                $position['top_left'] = true;
            }
            if ( isset($loginradius_share_settings['vertical_position'][$page]['Top Right']) && $loginradius_share_settings['vertical_position'][$page]['Top Right'] == 'Top Right' ) {
                $position['top_right'] = true;
            }
            if ( isset($loginradius_share_settings['vertical_position'][$page]['Bottom Left']) && $loginradius_share_settings['vertical_position'][$page]['Bottom Left'] == 'Bottom Left' ) {
                $position['bottom_left'] = true;
            }
            if ( isset($loginradius_share_settings['vertical_position'][$page]['Bottom Right']) && $loginradius_share_settings['vertical_position'][$page]['Bottom Right'] == 'Bottom Right' ) {
                $position['bottom_right'] = true;
            }
            
            return $position;
        }

        /**
         * 
         * @global type $loginradius_share_settings
         * @return type
         */
        public static function get_vertical_position() {
            global $post, $loginradius_share_settings;

                $position['top_left'] = false;
                $position['top_right'] = false;
                $position['bottom_left'] = false;
                $position['bottom_right'] = false;
            	
                // Show on static Pages.
                if ( is_page() && !is_front_page() && ( isset($loginradius_share_settings['lr-clicker-vr-static']) && $loginradius_share_settings['lr-clicker-vr-static'] == '1' )) {
                	$position = self::get_vertical_position_option('Static', $position);
                }
    	        // Show on Front home Page.
    	        if ( is_front_page() && ( isset($loginradius_share_settings['lr-clicker-vr-home']) && $loginradius_share_settings['lr-clicker-vr-home'] == '1' )) {
    	        	$position = self::get_vertical_position_option('Home', $position);
    	     	}
                // Show on Posts.
                if ( is_single() && $post->post_type == 'post' && ( isset($loginradius_share_settings['lr-clicker-vr-post']) && $loginradius_share_settings['lr-clicker-vr-post'] == '1' ) ) {
                    $position = self::get_vertical_position_option('Post', $position);
                }

                // Show on Custom Post Types
                if ( is_single() && $post->post_type != 'post' && ( isset($loginradius_share_settings['lr-clicker-vr-custom']) && $loginradius_share_settings['lr-clicker-vr-custom'] == '1' ) ) {
                    $position = self::get_vertical_position_option('Custom', $position);
                }
                return $position;
        }

        /**
         * Output Sharing for the content.
         * 
         * @global type $post
         * @param type $content
         * @return type
         */
        function vertical_page_content($content) {
            global $post;
            if (is_object($post)) {
                $lrMeta = get_post_meta($post->ID, '_login_radius_meta', true);

                // if sharing disabled on this page/post, return content unaltered.
                if (isset($lrMeta['sharing']) && $lrMeta['sharing'] == 1 && !is_front_page()) {
                    return $content;
                }
            }
            LR_Common_Sharing::vertical_sharing();
            $position = self::get_vertical_position();

            if ($position['top_left']) {
                $class = uniqid('lr_');
                self::$params['top_left']['class'] = $class;
                $content .= self::get_vertical_sharing($class);
            }
            if ($position['top_right']) {
                $class = uniqid('lr_');
                self::$params['top_right']['class'] = $class;
                $content .= self::get_vertical_sharing($class);
            }
            if ($position['bottom_left']) {
                $class = uniqid('lr_');
                self::$params['bottom_left']['class'] = $class;
                $content .= self::get_vertical_sharing($class);
            }
            if ($position['bottom_right']) {
                $class = uniqid('lr_');
                self::$params['bottom_right']['class'] = $class;
                $content .= self::get_vertical_sharing($class);
            }

            echo $content;
        }

    }

    new LR_Vertical_Sharing();
}