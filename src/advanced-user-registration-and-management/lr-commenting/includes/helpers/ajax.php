<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class Ajax_Helper {

    private static $return_content = false;
    private static $loginRadiusProfileData;

    public function __construct() {
        add_action('wp_ajax_loginradius_post_comment', array($this, 'loginradius_post_comment'), 1);
        add_action('wp_ajax_nopriv_loginradius_post_comment', array($this, 'loginradius_post_comment'), 1);

        add_action('wp_ajax_loginradius_get_mentions', array($this, 'loginradius_get_mentions'), 1);
        add_action('wp_ajax_nopriv_loginradius_get_mentions', array($this, 'loginradius_get_mentions'), 1);
    }

    /**
     * url_shortener shortens urls within strings via is.hre
     * @param  string $content content containing url to shorten
     * @return string content with shortened urls
     */
    static function url_shortener( $content ) {
        $content = preg_replace_callback( '!https?://\S+!', function ($matches) {
            global $loginradius_api_settings;
            $url = 'https://api.loginradius.com/sharing/v1/shorturl/?key=' . $loginradius_api_settings['LoginRadius_apikey'] . '&url=' . $matches[0];
            $args = array('timeout' => 45);
            $response = wp_remote_get( $url, $args );

            if( isset( $response['body'] ) ) {
                $body = json_decode( $response['body'], true);

                if( isset( $body['ShortUrl'] ) && ! empty( $body['ShortUrl'] ) ) {
                    return $body['ShortUrl'];
                } else {
                   return $matches[0];
                }
            } else {
                return $matches[0];
            }
           
        }, $content );
        return $content;
    }

    /**
     * AJAX function used to login user and post comment.
     */
    function loginradius_post_comment() {
        global $wpdb, $socialLoginObject, $loginRadiusSettings, $lr_commenting_settings;
        
        $login_helper = new Login_Helper();
        $lr_common = new LR_Common();

        $error_msg = '';

        // Set Authorization Token
        $auth_token = isset( $_POST['token'] ) ? $_POST['token'] : '';

        // Log In User
        if ( ! is_user_logged_in() && ! empty( $auth_token ) && $auth_token != null ) {

            // Fetch user profile using access token.
            try{
                $responseFromLoginRadius = $socialLoginObject->getUserProfiledata( $auth_token );
            } catch( \LoginRadiusSDK\LoginRadiusException $e ) {
                
                $responseFromLoginRadius = null;
                $message = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : $e->getMessage();
                        error_log($message);
                        // If debug option is set and Social Profile not retrieved
                        Login_Helper::login_radius_notify($message, 'isProfileNotRetrieved');
                        return;
            }

            // Retrieve profile data.
            if ( isset( $responseFromLoginRadius->ID ) && $responseFromLoginRadius->ID != null ) {
                // If profile data is retrieved successfully
                $loginRadiusProfileData = $login_helper->filter_loginradius_data_for_wordpress_use( $responseFromLoginRadius );
            } else {
                $message = isset($responseFromLoginRadius->description) ? $responseFromLoginRadius->description : $responseFromLoginRadius;
                // Profile not retrieved;
                echo json_encode(array(
                    "Error" => "Profile not retrieved " . $message
                ));
                die();
            }

            // Check for existing userId.
            $userId = $login_helper->is_socialid_exists_in_wordpress( $loginRadiusProfileData );
            if ( ! empty( $userId ) ) {
                // Id exists
                $tempUserId = $wpdb->get_var( $wpdb->prepare("SELECT user_id FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginradius_isVerified'", $userId ) );
                if ( ! empty( $tempUserId ) ) {
                    // check if verification field exists.
                    $isVerified = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginradius_isVerified'", $userId ) );

                    if ( $isVerified == '1' ) {                             
                        // if email is verified
                        $login_helper->login_user( $userId, $loginRadiusProfileData, false, false, false );
                    } else {
                        $error_msg = 'Please verify your email by clicking the confirmation link sent to you.';
                        echo json_encode(array(
                            "Error" => $error_msg
                        ));
                        die();
                    }
                } else {
                    $login_helper->login_user( $userId, $loginRadiusProfileData, false, false, false );
                }
            } else {
                if ( empty( $loginRadiusProfileData['Email'] ) ) {
                    // email not required according to plugin settings
                    $loginRadiusProfileData['Email'] = $login_helper->generate_dummy_email( $loginRadiusProfileData );
                    $login_helper->register_user( $loginRadiusProfileData, false, false );
                } else {
                    // email is not empty
                    $userObject = get_user_by('email', $loginRadiusProfileData['Email']);
                    $userId = is_object( $userObject ) ? $userObject->ID : '';

                    if ( ! empty( $userId ) ) {        // email exists
                        $isVerified = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginradius_isVerified'", $userId ) );

                        if ( ! empty( $isVerified ) ) {
                            if ( $isVerified == '1' ) {
                                // social linking
                                $lr_common->link_account( $userId, $loginRadiusProfileData['UniqueId'], $loginRadiusProfileData['Provider'], $loginRadiusProfileData['Thumbnail'], $loginRadiusProfileData['Provider'], '' );
                                // Login user
                                $login_helper->login_user( $userId, $loginRadiusProfileData, false, false, false );
                            } else {
                                $directorySeparator = DIRECTORY_SEPARATOR;
                                require_once( getcwd() . $directorySeparator . 'wp-admin' . $directorySeparator . 'inc' . $directorySeparator . 'user.php' );
                                wp_delete_user( $userId );
                                $login_helper->register_user( $loginRadiusProfileData, false, false );
                            }
                        } else {
                            if ( get_user_meta( $userId, 'loginradius_provider_id', true ) != false ) {
                                // social linking
                                $lr_common->link_account( $userId, $loginRadiusProfileData['UniqueId'], $loginRadiusProfileData['Provider'], $loginRadiusProfileData['Thumbnail'], $loginRadiusProfileData['Provider'], '' );
                            } else {
                                // traditional account
                                // social linking
                                if ( isset( $loginRadiusSettings['LoginRadius_socialLinking'] ) && ( $loginRadiusSettings['LoginRadius_socialLinking'] == '1' )) {
                                    $lr_common->link_account( $userId, $loginRadiusProfileData['UniqueId'], $loginRadiusProfileData['Provider'], $loginRadiusProfileData['Thumbnail'], $loginRadiusProfileData['Provider'], '' );
                                }
                            }
                            // Login user
                            $login_helper->login_user( $userId, $loginRadiusProfileData, false, false, false );
                        }
                    } else {
                        
                        // create new user
                        $login_helper->register_user( $loginRadiusProfileData, false, false );
                    }
                }
            }
           
        } // Authentication ends

        $comment_post_ID = isset( $_POST['comment_post_ID'] ) ? (int)$_POST['comment_post_ID'] : 0;

        $post = get_post( $comment_post_ID );

        if ( empty( $post->comment_status ) ) {
            echo json_encode(array(
                "Error" => 'comment id not found'
            ));
            die();
        }

        // get_post_status() will get the parent status for attachments.
        $status = get_post_status($post);

        $status_obj = get_post_status_object($status);

        if ( ! comments_open( $comment_post_ID ) ) {
            //Fires when a comment is attempted on a post that has comments closed.
            echo json_encode(array(
                "Error" => 'Comment Id# ' . $comment_post_ID . ' Closed '
            ));
            die();
        } elseif ( 'trash' == $status ) {
            //Fires when a comment is attempted on a trashed post.
            echo json_encode(array(
                "Error" => 'Comment Id# ' . $comment_post_ID . ' has been trashed'
            ));
            die();
        } elseif ( ! $status_obj->public && ! $status_obj->private ) {
            //Fires when a comment is attempted on a post in draft mode.
            echo json_encode(array(
                "Error" => 'Comment Id# ' . $comment_post_ID . ' is in draft mode'
            ));
            die();
        } elseif ( post_password_required( $comment_post_ID ) ) {
            //Fires when a comment is attempted on a password-protected post.
            echo json_encode(array(
                "Error" => 'Comment Id# ' . $comment_post_ID . ' is password protected'
            ));
            die();
        }

        $comment_author = ( isset($_POST['author']) ) ? trim(strip_tags($_POST['author'])) : null;
        $comment_author_email = ( isset($_POST['email']) ) ? trim($_POST['email']) : null;
        $comment_author_url = ( isset($_POST['url']) ) ? trim($_POST['url']) : null;
        $comment_content = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;

        // If the user is logged in
        $user = wp_get_current_user();
        

        if ( $user->exists() ) {

            $user_id = $user->ID;

            if (empty($user->display_name)) {
                $user->display_name = $user->user_login;
            }
            $comment_author = wp_slash($user->display_name);
            $comment_author_email = wp_slash($user->user_email);
            $comment_author_url = wp_slash($user->user_url);

            if ( current_user_can('unfiltered_html') ) {
                if ( ! isset( $_POST['_wp_unfiltered_html_comment'] ) || ! wp_verify_nonce( $_POST['_wp_unfiltered_html_comment'], 'unfiltered-html-comment_' . $comment_post_ID )
                ) {
                    kses_remove_filters(); // start with a clean slate
                    kses_init_filters(); // set up the filters
                }
            }
        } else {
            if ( get_option('comment_registration') || 'private' == $status ) {
                echo json_encode(array(
                    "Error" => _( 'Sorry, you must be logged in to post a comment.' )
                ));
                die();
            }
        }

        $comment_type = '';

        if ( get_option( 'require_name_email' ) && ! $user->exists() ) {
            if ( 6 > strlen( $comment_author_email) || '' == $comment_author )
                $error_msg = 'Please fill the required fields ( name, email ).';
            elseif ( ! is_email( $comment_author_email ) )
                $error_msg = 'Please enter a valid email address.';
        }

        if ( '' == $comment_content || null === $comment_content ) {
            echo json_encode(array(
                "Error" => $lr_commenting_settings['no_comment_msg']
            ));
            die();
        }
        
        // Replace URLs with Short URL
        $comment_content = self::url_shortener( $comment_content );
        // Replace all escaped apostropes
        $comment_content = str_replace( "\'", "'", $comment_content );

        // Post Images
        if ( isset( $_POST['images'] ) && count( $_POST['images'] ) > 0) {

            //Image Upload
            $upload_dir = wp_upload_dir();

            $path = $upload_dir['path'];
            $path_url = $upload_dir['url'];
            $comment_image = "";
            $rel_id = uniqid();

            for ( $i = 0; $i < count( $_POST['images'] ); $i++ ) {

                $data = $_POST['images'][$i];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                $image_id = uniqid();
                $file = $path . '/image-' . $comment_post_ID . '-' . $i . '-' . $image_id . '.png';
                $response = file_put_contents( $file, $data );

                if ($response !== false) {
                    $comment_image .= '<br /><a href="' . $path_url . '/image-' . $comment_post_ID . '-' . $i . '-' . $image_id . '.png?TB_iframe=true&width=600&height=550" class="thickbox" rel="' . $rel_id . '"><img src="' . $path_url . '/image-' . $comment_post_ID . '-' . $i . '-' . $image_id . '.png" class="comment_image"></a>';
                }
            }

            $comment_content = $comment_content . $comment_image;
        }

        $comment_parent = isset( $_POST['comment_parent'] ) ? absint( $_POST['comment_parent'] ) : 0;
        $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_id');
        $comment_id = wp_new_comment($commentdata);

        if ( ! $comment_id ) {
            echo json_encode( array(
                "Error" => _e( 'The comment could not be saved. Please try again later.' )
            ));
            die();
        }

        $lr_provider = get_user_meta( $user->ID, 'loginradius_provider', true );

        // Approve Social Login Comments 
        if ( is_user_logged_in() && isset( $lr_commenting_settings['approve_social_user_comments'] ) && ! empty( $lr_provider ) ) {
           
            wp_set_comment_status( $comment_id, 'approve' );
        }

        // Approve Wordpress Login Comments 
        if ( is_user_logged_in() && isset( $lr_commenting_settings['approve_wp_user_comments'] ) && empty( $lr_provider ) ) {
            
            wp_set_comment_status( $comment_id, 'approve' );
        }

        $comment = get_comment( $comment_id );

        // Post Content to Social Media if Logged In
        $status = strip_tags( $comment_content );
        $post_url = self::url_shortener( get_permalink( $comment->comment_post_ID ) . "#comment-" . $comment->comment_ID );
        $post_title = get_the_title( $comment->comment_post_ID );
        $site_description = get_bloginfo( 'description' );
        $site_caption = get_bloginfo( 'name' );
        $image_url = get_permalink( $comment->comment_post_ID );

        if ( ! empty( $_POST['share_facebook_token'] ) ) {
            // Facebook
            $accessToken = $_POST['share_facebook_token'];
            
            /*
             * Only $accessToken, and $status are required, the other parameters are optional.
             */
            try {
                $facebook_msg = $socialLoginObject->postStatus( $accessToken, $post_title, $post_url, $image_url, $status, $site_caption, $site_description );
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                error_log( 'Facebook Post Status Failure ' . $e->errorResponse->providerErrorResponse );
                $facebook_msg = false;
            }
        }

        if ( ! empty( $_POST['share_twitter_token'] ) ) {
            // Twitter
            $accessToken = $_POST['share_twitter_token'];

            $status = $status . " " . $post_url;

            // Truncate String to 140 chars
            $status = ( strlen($status) > 140 ) ? substr( $status, 0, 140 ) : $status;

            /*
             * Only $accessToken, and $status are required, the other parameters are optional.
             */
            try {
                $twitter_msg = $socialLoginObject->postStatus( $accessToken, $post_title, $post_url, $image_url, $status, $site_caption, $site_description );
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                error_log( 'Twitter Post Status Failure ' . $e->errorResponse->providerErrorResponse );
                $twitter_msg = false;
            }
        }

        if ( ! empty( $_POST['share_linkedin_token'] ) ) {
            // LinkedIn
            $accessToken = $_POST['share_linkedin_token'];
            
            /*
             * Only $accessToken, and $status are required, the other parameters are optional.
             */
            try {
                $linkedin_msg = $socialLoginObject->postStatus( $accessToken, $post_title, $post_url, $image_url, $status, $site_caption, $site_description );
            } catch ( \LoginRadiusSDK\LoginRadiusException $e ) {
                error_log( 'Linkedin Post Status Failure ' . $e->getErrorResponse()->providerErrorResponse );
                $linkedin_msg = false;
            } 
        }

        echo json_encode(array(
            "Facebook_msg" => isset( $facebook_msg ) ? $facebook_msg : '',
            "Twitter_msg" => isset( $twitter_msg ) ? $twitter_msg : '',
            "Linkedin_msg" => isset( $linkedin_msg ) ? $linkedin_msg : '',
            "Message" => isset( $comment_content ) ? $comment_content : '',
            "Error" => $error_msg
        ));

        die();
    }

    /**
     * Custom Commenting theme.
     */
    function loginradius_comment_theme( $comment, $args, $depth ) {
        global $lr_commenting_settings;
        $GLOBALS['comment'] = $comment;
        extract( $args, EXTR_SKIP );

        if ( 'div' == $args['style'] ) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        }
        ?>
        <?php echo '<' . $tag . ' ' ?><?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
        <?php if ( 'div' != $args['style'] ) : ?>
            <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
        <?php endif; ?>
            <div class="comment-author vcard">
            <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
                <?php printf( __( '<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link() ); ?>
            </div>
                <?php   if ( $comment->comment_approved == '0' ) : ?>
                <em class="comment-awaiting-moderation"><?php echo $lr_commenting_settings['moderation_msg']; ?></em>
                <br />
        <?php endif; ?>

            <div class="comment-meta commentmetadata">
                <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
        <?php
        /* translators: 1: date, 2: time */
        printf( __( '%1$s at %2$s' ), get_comment_date(), get_comment_time() );
        ?>
                </a>
                    <?php
                    $id = $comment->comment_ID;
                    $site = urlencode(get_permalink());
                    $template = '<a class="comment-edit-link" href="%1$s%2$s">%3$s</a>';
                    $admin_url = admin_url("comment.php?c=$id&action=");

                    if ( current_user_can( 'moderate_comments' ) ) {

                        edit_comment_link( __( '(Edit)' ), '  ', '' );
                        // Delete.
                        printf( $template, $admin_url, 'cdc', __( '(Delete)' ) );
                        // Mark as Spam.
                        printf( $template, $admin_url, 'cdc&dt=spam', __( '(Spam)' ) );
                    }
                    ?>
            </div>
            <div class="lr-comment-text">
                <?php comment_text(); ?>
            </div>
            <div class="reply">
        <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            </div>
                <?php if ( 'div' != $args['style'] ) : ?>
            </div>
            <?php endif;
    }

    /**
     * Get all comments for post/page.
     */
    function loginradius_get_comments() {
        global $post, $lr_commenting_settings;

        // If the user is logged in
        $user = wp_get_current_user();
        $post_id = $post->ID;

        if ( current_user_can('moderate_comments') ) {
            $comments_args = array(
                'post_id' => $post_id
            );
            $comments = get_comments( $comments_args );
        } else {
            $comments_args = array(
                'post_id' => $post_id,
                'status' => 'approve'
            );
            $comments = get_comments( $comments_args );

            if ( isset( $lr_commenting_settings['enable_moderation_msg'] ) && $lr_commenting_settings['enable_moderation_msg'] == '1' ) {
                $comments_args2 = array(
                    'post_id' => $post_id,
                    'status' => 'hold',
                    'author_email' => $user->user_email
                );
                $comments2 = get_comments( $comments_args2 );

                $comments = array_merge( $comments, $comments2 );
            }
        }

        $args = array(
            'walker' => null,
            'max_depth' => 15,
            'style' => 'ul',
            'callback' => array( $this, 'loginradius_comment_theme' ),
            'end-callback' => null,
            'type' => $lr_commenting_settings['display_comment_type'],
            'reply_text' => 'Reply',
            'page' => '',
            'per_page' => '',
            'avatar_size' => 32,
            'reverse_top_level' => false,
            'reverse_children' => '',
            'format' => 'html5', //or xhtml if no HTML5 theme support
            'short_ping' => false, // @since 3.6,
            'echo' => true // boolean, default is true
        );

        wp_list_comments( $args, $comments );
    }

    /**
     * AJAX function used to get all twitter mentions.
     */
    function loginradius_get_mentions() {
        global $socialLoginObject;
        /*      $apikey = isset($loginradius_api_settings['LoginRadius_apikey'])?$loginradius_api_settings['LoginRadius_apikey']:'';
        $secret = isset($loginradius_api_settings['LoginRadius_secret'])?$loginradius_api_settings['LoginRadius_secret']:'';
        $socialLoginObject = new SocialLoginAPI ($apikey, $secret, array('authentication'=>false, 'output_format' => 'json'));
*/

        if ( isset( $_POST['share_twitter_token'] ) && $_POST['share_twitter_token'] != null ) {
            // Twitter
            $accessToken = $_POST['share_twitter_token'];
            //Get loginradius_get_following
            try {
               $twitter = $socialLoginObject->getFollowing($accessToken); 
            } catch ( Exception $e ) {
                error_log( 'Twitter Get Mentions Error ' . $e->errorResponse->providerErrorResponse );
            }    
        }

        echo json_encode( array(
            "Twitter" => isset( $twitter ) ? $twitter : ''
        ));

        die();
    }
}