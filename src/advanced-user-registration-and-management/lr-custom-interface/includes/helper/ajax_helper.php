<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

Class LR_CI_Ajax_Helper {

    private static $custom_interface_dir;
    private static $default_interface_dir;

    public function __construct() {
        self::$custom_interface_dir = LR_ROOT_DIR . 'lr-custom-interface/assets/images/custom_interface';
        self::$default_interface_dir = LR_ROOT_DIR . 'lr-custom-interface/assets/images/default_interface';
    }

    public static function upload_handler() {

        // Check if dest dir is writable.
        if (is_writable(self::$custom_interface_dir)) {
            return self::lr_upload_provider_image();
        } else {
            return 'Upload folder is not writable, please check your permission settings. <br />';
        }
    }

    /**
     * Convert given date string into a different format.
     *
     * $format should be either a PHP date format string, e.g. 'U' for a Unix
     * timestamp, or 'G' for a Unix timestamp assuming that $date is GMT.
     *
     * If $translate is true then the given date and format string will
     * be passed to date_i18n() for translation.
     *
     *
     * @param string $format    Format of the date to return.
     * @param string $date      Date string to convert.
     * @param bool   $translate Whether the return date should be translated. Default true.
     * @return string|int|bool Formatted date string or Unix timestamp. False if $date is empty.
     */
    private static function move_upload_files($path) {
        foreach ($_FILES["images"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["images"]["tmp_name"][$key];
                $name = strtolower(isset($_POST["socialProvider"]) ? trim($_POST["socialProvider"]) . '.png' : $_FILES["images"]["name"][$key]);
                if ($dest_file = fopen("$path/$name", 'a')) {
                    fclose($dest_file);
                    unlink("$path/$name");
                    if (!self::move_uploaded_file($tmp_name, "$path/$name")) {
                        echo '<div style="color:red;">Failed to upload image ' . $name . ', please upload it manually.</div>';
                    }
                } else {
                    self::move_uploaded_file($tmp_name, "$path/$name");
                }
            }
        }
    }

    public static function move_uploaded_file($source, $destination) {
        return copy($source, $destination);
    }

    public static function lr_upload_provider_image() {
        if (is_multisite()) {
            $path = self::$custom_interface_dir . '/' . get_current_blog_id();
            if (!file_exists($path)) {
                wp_mkdir_p($path);
            }
            self::move_upload_files($path);
        } else {
            self::move_upload_files(self::$custom_interface_dir);
        }
        return 'image has been uploaded successfully.';
    }

    public function reset_ci_folder() {
        if (is_writable(self::$custom_interface_dir)) {
            self::lr_move_provider_image();
            return array('isValid' => 'alert', 'message' => 'Custom Interface settings have been reset and default images loaded');
        } else {
            return array('isValid' => 'warning', 'message' => 'Upload folder is not writable, please check your permission settings');
        }
        die();
    }

    function lr_move_provider_image() {
        if (is_multisite()) {
            self::move_default_files(self::$custom_interface_dir . '/' . get_current_blog_id());
        } else {
            self::move_default_files(self::$custom_interface_dir);
        }
    }

    public static function move_default_files($custom_interface_dir) {

        // Delete all files under custom interface directory.
        $ci_files = glob($custom_interface_dir . '/*');
        foreach ($ci_files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        // Move all files from default folder to custom interface folder.
        $default_files = scandir(self::$default_interface_dir);
        foreach ($default_files as $file) {
            if (in_array($file, array(".", ".."))) {
                continue;
            }
            self::move_uploaded_file(self::$default_interface_dir . '/' . $file, $custom_interface_dir . '/' . $file);
        }
    }

    public function check_max_upload() {
        return ini_get('max_file_uploads');
    }

    private function security_check($name) {

        $provider_list = LR_Custom_Interface_Install::get_selected_providers();

        $ext_list = array('png');

        // Check extensions.
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        if (!in_array($ext, $ext_list)) {
            echo "Wrong image type for file $name, all images must be in png.";
            return 0;
        }
        // Check file name.
        $file_name = basename($name);
        $file_name = basename($name, '.png');
        echo ( $file_name . "<br>" );
        if (!in_array(strtolower($file_name), $provider_list)) {
            echo "Wrong file name $name, please check the correct name convention for your files in our documentation.";
            return 0;
        }
        return 1;
    }

}
