<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class LoginRadius_Display {

    public function __construct() {
        global $lr_commenting_settings;
        // Set comments_template filter to maximum value to always override the default commenting widget
        if ( isset($lr_commenting_settings['commenting_enable']) && $lr_commenting_settings['commenting_enable'] == "1" ) {
            
              update_option( 'comment_registration', '0');
            add_action( 'wp_enqueue_scripts', array( $this, 'load_commenting_scripts'),100 );
            add_filter('comments_template', array($this, 'loginradius_comments'), 1);
            
            //Defines custom html tags allowed in comments
            add_action('init', array( $this, 'allowed_html_tags_in_comments' ), 10);
        } 
    }

    function allowed_html_tags_in_comments() {
      if( ! defined('CUSTOM_TAGS') ) {
        define('CUSTOM_TAGS', true);
      }
      
      global $allowedtags;
      
      $allowedtags = array(
          'a' => array(
               'href' => array (),
               'title' => array (),
               'class' => array(),
               'rel' => array()),
          'b' => array(),
          'div' => array(),
          'i' => array(),
          'u' => array(),
          'ol' => array(),
          'ul' => array(),
          'li' => array(),
          'blockquote' => array(
               'cite' => array ()),
          'br' => array(),
          'cite' => array (),
          'code' => array(),
          'em' => array(),
          'img' => array(
                'src' => array(),
                'class' => array()
            ),
          'strong' => array(),
      );
    }

    public function loginradius_comments($cmnts) {
        return dirname(__FILE__) . '/loginradius_comments_interface.php';
    }

    static function load_commenting_scripts() {
        global $loginradius_api_settings, $loginRadiusSettings, $lr_commenting_settings, $lr_custom_interface_settings, $lr_js_in_footer;
        wp_enqueue_style( 'lr-form-style', LR_CORE_URL . 'assets/css/lr-form-style.min.css', array(), LR_PLUGIN_VERSION );
        wp_enqueue_style("loginradius-comments-css", LR_COMMENTS_URL . 'assets/css/lr-comments.min.css', array(), LR_PLUGIN_VERSION);

        add_thickbox();
        wp_enqueue_script('lr-sdk');

        $args = array(
            'siteName' => isset($loginradius_api_settings['sitename']) ? $loginradius_api_settings['sitename'] : '',
            'apiKey' => isset($loginradius_api_settings['LoginRadius_apikey']) ? trim($loginradius_api_settings['LoginRadius_apikey']) : '',
            'providers' => isset($lr_custom_interface_settings['selected_providers']) ? $lr_custom_interface_settings['selected_providers'] : ''
        );

        wp_register_script('loginradius-interface-loader', LR_COMMENTS_URL . 'assets/js/lr-interface-loader.min.js', array(), '1.0', $lr_js_in_footer);
        wp_register_script('loginradius-comments', LR_COMMENTS_URL . 'assets/js/lr-comments.min.js', array('jquery', 'jquery-text-editor', 'lr-sdk'), '1.0', $lr_js_in_footer);

        wp_localize_script('loginradius-interface-loader', 'phpvar', $args);

        wp_enqueue_script('lr-custom-interface');

        $commentvar = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'debugging' => isset($loginRadiusSettings['enable_degugging']) ? 'true' : 'false',
            'is_user_logged_in' => is_user_logged_in() === '1' ? 'true' : 'false',
            'empty_msg' => $lr_commenting_settings['no_comment_msg'],
            'image_upload_enable' => isset($lr_commenting_settings['image_upload_enable']) ? 'true' : 'false',
            'sharing_enable' => isset($lr_commenting_settings['sharing_enable']) ? 'true' : 'false',
            'editor_enable' => isset($lr_commenting_settings['editor_enable']) ? 'true' : 'false'
        );

        wp_localize_script('loginradius-comments', 'commentvar', $commentvar);
        ?>
        <!-- Custom Login Template -->
        <script type="text/html" id="commenting_login_interface">
            <a href="javascript:void()" onclick="return $LRIC.util.openWindow('<%=Endpoint%>&is_access_token=true&callback=<?php echo get_permalink(); ?>');">
                <span class="lr-img-icon-<%= Name.toLowerCase() %>" title="sign in with <%= Name%>"></span>
            </a>
        </script>

        <!-- Custom Share Template -->
        <script type="text/html" id="commenting_sharing_interface">
            <label>
                <span id="lr-<%= Name.toLowerCase() %>-share-trigger" onclick="return $LRIC.util.openWindow('<%=Endpoint%>&is_access_token=true&callback=<?php echo get_permalink(); ?>');" ></span>
                <span class="lr-img-icon-<%= Name.toLowerCase() %> share" title="Share with "></span>
                <input type="checkbox" class="lr-share-switch" name="<%= Name.toLowerCase() %>" id="lr-<%= Name.toLowerCase() %>-share-switch" />
            </label>
        </script>

        <!-- Custom Required Template -->
        <script type="text/html" id="commenting_required_interface">
            <a href="javascript:void()" onclick="return $LRIC.util.openWindow('<%=Endpoint%>&is_access_token=true&callback=<?php echo get_permalink(); ?>');">
                <span class="lr-img-icon-<%= Name.toLowerCase() %> required-login" title="sign in with <%= Name%>"></span>
            </a>
        </script>
        <?php
        wp_enqueue_script('loginradius-interface-loader');

        wp_enqueue_script("jquery-text-editor", LR_COMMENTS_URL . 'assets/js/jQuery-TE_v.1.4.0/jquery-te-1.4.0.min.js', array('jquery'), '1.4.0', $lr_js_in_footer);
        wp_enqueue_script('loginradius-comments');
    }

}

new LoginRadius_Display();
