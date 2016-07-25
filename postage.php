<?php

/*
Plugin Name: Postage
Plugin URI: https://github.com/ronaldcastillo/postage/
Description: PostageApp 
Version: 0.1.0
Author: Ronald Castillo
Author URI: https://github.com/ronaldcastillo/
*/

require_once 'src/Postage.class.php';

/**
 * This is the WordPress way of doing things, and to be honest
 * I'm not fond of it, but, let's stick with it for the moment
 */

/*
------------------------------
-- PostageApp plugin settings
------------------------------
*/

if ( ! function_exists( 'postageapp_menu' ) ) {
    /**
     * Add PostageApp menu link
     */
    function postageapp_menu() {
        add_options_page(
            'PostageApp',
            'PostageApp',
            'manage_options',
            'postageapp-settings',
            'postageapp_settings'
        );
    }
}

if ( ! function_exists( 'postageapp_register_options' ) ) {
    function postageapp_register_options() {

        add_settings_section(
            'postageapp_settings',
            'Settings',
            'postageapp_render_uploader_settings_section',
            'postageapp-settings'
        );

        add_settings_field(
            'postageapp_endpoint',
            'PostageApp API URL',
            'postageapp_render_postageapp_endpoint_setting_field',
            'postageapp-settings',
            'postageapp_settings'
        );

        add_settings_field(
            'postageapp_key',
            'API Key',
            'postageapp_render_postageapp_api_setting_field',
            'postageapp-settings',
            'postageapp_settings'
        );

        add_settings_field(
            'postageapp_from',
            'From',
            'postageapp_render_postageapp_from_setting_field',
            'postageapp-settings',
            'postageapp_settings'
        );

        add_settings_field(
            'postageapp_reply',
            'Reply To',
            'postageapp_render_postageapp_reply_setting_field',
            'postageapp-settings',
            'postageapp_settings'
        );

        register_setting(
            'postageapp-settings',
            'postageapp_endpoint'
        );

        register_setting(
            'postageapp-settings',
            'postageapp_key'
        );

        register_setting(
            'postageapp-settings',
            'postageapp_from'
        );

        register_setting(
            'postageapp-settings',
            'postageapp_reply'
        );
    }
}

if ( ! function_exists( 'postageapp_render_uploader_settings_section' ) ) {
    function postageapp_render_uploader_settings_section() {
        echo 'PostageApp Settings';
    }
}

if ( ! function_exists( 'postageapp_render_postageapp_endpoint_setting_field' ) ) {
    function postageapp_render_postageapp_endpoint_setting_field() {

        $setting = esc_attr( get_option( 'postageapp_endpoint' ) );

        echo sprintf(
            '<input type="text" name="postageapp_endpoint" value="%1$s"',
            $setting
        );
    }
}

if ( ! function_exists( 'postageapp_render_postageapp_api_setting_field' ) ) {
    function postageapp_render_postageapp_api_setting_field() {

        $setting = esc_attr( get_option( 'postageapp_key' ) );

        echo sprintf(
            '<input type="text" name="postageapp_key" value="%1$s"',
            $setting
        );
    }
}

if ( ! function_exists( 'postageapp_render_postageapp_from_setting_field' ) ) {
    function postageapp_render_postageapp_from_setting_field() {

        $setting = esc_attr( get_option( 'postageapp_from' ) );

        echo sprintf(
            '<input type="text" name="postageapp_from" value="%1$s"',
            $setting
        );
    }
}

if ( ! function_exists( 'postageapp_render_postageapp_reply_setting_field' ) ) {
    function postageapp_render_postageapp_reply_setting_field() {

        $setting = esc_attr( get_option( 'postageapp_reply' ) );

        echo sprintf(
            '<input type="text" name="postageapp_reply" value="%1$s"',
            $setting
        );
    }
}

if ( ! function_exists( 'postageapp_settings' ) ) {
    /**
     *
     */
    function postageapp_settings() {
        ?>
        <form method="POST" action="options.php"><?php
        settings_fields( 'postageapp-settings', 'postageapp_settings' );
        do_settings_sections( 'postageapp-settings' );
        submit_button();
        ?></form><?php
    }
}

add_action( 'admin_menu', 'postageapp_menu' );

add_action( 'admin_init', 'postageapp_register_options' );

/*
--------------------------------
-- PostageApp custom post types
--------------------------------
*/

if ( ! function_exists( 'postageapp_create_newsletter_post_type' ) ) {
    function postageapp_create_newsletter_post_type() {
        // The CPT name can't be longer than 20 characters or it won't show
        // Seriously ?
        register_post_type( 'postage_newsletter',
            array(
                'labels' => array(
                    'name' => __( 'Newsletters', 'postageapp' ),
                    'singular_name' => __( 'Newsletter', 'postageapp' )
                ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 5,
                'menu_icon' => 'dashicons-email-alt',
                'query_var' => 'newsletter'
            )
        );

        register_taxonomy_for_object_type( 'category', 'postage_newsletter' );
    }
}

add_action( 'init', 'postageapp_create_newsletter_post_type' );

if ( ! function_exists( 'postageapp_send_newsletter' ) ) {
    /**
     *
     * Fires once a post has been saved.
     * Sends the newsletter to the defined list.
     *
     * @param int $post_ID Post ID.
     * @param WP_Post $post Post object.
     * @param bool $update Whether this is an existing post being updated or not.
     */
    function postageapp_send_newsletter( $post_ID = null, $post = null ) {
        /**
         * I check for the following:
         *  - It has to be a new post (won't send updates)
         *  - Can't send post revisions (duh)
         *  - cURL is enabled
         *  - They are in fact saving a newsletter instead of a regular post or other custom post type
         */

        if( is_null( $post ) ) {
            $post = get_post( $post_ID );
        }

        $endpoint = esc_attr( get_option( 'postageapp_endpoint' ) );
        $key = esc_attr( get_option( 'postageapp_key' ) );

        if( empty($key)) {

            set_transient( get_current_user_id().'.error', 'Unable to connect to PostageApp, please contact an administrator' );

            wp_update_post(array(
                'ID' => $post_ID,
                'post_status' => 'draft'
            ));

            return;
        }

        $postage = new Postage;
        $postage->setHostname( $endpoint ?: $postage->getHostname() )
            ->setKey( $key );

        $r = $postage->mail(
            'ronaldcastillo@gmail.com',
            $post->post_title,
            array(
                'text/plain' => nl2br($post->post_content),
                'text/html' => nl2br($post->post_content)
            ),
            array(
                'From' => esc_attr( get_option( 'postageapp_from' ) ) ?: 'no-reply@example.org',
                'Reply-To' => esc_attr( get_option( 'postageapp_reply' ) ) ?: 'no-reply@example.org'
            )
        );

        if( is_object($r) && $r->response->status === 'ok') {
            set_transient( get_current_user_id().'.success', 'Newsletter sent successfully' );
        } else {

            wp_update_post(array(
                'ID' => $post_ID,
                'post_status' => 'draft'
            ));

            set_transient( get_current_user_id().'.error', 'Could not send newsletter, contact an administrator if the problem persists' );
        }
    }
}

function postageapp_notices() {
    if( $out = get_transient( get_current_user_id().'.error' ) ) {
        delete_transient( get_current_user_id() . '.error' );
        ?>
        <div class="error notice">
            <p><?php _e( $out, 'postageapp' ); ?></p>
        </div>
        <?php
    } elseif( $out = get_transient( get_current_user_id().'.success' ) ) {
        delete_transient( get_current_user_id() . '.success' );
        ?>
        <div class="updated notice">
            <p><?php _e( $out, 'postageapp' ); ?></p>
        </div>
        <?php
    }
    // return false; // nothing to return here
}

add_action( 'admin_notices', 'postageapp_notices' );

add_action( 'publish_postage_newsletter', 'postageapp_send_newsletter' );