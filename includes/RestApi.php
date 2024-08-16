<?php

namespace RHC\includes;

use WP_REST_Response;

/**
 * This file is the maine class of rest api of plugin
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


/**
 * The main plugin rest api class.
 *
 * this class used to evaluate and display all thing about rest api
 *
 */
class RestApi
{


    public function __construct() {}




    public function hotelsEndpoint()
    {
        register_rest_route('reisetopia-hotels/v1', '/hotels/', array(
            'methods' => 'POST',
            'callback' => [$this, 'hotelsListCallBack'],
            'permission_callback' => '__return_true',
        ));
        register_rest_route('reisetopia-hotels/v1', '/hotels/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'singleHotelsCallBack'],
            'permission_callback' => '__return_true',
        ));
    }

    public function hotelsListCallBack($request)
    {
        $name = $request->get_param('name') ?: '';
        $location = $request->get_param('location') ?: '';
        $max_price = $request->get_param('max_price') ?: '';
        $query = new Query();
        $data = $query->getAllHotels($name, $location, $max_price);

        if (empty($data)) {
            return   $this->apiResponse(404, 'No hotels found');
        }

        return $this->apiResponse(200, 'hotels list', $data);
    }

    public function singleHotelsCallBack($request)
    {
        $id = $request['id'];
        $query = new Query();
        [$hotel, $data] = $query->getHotelById($id);
        if (!$hotel || $hotel->post_type !== 'reisetopia_hotel') {
            return $this->apiResponse(404, 'Hotel not found');
        }

        return $this->apiResponse(200, 'hotel data', $data);
    }


    public  function apiResponse($status = 200, $message = '', $value = null)
    {

        $response = array(
            'message' => $message,
            'value' => $value,
        );

        return new WP_REST_Response($response, $status);
    }
}
