<?php

namespace RHC\includes\publics;

/**
 * This file is the public class of plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


/**
 * The plugin public class.
 *
 * this class used to apply public methods.
 *
 */
class Publics
{
    public function __construct() {}

    public function loadTextdomain()
    {
        load_plugin_textdomain('rhc', false, dirname(plugin_basename(__FILE__), 3) . '/languages/');
    }

    public  function registerPostType()
    {
        if (!post_type_exists('reisetopia_hotel')) {

            // Set labels for news post type
            $labels = array(
                'name'          => esc_html__('Hotels', 'rhc'),
                'singular_name' => esc_html__('Hotel', 'rhc'),
                'search_items'  => esc_html__('Search Hotels', 'rhc'),
                'all_items'     => esc_html__('All Hotels', 'rhc'),
                'parent_item'   => esc_html__('Parent Hotel', 'rhc'),
                'add_new'   => esc_html__('Add New Hotel', 'rhc'),
                'not_found'   => esc_html__('No Hotels Found', 'rhc'),
                'archives'   => esc_html__('Hotels Archive', 'rhc'),
                'insert_into_item'   => esc_html__('Inset into Hotel', 'rhc'),
                'item_link'   => esc_html__('Hotel Link', 'rhc'),
                'edit_item'     => esc_html__('Edit Hotel', 'rhc'),
                'new_item'     => esc_html__('Add New Hotel', 'rhc'),
                'add_new_item'  => esc_html__('Add New Hotel', 'rhc'),
                'view_item'     => esc_html__('View Hotel', 'rhc'),
                'view_items'     => esc_html__('View Hotels', 'rhc'),
                'item_updated'   => esc_html__('Hotel Update', 'rhc'),
                'item_trashed'   => esc_html__('Hotel Trash', 'rhc'),
                'menu_name'     => esc_html__('Hotels', 'rhc')
            );

            // Set main arguments for hotel post type
            $args = array(
                'labels'          => $labels,
                'singular_label'  => esc_html__('hotels', 'rhc'),
                'public'          => true,
                'capability_type' => 'post',
                'show_ui'         => true,
                'show_in_menu'    => true,
                'hierarchical'    => false,
                'menu_position'   => 10,
                'menu_icon'       => 'dashicons-admin-multisite',
                'supports'        => ['title',  'author','thumbnail', 'custom-fields'],
                'rewrite'         => array(
                    'slug' => 'hotels',
                    'with_front' => false
                ),
                // 'has_archive'=>'hotels',
            );

            // Register hotel post type
            register_post_type('reisetopia_hotel', $args);

        }
    }


    /**
     * Enqueue public css file
     */
    public function enqueueCss()
    {

        wp_enqueue_style('rhc-public', RHC_URL . 'assets/css/public-style.css', array(), RHC_VERSION, 'all');
    }

    /**
     * Enqueue public js file
     */
    public function enqueueJs()
    {


        wp_enqueue_script('rhc-public', RHC_URL . 'assets/js/public.js', array('jquery'), RHC_VERSION, true);
    }
}
