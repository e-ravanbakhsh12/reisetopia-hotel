<?php

namespace RHC\includes;

use RHC\includes\publics\Publics;
use RHC\includes\RestApi;
use RHC\includes\Ajax;
use RHC\includes\RewriteApi;

/**
 * This file contains the main class of the plugin.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The main plugin class.
 *
 * This class is used to add action and filter hooks.
 */
class RHC
{
    /**
     * Constructor: Initializes the plugin by setting up hooks.
     *
     * @return void
     */
    public function __construct()
    {
        // Set up REST API hooks
        $this->setRestApiHooks();

        // Set up public-facing hooks
        $this->setPublicHooks();

        // Set up AJAX hooks
        $this->setAjaxHooks();

        // set Rewrite Api hooks
        $this->setRewriteApiHooks();
    }

    /**
     * Set up REST API hooks needed for the plugin.
     *
     * @return void
     */
    public function setRestApiHooks(): void
    {
        // Instantiate the RestApi class
        $restApi = new RestApi();
        // Register the REST API endpoint
        add_action('rest_api_init', [$restApi, 'hotelsEndpoint']);
    }

    /**
     * Set up AJAX hooks needed for the plugin.
     *
     * @return void
     */
    public function setAjaxHooks(): void
    {
        // Instantiate the Ajax class
        $ajax = new Ajax();

        // Register AJAX actions for authenticated users
        add_action('wp_ajax_reisetopia_hotels_get_all', [$ajax, 'getHotelsList']);
        add_action('wp_ajax_reisetopia_hotels_get_by_id', [$ajax, 'getHotelById']);

        // Register AJAX actions for unauthenticated users
        add_action('wp_ajax_nopriv_reisetopia_hotels_get_all', [$ajax, 'getHotelsList']);
        add_action('wp_ajax_nopriv_reisetopia_hotels_get_by_id', [$ajax, 'getHotelById']);
    }

    
    /**
     * Set up Rewrite AJAX hooks needed for the plugin.
     *
     * @return void
     */
    public function setRewriteApiHooks(): void
    {
        $rewriteApi = new RewriteApi();
        add_filter('query_vars', [$rewriteApi, 'addQueryVars']);
        add_action('init', [$rewriteApi, 'defineAjaxRoute']);
        add_action('template_redirect', [$rewriteApi, 'ajaxHandler']);
    }

    /**
     * Set up public-facing hooks needed for the plugin.
     *
     * @return void
     */
    public function setPublicHooks(): void
    {
        // Instantiate the Publics class
        $publics = new Publics();

        // Load the text domain for translation
        add_action('wp_loaded', [$publics, 'loadTextdomain']);

        // Register custom post type
        add_action('init', [$publics, 'registerPostType']);

        // Register a shortcode for displaying hotel listings
        add_shortcode('reisetopia-hotels', [$publics, 'reisetopiaHotelsShortcodeHandler']);
    }
}
