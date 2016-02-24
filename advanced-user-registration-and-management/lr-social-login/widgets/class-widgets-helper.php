<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if ( ! class_exists( 'Login_Radius_Widget_Helper' ) ) {

    class Login_Radius_Widget_Helper {

        /**
         * Display social login interface in widget area.
         */
        public static function login_radius_widget_connect_button() {
            global $loginRadiusSettings, $user_ID;
            if ( ! is_user_logged_in() ) {
                Login_Helper::get_loginradius_interface_container();
            }
            // On user Login show user details.
            if ( is_user_logged_in() && ! is_admin() ) {

                $size = '60';
                $user = get_userdata( $user_ID );
                $currentSocialId = get_user_meta( $user_ID, 'loginradius_current_id', true );
                // hold the value of avatar option

                echo "<div style='height:80px;width:180px'><div style='width:63px;float:left;'>";
                echo @get_avatar( $user_ID, $size, $default, $alt );
                echo "</div><div style='width:100px; float:left; margin-left:10px'>";
                
                // username separator
                if ( ! isset( $loginRadiusSettings['username_separator'] ) || $loginRadiusSettings['username_separator'] == 'dash' ) {
                    echo $user->user_login;
                } elseif ( isset( $loginRadiusSettings['username_separator']) && $loginRadiusSettings['username_separator'] == 'dot' ) {
                    echo str_replace( '-', '.', $user->user_login );
                } else {
                    echo str_replace( '-', ' ', $user->user_login );
                }
                if ( isset( $loginRadiusSettings['LoginRadius_loutRedirect'] ) && $loginRadiusSettings['LoginRadius_loutRedirect'] == 'custom' && ! empty( $loginRadiusSettings['custom_loutRedirect'] ) ) {
                    $redirect = htmlspecialchars( $loginRadiusSettings['custom_loutRedirect'] );
                } else {
                    $redirect = home_url();
                    ?>
                    <?php
                }
                ?>
                <br/>
                <a href="<?php echo wp_logout_url($redirect); ?>"><?php _e( 'Log Out', 'lr-plugin-slug' ); ?></a></div></div><?php
            }
        }

    }

}