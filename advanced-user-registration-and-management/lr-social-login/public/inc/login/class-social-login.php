<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'Social_Login' ) ) {

    /**
     * Main class for providing Social Login functionality
     */
    class Social_Login {

        private static $instance = null;
        public static $loginRadiusProfileData;

        /**
         * Get singleton object for class Social_Login
         *
         * @return object Social_Login
         */
        public static function get_instance() {

            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Social_Login ) ) {
                self::$instance = new Social_Login();
            }
            return self::$instance;
        }

        /**
         * Constructor for class Social_Login
         */
        public function __construct() {
            require_once( LOGINRADIUS_PLUGIN_DIR."public/inc/login/class-login-helper.php" );

            $this->register_hook_callbacks();
            add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_scripts' ) );
        }

        public static function login_enqueue_scripts( ) {
            wp_enqueue_script( 'jquery' );
        }

        /**
         * regeister callbacks for required hooks for social login
         */
        public function register_hook_callbacks() {
            global $loginRadiusSettings;
            
            add_action('init', array($this, 'social_login_init'));

            // Display Social Login Interface with wp_login form
            if (isset($loginRadiusSettings['LoginRadius_loginform']) && $loginRadiusSettings['LoginRadius_loginform'] == '1' && isset($loginRadiusSettings['LoginRadius_loginformPosition']) && $loginRadiusSettings['LoginRadius_loginformPosition'] == 'embed') {
                add_action('login_form', array('Login_Helper', 'display_social_login_interface'));
                add_action('bp_before_sidebar_login_form', array('Login_Helper', 'display_social_login_interface'));
            }

            // display Social Login interface on register form in embed mode
            if (isset($loginRadiusSettings['LoginRadius_regform']) && $loginRadiusSettings['LoginRadius_regform'] == '1' && isset($loginRadiusSettings['LoginRadius_regformPosition']) && $loginRadiusSettings['LoginRadius_regformPosition'] == 'embed') {
                add_action('register_form', array('Login_Helper', 'display_social_login_interface'));
                //Enable if theme is not supporting after signup form
                //add_action('preprocess_signup_form', array('Login_Helper', 'display_social_login_interface'));
                add_action('after_signup_form', array('Login_Helper', 'display_social_login_interface'));
                add_action('bp_before_account_details_fields', array('Login_Helper', 'display_social_login_interface'));
            }

            // Filter for changing default WordPress avatar
            if ( ! empty( $loginRadiusSettings['LoginRadius_socialavatar'] ) && in_array( $loginRadiusSettings['LoginRadius_socialavatar'], array( 'socialavatar','largeavatar' ) ) ) {
                add_filter( 'get_avatar', array( $this, 'replace_default_avatar_with_social_avatar' ), 10, 5 );
                add_filter( 'bp_core_fetch_avatar', array( $this, 'change_buddypress_avatar' ), 10, 2 );
            }

            // show Social Login interface before buddypress login form and register form
            if ( ( isset($loginRadiusSettings['LoginRadius_loginform']) && $loginRadiusSettings['LoginRadius_loginform'] == '1' && isset($loginRadiusSettings['LoginRadius_loginformPosition']) && $loginRadiusSettings['LoginRadius_loginformPosition'] == 'beside') || ( isset($loginRadiusSettings['LoginRadius_regform']) && $loginRadiusSettings['LoginRadius_regform'] == '1' && isset($loginRadiusSettings['LoginRadius_regformPosition']) && $loginRadiusSettings['LoginRadius_regformPosition'] == 'beside') ) {
                add_action('login_head', array('Login_Helper', 'social_login_interface_beside_registration'));
                if ($loginRadiusSettings['LoginRadius_loginformPosition'] == 'beside') {
                    add_action('bp_before_sidebar_login_form', array('Login_Helper', 'display_social_login_interface'));
                }
                if ($loginRadiusSettings['LoginRadius_regformPosition'] == 'beside') {
                    add_action('bp_before_account_details_fields', array('Login_Helper', 'display_social_login_interface'));
                }
            }

            add_filter('authenticate', array($this, 'Stop_disabled_user_registration'), 40, 2);
            add_filter('login_errors', array($this, 'error_message_for_inactive_user'));
            add_action('clear_auth_cookie', array('Login_Helper', 'delete_social_login_meta'));
        }

        /**
         * callback for init hook, it loads plugin script on front
         */
        public function social_login_init() {

            if (get_option('LR_PLUGIN_VERSION') != LR_MIN_WP_VERSION) {
                $this->update_plugin_meta_if_old_verison();
            }

            add_action('wp_enqueue_scripts', array($this, 'front_end_scripts'), 15);
            add_action('parse_request', array($this, 'login_radius_connect'));
            add_action('wp_enqueue_scripts', array($this, 'front_end_css'));
            add_filter('LR_logout_url', array($this, 'log_out_url'), 20, 2);
            add_action('login_head', array( $this, 'front_end_scripts' ), 15);
        }

        /**
         * This function is called when token is returned from LoginRadius.
         * it checks for query string variable and fetches data using LoginRadius api.
         * After fetching data, appropriate action is taken on the basis of LoginRadius plugin settings
         */
        public static function login_radius_connect() {
            global $wpdb, $loginRadiusSettings, $loginRadiusObject, $lr_social_profile_data_settings;

            if (isset($_GET['loginradius_linking']) && isset($_REQUEST['token'])) {
                LR_Common::perform_linking_operation();
            }

            if ( isset( $_GET['loginRadiusVk'] ) && trim( $_GET['loginRadiusVk'] ) != '' ) {
                //If verification link is clicked
                Login_Helper::verify_user_after_email_confirmation();
            }

            if ( isset( $_POST['LoginRadius_popupSubmit'] ) ) {
                //If "email required" popup has been submitted
                Login_helper::response_to_popup_submission();
            }

            // If remove button clicked
            if ( isset( $_GET['loginRadiusMap'] ) && ! empty( $_GET['loginRadiusMap'] ) && isset( $_GET['loginRadiusMappingProvider'] ) && ! empty( $_GET['loginRadiusMappingProvider'] ) && is_user_logged_in() ) {
                Login_Helper::unlink_provider();
            }

            if ( ! is_user_logged_in() && isset( $_REQUEST['token'] ) ) {

                $token = $_REQUEST['token'];

                try {
                    // Fetch user profile using access token ......
                    $responseFromLoginRadius = $loginRadiusObject->loginradius_get_user_profiledata($token); 
                } catch ( LoginRadiusException $e ) {
                    // Error Handling
                    if ( $loginRadiusSettings['enable_degugging'] == '0' ) {
                        // if debugging is off and Social profile not recieved, redirect to home page.
                        wp_redirect(site_url());
                        exit();
                    } else {
                        $responseFromLoginRadius = null;
                        $message = isset( $e->getErrorResponse()->description ) ? $e->getErrorResponse()->description : $e->getMessage();
                        error_log( $message );
                        // If debug option is set and Social Profile not retrieved
                        Login_Helper::login_radius_notify( $message, 'isProfileNotRetrieved' );
                        return;
                    }
                }

                if ( isset( $responseFromLoginRadius->ID ) && $responseFromLoginRadius->ID != null ) {
                    // If profile data is retrieved successfully
                    self::$loginRadiusProfileData = Login_Helper::filter_loginradius_data_for_wordpress_use($responseFromLoginRadius);
                }
                $userId = Login_Helper::is_socialid_exists_in_wordpress( self::$loginRadiusProfileData );
                if ( $userId ) {
                    //if Social id exists in wordpress database
                    if ( ( 1 == get_user_meta( $userId, self::$loginRadiusProfileData['Provider'] . 'LrVerified', true ) ) || class_exists( 'LR_Raas_Install' ) ) {
                        // if user is verified, provide login.
                        Login_Helper::login_user( $userId, self::$loginRadiusProfileData );
                    } else {
                        // If not verified then display pop up.
                        Login_Helper:: login_radius_notify( __( 'Please verify your email by clicking the confirmation link sent to you.', 'lr-plugin-slug' ), 'isEmailNotVerified');
                        return;
                    }
                }

                // check if id already exists.
                $loginRadiusUserId = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key='loginradius_provider_id' AND meta_value = %s", self::$loginRadiusProfileData['ID']));
                if ( ! empty( $loginRadiusUserId ) ) {
                    // id exists
                    $tempUserId = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginradius_isVerified'", $loginRadiusUserId));
                    if ( ! empty( $tempUserId ) ) {
                        // check if verification field exists.
                        $isVerified = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginradius_isVerified'", $loginRadiusUserId));
                        
                        if ( $isVerified == '1' ) {                             // if email is verified
                            Login_Helper::login_user( $loginRadiusUserId, self::$loginRadiusProfileData );
                            return;
                        } else {
                            Login_Helper::login_radius_notify( __( 'Please verify your email by clicking the confirmation link sent to you.', 'lr-plugin-slug' ), 'isEmailNotVerified' );
                            return;
                        }
                    } else {
                        Login_Helper::login_user( $loginRadiusUserId, self::$loginRadiusProfileData );
                        return;
                    }
                } else {

                    // Email is empty for social profile data.
                    $dummyEmail = isset( $loginRadiusSettings['LoginRadius_dummyemail'] ) ? $loginRadiusSettings['LoginRadius_dummyemail'] : '';

                    // Create dummy email.
                    if ( empty( self::$loginRadiusProfileData['Email'] ) && $dummyEmail == 'dummyemail' ) {
                        // Email not required according to plugin settings
                        self::$loginRadiusProfileData['Email'] = Login_Helper:: generate_dummy_email( self::$loginRadiusProfileData );
                    }

                    // Retrieve Profile Data in Popup - Custom popup enabled
                    if ( isset( $lr_social_profile_data_settings['enable_custom_popup'] ) && $lr_social_profile_data_settings['enable_custom_popup'] == '1' ) {

                        //Has Email from provider or from dummy email
                        $has_email = isset( self::$loginRadiusProfileData['Email'] ) && ! empty( self::$loginRadiusProfileData['Email'] ) ? true : false;

                        $notdummyemail = isset( $loginRadiusSettings['LoginRadius_dummyemail'] ) && $loginRadiusSettings['LoginRadius_dummyemail'] == 'notdummyemail' ? true : false;

                        $showpopup = false;

                        if ( ! $has_email && $notdummyemail && isset( $lr_social_profile_data_settings['show_email'] ) && $lr_social_profile_data_settings['show_email'] == '1' || isset( $loginRadiusSettings['LoginRadius_dummyemail'] ) && $loginRadiusSettings['LoginRadius_dummyemail'] == 'notdummyemail' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_gender']) && $lr_social_profile_data_settings['show_gender'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_birthdate']) && $lr_social_profile_data_settings['show_birthdate'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_phonenumber']) && $lr_social_profile_data_settings['show_phonenumber'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_city']) && $lr_social_profile_data_settings['show_city'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_postalcode']) && $lr_social_profile_data_settings['show_postalcode'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_relationshipstatus']) && $lr_social_profile_data_settings['show_relationshipstatus'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_custom_one']) && $lr_social_profile_data_settings['show_custom_one'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_custom_two']) && $lr_social_profile_data_settings['show_custom_two'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_custom_three']) && $lr_social_profile_data_settings['show_custom_three'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_custom_four']) && $lr_social_profile_data_settings['show_custom_four'] == '1' ) {
                            $showpopup = true;
                        } elseif ( isset($lr_social_profile_data_settings['show_custom_five']) && $lr_social_profile_data_settings['show_custom_five'] == '1' ) {
                            $showpopup = true;
                        }

                        if ( $showpopup ) {
                            $lrUniqueId = Login_Helper::store_temporary_data( self::$loginRadiusProfileData );
                            $queryString = '?lrid=' . $lrUniqueId;
                            wp_redirect( site_url() . $queryString );
                            exit();
                        } else {
                            Login_Helper::register_user( self::$loginRadiusProfileData );
                            return;
                        }
                    } else {
                        // Normal flow no custom popup.
                        if ( empty( self::$loginRadiusProfileData['Email'] ) ) {
                            if ( $dummyEmail == 'dummyemail' ) {
                                Login_Helper::register_user(self::$loginRadiusProfileData);
                                return;
                            } else {
                                // Email required according to plugin settings.
                                $lrUniqueId = Login_Helper::store_temporary_data(self::$loginRadiusProfileData);
                                $queryString = '?lrid=' . $lrUniqueId;
                                wp_redirect(site_url() . $queryString);
                                exit();
                            }
                        } else {
                            // Email is not empty
                            $userObject = get_user_by( 'email', self::$loginRadiusProfileData['Email'] );
                            $loginRadiusUserId = is_object( $userObject ) ? $userObject->ID : '';
                            if ( ! empty($loginRadiusUserId )) {// Email exists
                                $isVerified = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginradius_isVerified'", $loginRadiusUserId ) );
                                if ( ! empty( $isVerified ) ) {
                                    if ($isVerified == '1') {
                                        // Social linking
                                        LR_Common::link_account($loginRadiusUserId, self::$loginRadiusProfileData['ID'], self::$loginRadiusProfileData['Provider'], self::$loginRadiusProfileData['ThumbnailImageUrl'], self::$loginRadiusProfileData['Provider'], '');
                                        // Login user
                                        Login_Helper::login_user($loginRadiusUserId, self::$loginRadiusProfileData);
                                        return;
                                    } else {
                                        $directorySeparator = DIRECTORY_SEPARATOR;
                                        require_once( getcwd() . $directorySeparator . 'wp-admin' . $directorySeparator . 'inc' . $directorySeparator . 'user.php' );
                                        wp_delete_user($loginRadiusUserId);
                                        Login_Helper::register_user(self::$loginRadiusProfileData);
                                    }
                                } else {
                                    // Link Account
                                    LR_Common:: link_account($loginRadiusUserId, self::$loginRadiusProfileData['ID'], self::$loginRadiusProfileData['Provider'], self::$loginRadiusProfileData['ThumbnailImageUrl'], self::$loginRadiusProfileData['Provider'], '');
                                    // Login user
                                    Login_Helper::login_user($loginRadiusUserId, self::$loginRadiusProfileData);
                                    return;
                                }
                            } else {
                                Login_Helper::register_user(self::$loginRadiusProfileData);
                            }
                        }
                    }
                }
            } // Authentication ends
        }

        /**
         * Include necessary stylesheets at front end.
         */
        public function front_end_css() {
            wp_enqueue_style('lr-social-login-style', LOGINRADIUS_PLUGIN_URL . 'assets/css/lr-social-login-front.css', array());
        }

        public static function login_script() {
            LR_Common::load_login_script(true);
        }

        /**
         * Include necessary scripts at front end
         */
        public function front_end_scripts() {
            global $lr_js_in_footer;

            wp_enqueue_script('jquery');

            if ( isset( $_GET['lrid'] ) || isset($_GET['loginRadiusKey']) ) {
                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
            }

            // User is not logged in
            if ( ! is_user_logged_in() ) {

                // Enqueue main scripts
                if ( $lr_js_in_footer ) {
                    add_action('wp_footer', array('LR_Common', 'enqueue_login_scripts'), 2);
                    add_action('wp_footer', array('LR_Common', 'load_login_script'), 9999);

                    add_action('login_footer', array('LR_Common', 'enqueue_login_scripts'), 2);
                    add_action('login_footer', array('LR_Common', 'load_login_script'), 9999);
                } else {
                    // Enqueue main scripts
                    add_action('wp_enqueue_scripts', array('LR_Common', 'enqueue_login_scripts'), 20);
                    add_action('wp_head', array('LR_Common', 'load_login_script'), 9999);

                    add_action('login_enqueue_scripts', array('LR_Common', 'enqueue_login_scripts'), 2);
                    add_action('login_head', array('LR_Common', 'load_login_script'), 9999);
                }
            }
            // User is Logged In
            else {
                if ($lr_js_in_footer) {
                    add_action('wp_footer', array('LR_Common', 'enqueue_login_scripts'), 2);
                    add_action('wp_footer', array(__CLASS__, 'login_script'), 9999);
                } else {
                    add_action('wp_enqueue_scripts', array('LR_Common', 'enqueue_login_scripts'), 20);
                    add_action('wp_head', array(__CLASS__, 'login_script'), 9999);
                }
            }
            ?>
            <script>

                function loginRadiusLoadEvent(func) {
                    /**
                     * Call functions on window.onload
                     */
                    var oldOnLoad = window.onload;
                    if (typeof window.onload != 'function') {
                        window.onload = func;
                    } else {
                        window.onload = function () {
                            oldOnLoad();
                            func();
                        }
                    }
                }
            </script>
            <?php
            //loading thickbox script and css for pop up
            if ( isset( $_GET['lrid'] ) && trim( $_GET['lrid'] ) != '') {
                self::add_thickbox_script_for_email_popup();
            }

            if ( isset( $_GET['loginRadiusKey'] ) ) {
                // if user is not verified then display notification
                self::display_notification_popup();
            }
        }

        /**
         * update usermeta if ita a older version plugin
         */
        public function update_plugin_meta_if_old_verison() {
            global $wpdb;
            $wpdb->query("update " . $wpdb->usermeta . " set meta_key = 'loginradius_provider_id' where meta_key = 'id'");
            $wpdb->query("update " . $wpdb->usermeta . " set meta_key = 'loginradius_thumbnail' where meta_key = 'thumbnail'");
            $wpdb->query("update " . $wpdb->usermeta . " set meta_key = 'loginradius_verification_key' where meta_key = 'loginRadiusVkey'");
            $wpdb->query("update " . $wpdb->usermeta . " set meta_key = 'loginradius_isVerified' where meta_key = 'loginRadiusVerified'");
        }

        /**
         * Display notification if user is not verified
         */
        public static function display_notification_popup() {
            $message = get_user_meta($_GET['loginRadiusKey'], 'loginradius_tmpKey', true);
            $redirection = get_user_meta($_GET['loginRadiusKey'], 'loginradius_tmpRedirection', true);
            delete_user_meta($_GET['loginRadiusKey'], 'loginradius_tmpKey');
            delete_user_meta($_GET['loginRadiusKey'], 'loginradius_tmpRedirection');
            if ($message != '') {
                $args = array(
                    'height' => 1,
                    'width' => 1,
                    'action' => 'login_radius_notification_popup',
                    'key' => '',
                    'message' => urlencode($message),
                );
                if ($redirection != '') {
                    $args['redirection'] = $redirection;
                }
                $ajaxUrl = add_query_arg($args, 'admin-ajax.php');
                ?>
                <style type="text/css">
                    #TB_window{
                        margin-top: -45px !important;
                    }
                </style>
                <script>
                    // show thickbox on window load
                    loginRadiusLoadEvent(function () {
                        tb_show('Message', '<?php echo admin_url() . $ajaxUrl; ?>');
                    });
                </script>
                <?php
            }
        }

        /**
         * function for using log_out_url for LoginRadius Login widget button
         */
        public function log_out_url() {
            $redirect = get_permalink();
            $link = '<a href="' . wp_logout_url($redirect) . '" title="' . _e('Logout', 'lr-plugin-slug') . '">' . _e('Logout', 'lr-plugin-slug') . '</a>';
            echo apply_filters('Login_Radius_log_out_url', $link);
        }

        /**
         * add thickbox script and css for email popup
         */
        public static function add_thickbox_script_for_email_popup() {
            global $wpdb, $loginRadiusSettings;
            $isError = 'no';
            $message = $key = '';

            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
            wp_enqueue_script('jquery-validator', '//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.47/jquery.form-validator.min.js');

            if (isset($_GET['LoginRadiusMessage']) && trim($_GET['LoginRadiusMessage']) == 'emailExists') {

                $key = trim($_GET['lrid']);
                $message = trim($loginRadiusSettings['msg_existemail']);
                $isError = 'yes';
            } elseif (isset($_GET['LoginRadiusMessage']) && trim($_GET['LoginRadiusMessage']) == 'formData') {
                $key = trim($_GET['lrid']);
                $message = 'Please ensure all fields are filled in correctly';
            } else {
                $loginRadiusTempUniqueId = $wpdb->get_var($wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key='tmpsession' AND meta_value = %s", trim( $_GET['lrid'] ) ) );
                if (!empty($loginRadiusTempUniqueId)) {
                    $key = trim($_GET['lrid']);
                    $message = trim($loginRadiusSettings['msg_email']);
                }
            }
            $ajaxUrl = add_query_arg(
                    array(
                'height' => 1,
                'width' => 1,
                'action' => 'login_radius_email_popup',
                'key' => $key,
                'message' => urlencode($message),
                'isError' => $isError,
                    ), 'admin-ajax.php'
            );
            ?>

            <script>
                // Show thickbox on window load
                loginRadiusLoadEvent(function () {
                    tb_show('Email required', '<?php echo admin_url() . $ajaxUrl; ?>');
                });

                // get trim() worked in IE
                if ( typeof String.prototype.trim !== 'function' ) {
                    String.prototype.trim = function () {
                        return this.replace(/^\s+|\s+$/g, '');
                    }
                }
                var loginRadiusPopupSubmit = true;
                function loginRadiusValidateEmail() {

                    if ( ! loginRadiusPopupSubmit ) {
                        return true;
                    }
                    var email = document.getElementById('loginRadiusEmail').value.trim();
                    var loginRadiusErrorDiv = document.getElementById('loginRadiusError');
                    var emailRequiredMessageDiv = document.getElementById('textmatter');

                    var atPosition = email.indexOf("@");
                    var dotPosition = email.lastIndexOf(".");
                    if ( email == '' || atPosition < 1 || dotPosition < atPosition + 2 || dotPosition + 2 >= email.length ) {
                        emailRequiredMessageDiv.style.display = "none";
                        loginRadiusErrorDiv.style.display = "block";
                        loginRadiusErrorDiv.innerHTML = "The email you have entered is invalid. Please enter a valid email address.";
                        loginRadiusErrorDiv.style.backgroundColor = "rgb(255, 235, 232)";
                        loginRadiusErrorDiv.style.border = "1px solid rgb(204, 0, 0)";
                        loginRadiusErrorDiv.style.padding = "2px 5px";
                        loginRadiusErrorDiv.style.width = "94%";
                        loginRadiusErrorDiv.style.textAlign = "left";

                        return false;
                    }
                    return true;
                }
            </script>

            <?php
        }

        /**
         * Replace default avatar with social avatar
         * Used for Clinet and Admin Sections
         */
        public function replace_default_avatar_with_social_avatar( $avatar, $avuser, $size, $default, $alt = '' ) {
            global $loginRadiusSettings;
            $userId = null;
            $default = null;
            if ( is_numeric( $avuser ) ) {
                if ( $avuser > 0) {
                    $userId = $avuser;
                }
            } elseif ( is_object( $avuser ) ) {
                if ( property_exists( $avuser, 'user_id' ) && is_numeric( $avuser->user_id ) ) {
                    $userId = $avuser->user_id;
                }
            }
            if ( ! empty( $userId ) ) {
                // Changes Avatar to Social Login Avatar - Client and Admin Section.
                if( 'socialavatar' == $loginRadiusSettings['LoginRadius_socialavatar'] ) {
                    if ( ( $userAvatar = get_user_meta( $userId, 'loginradius_thumbnail', true ) ) !== false && strlen( trim( $userAvatar ) ) > 0 ) {
                    }
                } elseif ( 'largeavatar' == $loginRadiusSettings['LoginRadius_socialavatar'] )  {
                    if ( ( $userAvatar = get_user_meta( $userId, 'loginradius_picture', true ) ) !== false && strlen( trim( $userAvatar ) ) > 0 ) {
                    }
                }
                if( empty( $userAvatar ) ) {
                    return $avatar;
                }
                return '<img alt="' . esc_attr( $alt ) . '" src="' . $userAvatar . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '" />';
            }
            return $avatar;
        }

        /**
         * Replace buddypress default avatar with social avatar.
         * Used for Client and Admin Sections
         */
        public static function change_buddypress_avatar( $text, $args ) {
            global $loginRadiusSettings;
            //Check arguments
            if ( is_array( $args ) ) {
                
                if ( ! empty( $args['object'] ) && strtolower( $args['object'] ) == 'user' ) {
                    if ( ! empty( $args['item_id'] ) && is_numeric( $args['item_id'] ) ) {
                        if ( ( $userData = get_userdata( $args['item_id'] ) ) !== false ) {
                            $currentSocialId = get_user_meta( $args['item_id'], 'loginradius_current_id', true);
                            $avatar = '';

                            if( 'socialavatar' == $loginRadiusSettings['LoginRadius_socialavatar'] ) {
                                if ( ( $userAvatar = get_user_meta( $args['item_id'], 'loginradius_thumbnail', true ) ) !== false && strlen( trim( $userAvatar ) ) > 0) {
                                    $avatar = $userAvatar;
                                }
                            } elseif ( 'largeavatar' == $loginRadiusSettings['LoginRadius_socialavatar'] )  {
                                if ( ( $userAvatar = get_user_meta( $args['item_id'], 'loginradius_picture', true ) ) !== false && strlen( trim( $userAvatar ) ) > 0) {
                                    $avatar = $userAvatar;
                                }
                            }

                            if ( $avatar != '' ) {
                                $imgAltText = ( ! empty($args['alt'] ) ? 'alt="' . esc_attr( $args['alt'] ) . '" ' : '' );
                                $imgAlt = sprintf( $imgAltText, htmlspecialchars( $userData->user_login ) );
                                $imgClass = ( 'class="' . ( ! empty( $args['class'] ) ? ( $args['class'] . ' ' ) : '' ) . 'avatar-social-login" ' );
                                $imgWidth = ( ! empty( $args['width']) ? 'width="' . $args['width'] . '" ' : 'width="50"' );
                                $imgHeight = ( ! empty( $args['height']) ? 'height="' . $args['height'] . '" ' : 'height="50"' );
                                $text = preg_replace('#<img[^>]+>#i', '<img src="' . $avatar . '" ' . $imgAlt . $imgClass . $imgHeight . $imgWidth . ' />', $text);
                            }
                        }
                    }
                }
            }
            return $text;
        }

        /**
         * Stop disabled user from logging in.
         */
        public function Stop_disabled_user_registration( $user, $username ) {
            $tempUser = get_user_by( 'login', $username );
            if ( isset( $tempUser->data->ID ) ) {
                $id = $tempUser->data->ID;
                if ( get_user_meta( $id, 'loginradius_status', true ) === '0' ) {
                    global $loginRadiusLoginAttempt;
                    $loginRadiusLoginAttempt = 1;
                    return null;
                }
            }
            return $user;
        }

        /**
         * Display error message to inactive user
         */
        public static function error_message_for_inactive_user($error) {
            global $loginRadiusLoginAttempt;
            //check if inactive user has attempted to login
            if ( $loginRadiusLoginAttempt == 1 ) {
                $error = __( 'Your account is currently inactive. You will be notified through email, once Administrator activates your account.', 'lr-plugin-slug');
            }
            return $error;
        }
    }

}