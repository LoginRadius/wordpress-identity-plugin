<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * This class is responsible for creating Social Linking widget.
 */
class LR_Raas_Popup_Menu extends WP_Widget {

    /**
     * Constructor
     * Sets up the widgets name etc
     */
    function __construct() {
        parent::__construct(
            'LR_Raas_Popup_Menu', // Base ID
            __( 'LoginRadius - User Registration Popup Menu', 'lr-plugin-slug' ), // Name
            array( 'classname' => 'widget_meta', 'description' => __( 'Display a Popup Menu Widget', 'lr-plugin-slug' ), ) // Args
        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    function widget( $args, $instance ) {
        global $loginRadiusObject, $loginradius_api_settings;

        $loginRadiusApiKey = isset( $loginradius_api_settings['LoginRadius_apikey'] ) ? trim( $loginradius_api_settings['LoginRadius_apikey'] ) : '';
        $loginRadiusSecret = isset( $loginradius_api_settings['LoginRadius_secret'] ) ? trim( $loginradius_api_settings['LoginRadius_secret'] ) : '';
            
        extract( $args );

        echo $before_widget;

        if ( ! empty( $instance['title'] ) ) {
            $title = apply_filters( 'widget_title', $instance['title'] );
            echo $before_title . $title . $after_title;
        }
        if ( ! empty( $instance['before_widget_content'] ) ) {
            echo $instance['before_widget_content'];
        }
        
        //require_once ABSPATH . 'wp-includes/general-template.php';

        if ( ! $loginRadiusObject->loginradius_is_valid_guid( $loginRadiusApiKey ) || ! $loginRadiusObject->loginradius_is_valid_guid( $loginRadiusSecret ) ) {
            echo "<div style='background-color: #FFFFE0;border:1px solid #E6DB55;padding:5px;'><p style ='color:red;'>Your LoginRadius API key or secret is not valid, please correct it or contact LoginRadius support at <b><a href ='http://www.loginradius.com' target = '_blank'>www.LoginRadius.com</a></b></p></div>";
        } else {
            echo '<div><ul>';
            if ( ! is_user_logged_in() ) {
               echo '<li><a href="#!login" >Login</a></li>';
               echo '<li><a href="#!register" >Register</a></li>';
               echo '<li><a href="#!forgotpassword" >Lost Password</a></li>';
            } else {
                echo '<li><a href="#!changepassword" >Change Password</a></li>';
                echo '<li><a href="' . urldecode( wp_logout_url( get_permalink() ) ) .'" >Logout</a></li>';
            }
            echo '</ul></div>';
        }
        
        if ( ! empty( $instance['after_widget_content'] ) ) {
            echo $instance['after_widget_content'];
        }
        echo $after_widget;
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['before_widget_content'] = $new_instance['before_widget_content'];
        $instance['after_widget_content'] = $new_instance['after_widget_content'];
        return $instance;
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    function form( $instance ) {
        /* Set up default widget settings. */
        $defaults = array('title' => 'Menu', 'before_widget_content' => '', 'after_widget_content' => '');
        foreach ( $instance as $key => $value ) {
            $instance[$key] = esc_attr( $value );
        }
        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'lr-plugin-slug'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
            <label for="<?php echo $this->get_field_id('before_widget_content'); ?>"><?php _e('Before widget content:', 'lr-plugin-slug'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('before_widget_content'); ?>" name="<?php echo $this->get_field_name('before_widget_content'); ?>" type="text" value="<?php echo $instance['before_widget_content']; ?>" />
            <label for="<?php echo $this->get_field_id('after_widget_content'); ?>"><?php _e('After widget content:', 'lr-plugin-slug'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('after_widget_content'); ?>" name="<?php echo $this->get_field_name('after_widget_content'); ?>" type="text" value="<?php echo $instance['after_widget_content']; ?>" />
        </p>
        <?php
    }
}

add_action( 'widgets_init', function(){
     register_widget( 'LR_Raas_Popup_Menu' );
});