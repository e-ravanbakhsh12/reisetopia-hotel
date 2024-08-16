<?php

namespace RHC\includes;

use RHC\includes\publics\Publics;
use RHC\includes\RestApi;
use RHC\includes\Ajax;

/**
 * This file is the maine class of plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


/**
 * The main plugin class.
 *
 * this class used to add action and filter hooks.
 *
 */
class RHC
{

    /**
     * execute front-end and backend hooks
     */
    function __construct()
    {

        $this->setRestApiHooks();
        $this->setPublicHooks();
        $this->setAjaxHooks();
    }


    /**
     * loading RestApi hooks needed for plugin
     */
    public function setRestApiHooks()
    {

        $restApi = new RestApi();
        add_action('rest_api_init', [$restApi, 'hotelsEndpoint']);
    }

    /**
     * loading Ajax hooks needed for plugin
     */
    public function setAjaxHooks()
    {

        $ajax = new Ajax();
        add_action('wp_ajax_reisetopia_hotels_get_all', [ $ajax,'getHotelsList']);
        add_action('wp_ajax_nopriv_reisetopia_hotels_get_all', [ $ajax,'getHotelsList']);
        add_action('wp_ajax_reisetopia_hotels_get_by_id', [ $ajax,'getHotelById']);
        add_action('wp_ajax_nopriv_reisetopia_hotels_get_by_id', [ $ajax,'getHotelById']);
    }

    /**
     * loading public hooks needed for plugin
     */
    public function setPublicHooks()
    {
        $publics = new Publics();
        add_action('wp_loaded', [$publics, 'loadTextdomain']);
        add_action('wp_enqueue_scripts', [$publics, 'enqueueCss']);
        add_action('wp_enqueue_scripts', [$publics, 'enqueueJs']);
        add_action('init', [$publics, 'registerPostType']);
    }
    
}
