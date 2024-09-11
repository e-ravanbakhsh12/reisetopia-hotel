<?php

namespace RHC\includes\publics;

use RHC\includes\Ajax;

/**
 * This file contains the public class of the plugin.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The plugin public class.
 *
 * This class contains methods for handling public-facing functionality.
 */
class Publics
{
    /**
     * Constructor for the Publics class.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Load the text domain for internationalization.
     *
     * This method loads the plugin's text domain for translations.
     *
     * @return void
     */
    public function loadTextdomain(): void
    {
        load_plugin_textdomain('rhc', false, dirname(plugin_basename(__FILE__), 3) . '/languages/');
    }

    /**
     * Register the custom post type for hotels.
     *
     * This method registers the 'reisetopia_hotel' post type with WordPress.
     *
     * @return void
     */
    public function registerPostType(): void
    {
        if (!post_type_exists('reisetopia_hotel')) {

            // Set labels for the hotel post type
            $labels = array(
                'name'               => esc_html__('Hotels', 'rhc'),
                'singular_name'      => esc_html__('Hotel', 'rhc'),
                'search_items'       => esc_html__('Search Hotels', 'rhc'),
                'all_items'          => esc_html__('All Hotels', 'rhc'),
                'parent_item'        => esc_html__('Parent Hotel', 'rhc'),
                'add_new'            => esc_html__('Add New Hotel', 'rhc'),
                'not_found'          => esc_html__('No Hotels Found', 'rhc'),
                'archives'           => esc_html__('Hotels Archive', 'rhc'),
                'insert_into_item'   => esc_html__('Insert into Hotel', 'rhc'),
                'item_link'          => esc_html__('Hotel Link', 'rhc'),
                'edit_item'          => esc_html__('Edit Hotel', 'rhc'),
                'new_item'           => esc_html__('Add New Hotel', 'rhc'),
                'add_new_item'       => esc_html__('Add New Hotel', 'rhc'),
                'view_item'          => esc_html__('View Hotel', 'rhc'),
                'view_items'         => esc_html__('View Hotels', 'rhc'),
                'item_updated'       => esc_html__('Hotel Updated', 'rhc'),
                'item_trashed'       => esc_html__('Hotel Trash', 'rhc'),
                'menu_name'          => esc_html__('Hotels', 'rhc')
            );

            // Set main arguments for the hotel post type
            $args = array(
                'labels'             => $labels,
                'singular_label'     => esc_html__('Hotels', 'rhc'),
                'public'             => true,
                'capability_type'    => 'post',
                'show_ui'            => true,
                'show_in_menu'       => true,
                'hierarchical'       => false,
                'menu_position'      => 10,
                'menu_icon'          => 'dashicons-admin-multisite',
                'supports'           => ['title', 'author', 'thumbnail', 'custom-fields'],
                'rewrite'            => array(
                    'slug'         => 'hotels',
                    'with_front'   => false
                ),
                // 'has_archive'     => 'hotels',
            );

            // Register the hotel post type
            register_post_type('reisetopia_hotel', $args);
        }
    }

    /**
     * Prepare localized data for use in JavaScript.
     *
     * This method creates an array of localized data to be used in JavaScript.
     *
     * @return array The localized data.
     */
    public function localizeArr(): array
    {
        $ajax = new Ajax();
        return [
            'adminAjax'   => admin_url('admin-ajax.php'),
            'homeUrl'     => home_url(),
            'nonce'       => wp_create_nonce($ajax->getNonce()),
            'path'        => RHC_DIR,
        ];
    }

    /**
     * Handle the shortcode for displaying hotels.
     *
     * This method includes the shortcode file for rendering the hotel list.
     *
     * @param array $attrs Shortcode attributes.
     * @return string
     */
    public function reisetopiaHotelsShortcodeHandler(array $attrs = []): string
    {
        ob_start();
        include_once RHC_DIR . '/includes/publics/shortcode.php';
        return ob_get_clean();
    }
}
