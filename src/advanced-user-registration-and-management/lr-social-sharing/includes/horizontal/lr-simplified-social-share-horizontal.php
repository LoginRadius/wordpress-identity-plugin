<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or die();

/**
 * The horizontal sharing class.
 */
if ( ! class_exists( 'LR_Horizontal_Sharing' ) ) {

    class LR_Horizontal_Sharing {

        /**
         * Constructor
         */
        public function __construct() {
            add_filter('the_content', array($this, 'loginradius_share_horizontal_content'));
            add_filter('get_the_excerpt', array($this, 'loginradius_share_horizontal_content'));
        }

        /**
         * Output Sharing <div> for the content.
         * 
         * @global type $post
         * @global type $loginradius_share_settings
         * @param type $content
         * @return string
         */
        function loginradius_share_horizontal_content( $content ) {
            global $post, $loginradius_share_settings;

            $return = '';
            $top = false;
            $bottom = false;

            if (is_object($post)) {
                $lrMeta = get_post_meta($post->ID, '_login_radius_meta', true);

                // if sharing disabled on this page/post, return content unaltered.
                if (isset($lrMeta['sharing']) && $lrMeta['sharing'] == '1' && !is_front_page()) {
                    return $content;
                }
            }
            LR_Common_Sharing::horizontal_sharing();
            if (current_filter() == 'the_content') {
                // Show on Post.
                if ( is_single() && $post->post_type == 'post' && ( isset($loginradius_share_settings['lr-clicker-hr-post']) && $loginradius_share_settings['lr-clicker-hr-post'] == '1' )) {
                    if (isset($loginradius_share_settings['horizontal_position']['Posts']['Top']))
                        $top = true;
                    if (isset($loginradius_share_settings['horizontal_position']['Posts']['Bottom']))
                        $bottom = true;
                }

                // Show on Custom Post Types
                if ( is_single() && $post->post_type != 'post' && ( isset($loginradius_share_settings['lr-clicker-hr-custom']) && $loginradius_share_settings['lr-clicker-hr-custom'] == '1' )) {
                    if (isset($loginradius_share_settings['horizontal_position']['Custom']['Top']))
                        $top = true;
                    if (isset($loginradius_share_settings['horizontal_position']['Custom']['Bottom']))
                        $bottom = true;
                }

                // Show on home Page.
                if ( is_front_page() && ( isset($loginradius_share_settings['lr-clicker-hr-home']) && $loginradius_share_settings['lr-clicker-hr-home'] == '1' )) {
                    if (isset($loginradius_share_settings['horizontal_position']['Home']['Top']))
                        $top = true;
                    if (isset($loginradius_share_settings['horizontal_position']['Home']['Bottom']))
                        $bottom = true;
                }

                // Show on Static Page.
                if ( is_page() && (isset($loginradius_share_settings['lr-clicker-hr-static']) && $loginradius_share_settings['lr-clicker-hr-static'] == '1' )) {
                    if (isset($loginradius_share_settings['horizontal_position']['Pages']['Top']))
                        $top = true;
                    if (isset($loginradius_share_settings['horizontal_position']['Pages']['Bottom']))
                        $bottom = true;
                }

                // Show on Posts Page when a static page is the front.
                if ( is_home() && ! is_front_page() && (isset($loginradius_share_settings['lr-clicker-hr-excerpts']) && $loginradius_share_settings['lr-clicker-hr-excerpts'] == '1' )) {
                    if (isset($loginradius_share_settings['horizontal_position']['Excerpts']['Top']))
                        $top = true;
                    if (isset($loginradius_share_settings['horizontal_position']['Excerpts']['Bottom']))
                        $bottom = true;
                }

                // Show on Excerpts Page.
                if ( has_excerpt($post->ID) && (isset($loginradius_share_settings['lr-clicker-hr-excerpts']) && $loginradius_share_settings['lr-clicker-hr-excerpts'] == '1' )) {
                    if (isset($loginradius_share_settings['horizontal_position']['Excerpts']['Top']))
                        $top = true;
                    if (isset($loginradius_share_settings['horizontal_position']['Excerpts']['Bottom']))
                        $bottom = true;
                }
            }

            if ( current_filter() == 'get_the_excerpt' && isset($loginradius_share_settings['lr-clicker-hr-excerpts']) && $loginradius_share_settings['lr-clicker-hr-excerpts'] == '1' ) {
                if ( isset($loginradius_share_settings['horizontal_position']['Excerpts']['Top'])) {
                    $top = true;
                }
                if ( isset($loginradius_share_settings['horizontal_position']['Excerpts']['Bottom'])) {
                    $bottom = true;
                }
            }

            if ($top) {
                $return = '<div class="lr_horizontal_share" data-share-url="' . get_permalink($post->ID) . '" data-counter-url="' . get_permalink($post->ID) . '"></div>';
            }

            $return .= $content;

            if ($bottom) {
                $return .= '<div class="lr_horizontal_share" data-share-url="' . get_permalink($post->ID) . '" data-counter-url="' . get_permalink($post->ID) . '"></div>';
            }
            return $return;
        }
    }

    new LR_Horizontal_Sharing();
}
