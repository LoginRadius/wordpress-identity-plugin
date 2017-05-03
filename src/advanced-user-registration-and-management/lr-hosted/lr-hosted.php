<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_HOSTED')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_HOSTED {

        /**
         * Constructor
         */
        public function __construct() {
            if (!class_exists('LR_Raas')) {
                return;
            }
            $this->load_dependencies();
            add_action('hosted_page', array('LR_HOSTED_Admin_Settings', 'render_options_page'));
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        public static function load_dependencies() {

            // Load required files.
            require_once(LR_ROOT_DIR . "lr-hosted/admin/views/settings.php");
            require_once(LR_ROOT_DIR . "lr-hosted/public/inc/class-lr-hosted.php");
        }

    }

    new LR_HOSTED();
}
