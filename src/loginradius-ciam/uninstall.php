<?php

//if uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

if ( ! is_multisite() ) {
    delete_loginradius_options();
} else {
    global $wpdb;
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    $original_blog_id = get_current_blog_id();
    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        delete_loginradius_options();
    }
    switch_to_blog( $original_blog_id );
}

function delete_loginradius_options() {
    global $wpdb;
    $Ciam_uninstall_settings = get_option( 'Ciam_uninstall_settings' );
    if ( $Ciam_uninstall_settings['delete_options'] == '1' ) {

        delete_option( 'ciam_api_settings' ); 
        delete_option( 'ciam_authentication_settings' ); 
        delete_option( 'ciam_uninstall_settings' ); 
  
        $wpdb->query( "delete from $wpdb->usermeta where meta_key like 'loginradius%'" );
    }
}