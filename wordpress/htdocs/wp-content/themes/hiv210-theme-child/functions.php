<?php
function my_force_login() {
global $post;

    if (!is_user_logged_in()) {
        //auth_redirect();
        wp_redirect( site_url('login') );
        exit;
    }
}
add_filter('push_force_login', 'my_force_login');
 
 

wp_enqueue_style( 'custom-css', site_url() . '/wp-content/themes/hiv210-theme-child/style.css', null, null, 'all');
wp_enqueue_script( 'script', site_url() . '/wp-content/themes/hiv210-theme-child/custom.js', array ( 'jquery' ), 1.1, true);