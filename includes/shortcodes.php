<?php
/**
 * add shortcode for The Timeshow-generator
 */

add_shortcode('timeshow_generator_sh','timeshow_plugin_shortcode_function');
function timeshow_plugin_shortcode_function(){
    ob_start();

    if(is_user_logged_in()){
        wp_enqueue_style("timeshow-bootstrap-styles");
        wp_enqueue_style("timeshow-bootstrap5-dataTables");
        wp_enqueue_style("timeshow-styles");
        wp_enqueue_script("timeshow-bootstrap_popper-script");
        wp_enqueue_script("timeshow-bootstrap_bundle-script");
        wp_enqueue_script("dataTables-script");
        wp_enqueue_script("bootstrap5-script");
        wp_enqueue_script("timeshow-script");

        require_once(TIME_DIR . '/views/timeshow-shortcode.php');
    }

    return ob_get_clean();
}
