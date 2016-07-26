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
 * This is the WordPress way of doing things (code conventions and stuff), and to be honest
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
        // Add the options/settings page
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

        // Add the settings section
        add_settings_section(
            'postageapp_settings',
            'Settings',
            'postageapp_render_uploader_settings_section',
            'postageapp-settings'
        );

        // Add endpoint URL field
        add_settings_field(
            'postageapp_endpoint',
            'PostageApp API URL',
            'postageapp_render_postageapp_endpoint_setting_field',
            'postageapp-settings',
            'postageapp_settings'
        );

        // Add API key field
        add_settings_field(
            'postageapp_key',
            'API Key',
            'postageapp_render_postageapp_api_setting_field',
            'postageapp-settings',
            'postageapp_settings'
        );

        // Add from field
        add_settings_field(
            'postageapp_from',
            'From',
            'postageapp_render_postageapp_from_setting_field',
            'postageapp-settings',
            'postageapp_settings'
        );

        // Add template field
        add_settings_field(
            'postageapp_postage',
            'Template',
            'postageapp_render_postageapp_template_setting_field',
            'postageapp-settings',
            'postageapp_settings'
        );

        // Add reply-to field
        add_settings_field(
            'postageapp_reply',
            'Reply To',
            'postageapp_render_postageapp_reply_setting_field',
            'postageapp-settings',
            'postageapp_settings'
        );

        // Register all settings added before
        register_setting(
            'postageapp-settings',
            'postageapp_endpoint'
        );

        register_setting(
            'postageapp-settings',
            'postageapp_template'
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
    /**
     * Render settings page title
     * @return string
     */
    function postageapp_render_uploader_settings_section() {
        echo 'PostageApp Settings';
    }
}

if ( ! function_exists( 'postageapp_render_postageapp_endpoint_setting_field' ) ) {
    /**
     * Render endpoint url setting field
     * @return string
     */
    function postageapp_render_postageapp_endpoint_setting_field() {

        $setting = esc_attr( get_option( 'postageapp_endpoint' ) );

        echo sprintf(
            '<input type="text" name="postageapp_endpoint" value="%1$s"',
            $setting
        );
    }
}

if ( ! function_exists( 'postageapp_render_postageapp_api_setting_field' ) ) {
    /**
     * Render api key setting field
     * @return string
     */
    function postageapp_render_postageapp_api_setting_field() {

        $setting = esc_attr( get_option( 'postageapp_key' ) );

        echo sprintf(
            '<input type="text" name="postageapp_key" value="%1$s"',
            $setting
        );
    }
}

if ( ! function_exists( 'postageapp_render_postageapp_template_setting_field' ) ) {
    /**
     * Render template setting field
     * @return string
     */
    function postageapp_render_postageapp_template_setting_field() {

        $setting = esc_attr( get_option( 'postageapp_template' ) );

        echo sprintf(
            '<input type="text" name="postageapp_template" value="%1$s"',
            $setting
        );
    }
}

if ( ! function_exists( 'postageapp_render_postageapp_from_setting_field' ) ) {
    /**
     * Render from setting field
     * @return string
     */
    function postageapp_render_postageapp_from_setting_field() {

        $setting = esc_attr( get_option( 'postageapp_from' ) );

        echo sprintf(
            '<input type="text" name="postageapp_from" value="%1$s"',
            $setting
        );
    }
}

if ( ! function_exists( 'postageapp_render_postageapp_reply_setting_field' ) ) {
    /**
     * Render reply-to setting field
     * @return string
     */
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
     * Render the settings form
     * @return string
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

if( ! function_exists( 'add_newsletter_metaboxes' ) ) {
    function add_newsletter_metaboxes() {
        // Testing metabox
        add_meta_box('postage_testing', 'Test', 'postage_testing_metabox', 'postage_newsletter', 'side', 'default');
    }
}

if ( ! function_exists( 'postage_testing_metabox' ) ) {

    function postage_testing_metabox() {
        global $post;

        // TODO
    }
}

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
                'supports' => array( 'title', 'editor', 'excerpt' ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 5,
                'menu_icon' => 'dashicons-email-alt',
                'query_var' => 'newsletter',
                'publicly_queryable' => true,
                'rewrite' => array( 'slug' => 'newsletter', 'with_front' => true ),
            )
        );

        // Add categories to our newsletter, this will be used to fetch the
        // recipients list on the PostageApp API
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

        // If the post is null
        if( is_null( $post ) ) {
            // Grab it from the database
            $post = get_post( $post_ID );

            // If it doesn't exist, we can't do more.
            // This shouldn't happen though
            if( ! $post ) {
                return;
            }
        }

        $endpoint = esc_attr( get_option( 'postageapp_endpoint' ) );
        $key = esc_attr( get_option( 'postageapp_key' ) );

        // We need the key in order to send messages
        // So if it's not set, I'll change the post status back to draft
        // and send an error message
        if( empty( $key ) ) {

            set_transient( get_current_user_id() . '.error', 'Unable to connect to PostageApp, please contact an administrator' );

            wp_update_post(array(
                'ID' => $post_ID,
                'post_status' => 'draft'
            ));

            return;
        }

        $template = esc_attr( get_option( 'postageapp_template' ) );

        // We also need the template name in order to send messages
        // So if it's not set, I'll change the post status back to draft
        // and send an error message
        if( empty( $template ) ) {

            set_transient( get_current_user_id() . '.error', 'Template not set, go to the settings and set the template name or contact an administrator' );

            wp_update_post(array(
                'ID' => $post_ID,
                'post_status' => 'draft'
            ));

            return;
        }

        $postage = new Postage;
        $postage->setHostname( $endpoint ?: $postage->getHostname() )
            ->setKey( $key );

        // Send the message
        // @TODO Parse result in Postage class and return response object
        $r = $postage->mail(
            'ronaldcastillo@gmail.com',
            $post->post_title,
            $template,
            array(
                'From' => esc_attr( get_option( 'postageapp_from' ) ) ?: 'no-reply@example.org',
                'Reply-To' => esc_attr( get_option( 'postageapp_reply' ) ) ?: 'no-reply@example.org'
            ),
            array( // Send the content and title variables to the template
                'content' => nl2br($post->post_content),
                'title' => $post->post_title
            )
        );

        // Did we get an object?
        if( is_object( $r ) ) {
            if($r->response->status == 'ok') {
                // Newsletter sent successfully
                set_transient( get_current_user_id().'.success', 'Newsletter sent successfully' );
            } else {
                // API returned an error
                set_transient( get_current_user_id().'.error', $r->response->message );
            }
        } else {
            // Did we get an object?
            // We didn't, something really wrong happened
            wp_update_post(array(
                'ID' => $post_ID,
                'post_status' => 'draft'
            ));

            set_transient( get_current_user_id().'.error', 'Could not send newsletter, contact an administrator if the problem persists' );
        }
    }
}

if( ! function_exists( 'postageapp_notices' ) ) {
    /**
     * Adds a message to the WordPress administrative panel
     * when something happens (good or bad)
     */
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
    }
}

add_action( 'admin_notices', 'postageapp_notices' );

/**
 * This hook is called when a postage_newsletter
 * custom post type is published. Observe for this event and send newsletter
 */
add_action( 'publish_postage_newsletter', 'postageapp_send_newsletter' );