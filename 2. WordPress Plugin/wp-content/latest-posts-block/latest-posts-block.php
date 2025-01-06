<?php
/**
 * Plugin Name: Latest Posts Block
 * Description: A Gutenberg block that displays the latest posts with title, excerpt, and featured image.
 * Version: 1.0
 * Author: Denis Dovganiuc
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

function latest_posts_block_register_block() {
    // Check if the block is already registered to avoid the error.
    if ( ! class_exists( 'WP_Block_Type_Registry' ) || ! WP_Block_Type_Registry::get_instance()->is_registered( 'latest-posts-block/block' ) ) {
        // Register block editor assets.
        wp_enqueue_script(
            'latest-posts-block-js',
            plugin_dir_url( __FILE__ ) . 'build/index.js',
            array( 'wp-blocks', 'wp-element', 'wp-editor' ),
            filemtime( plugin_dir_path( __FILE__ ) . 'build/index.js' ),
            true
        );

        // Register block styles.
        wp_enqueue_style(
            'latest-posts-block-style',
            plugin_dir_url( __FILE__ ) . 'build/style-index.css',
            array(),
            filemtime( plugin_dir_path( __FILE__ ) . 'build/style-index.css' )
        );

        // Register the block with render_callback
        register_block_type( 'latest-posts-block/block', array(
            'editor_script' => 'latest-posts-block-js',
            'editor_style'  => 'latest-posts-block-style',
            'render_callback' => 'latest_posts_block_render_callback',
        ) );
    }
}

add_action( 'init', 'latest_posts_block_register_block' );

// Register REST API endpoint for fetching latest posts.
function latest_posts_block_rest_api() {
    register_rest_route( 'latest-posts/v1', '/posts/', array(
        'methods' => 'GET',
        'callback' => 'latest_posts_block_get_posts',
        'permission_callback' => '__return_true', // Allow public access to this route
    ));
}
add_action( 'rest_api_init', 'latest_posts_block_rest_api' );

// Callback function to return latest posts.
function latest_posts_block_get_posts( $data ) {
    $number_of_posts = isset( $data['number'] ) ? (int) $data['number'] : 5;
    $args = array(
        'posts_per_page' => $number_of_posts,
        'post_status'    => 'publish',
    );
    $posts = get_posts( $args );
    $response = array();

    foreach ( $posts as $post ) {
        $featured_image = get_the_post_thumbnail_url( $post->ID, 'medium' );
        $response[] = array(
            'title'         => $post->post_title,
            'excerpt'       => wp_trim_words( $post->post_excerpt, 20 ),
            'featured_image' => $featured_image ? $featured_image : '',
        );
    }

    return rest_ensure_response( $response );
}

// Render callback for front-end rendering
function latest_posts_block_render_callback( $attributes ) {
    $number_of_posts = isset( $attributes['numberOfPosts'] ) ? $attributes['numberOfPosts'] : 5;
    $args = array(
        'posts_per_page' => $number_of_posts,
        'post_status'    => 'publish',
    );
    $posts = get_posts( $args );
    $output = '<div class="latest-posts-list">';

    foreach ( $posts as $post ) {
        $featured_image = get_the_post_thumbnail_url( $post->ID, 'medium' );
        $output .= '<div class="latest-post-item">';
        if ( $featured_image ) {
            $output .= '<img src="' . esc_url( $featured_image ) . '" alt="' . esc_attr( $post->post_title ) . '" class="post-featured-image" />';
        }
        $output .= '<h3>' . esc_html( $post->post_title ) . '</h3>';
        $output .= '<p>' . esc_html( wp_trim_words( $post->post_excerpt, 20 ) ) . '</p>';
        $output .= '</div>';
    }

    $output .= '</div>';
    return $output;
}