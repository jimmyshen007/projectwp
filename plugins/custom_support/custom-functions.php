<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 1/31/17
 * Time: 4:11 PM
 */

// setup a function to check if these pages exist
function the_slug_exists($post_name) {
    global $wpdb;
    if($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A')) {
        return true;
    } else {
        return false;
    }
}

function create_custom_page($page_name, $page_title, $page_template='page_populars.php',
    $page_content=''){
    // create the blog page
    if (!is_admin()){
        $page_check = get_page_by_title($page_title);
        $page = array(
            'post_type' => 'page',
            'post_title' => $page_title,
            'post_content' => $page_content,
            'post_status' => 'publish',
            'post_author' => 1,
            'post_slug' => $page_name
        );
        if(!isset($page_check->ID) && !the_slug_exists($page_name)){
            $page_id = wp_insert_post($page);
            add_post_meta( $page_id, '_wp_page_template', 'page-templates/' . $page_template);
        }
    }
}
