<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * This class is responsible for creating Social Login widget.
 */
class LR_Social_Login_Widget extends WP_Widget {

    /**
     * Constructor
     * Sets up the widgets name etc
     */
    function __construct() {
        parent::__construct(
            'LoginRadius', // Base ID
            __( 'LoginRadius - Social Login', 'lr-plugin-slug' ), // Name
            array( 'description' => __( 'Login or register with Facebook, Twitter, Yahoo, Google and many more', 'lr-plugin-slug' ), ) // Args
        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    function widget($args, $instance) {
        extract($args);

        if ($instance['hide_for_logged_in'] == 1 && is_user_logged_in()) {
            return;
        }
        echo $before_widget;

        if (!empty($instance['title']) && !is_user_logged_in()) {
            $title = apply_filters('widget_title', $instance['title']);
            echo $before_title . $title . $after_title;
        }
        if (!empty($instance['before_widget_content'])) {
            echo $instance['before_widget_content'];
        }
        if (!class_exists("Login_Radius_Widget_Helper")) {
            require_once LOGINRADIUS_PLUGIN_DIR.'widgets/class-widgets-helper.php';
        }
        Login_Radius_Widget_Helper:: login_radius_widget_connect_button();

        if (!empty($instance['after_widget_content'])) {
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
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['before_widget_content'] = $new_instance['before_widget_content'];
        $instance['after_widget_content'] = $new_instance['after_widget_content'];
        $instance['hide_for_logged_in'] = $new_instance['hide_for_logged_in'];

        return $instance;
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    function form($instance) {
        //Set up default widget settings
        $defaults = array('title' => 'Social Login', 'before_widget_content' => '', 'after_widget_content' => '');

        foreach ($instance as $key => $value) {
            $instance[$key] = esc_attr($value);
        }
        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'lr-plugin-slug'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
            <label for="<?php echo $this->get_field_id('before_widget_content'); ?>"><?php _e('Before widget content:', 'lr-plugin-slug'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('before_widget_content'); ?>" name="<?php echo $this->get_field_name('before_widget_content'); ?>" type="text" value="<?php echo $instance['before_widget_content']; ?>" />
            <label for="<?php echo $this->get_field_id('after_widget_content'); ?>"><?php _e('After widget content:', 'lr-plugin-slug'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('after_widget_content'); ?>" name="<?php echo $this->get_field_name('after_widget_content'); ?>" type="text" value="<?php echo $instance['after_widget_content']; ?>" />
            <br /><br /><label for="<?php echo $this->get_field_id('hide_for_logged_in'); ?>"><?php _e('Hide for logged in users:', 'lr-plugin-slug'); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id('hide_for_logged_in'); ?>" name="<?php echo $this->get_field_name('hide_for_logged_in'); ?>" type="text" value='1' <?php if (isset($instance['hide_for_logged_in']) && $instance['hide_for_logged_in'] == 1) echo 'checked="checked"'; ?> />
        </p>
        <?php
    }

}

add_action( 'widgets_init', function(){
     register_widget( 'LR_Social_Login_Widget' );
});
