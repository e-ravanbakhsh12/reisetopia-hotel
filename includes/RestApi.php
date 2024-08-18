<?php

namespace RHC\includes;

use WP_REST_Request;  // Import the WP_REST_Request class to ensure type safety
use WP_REST_Response; // Import the WP_REST_Response class for API responses

/**
 * This file contains the main REST API class of the plugin.
 * 
 * The class handles the evaluation and display of all REST API operations.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;  // Prevent direct access to the file
}

/**
 * The main REST API class for the plugin.
 */
class RestApi
{
    /**
     * Constructor: Initializes the REST API class.
     * 
     * @return void
     */
    public function __construct() {}

    /**
     * Registers REST API routes for the plugin.
     * 
     * @return void
     */
    public function hotelsEndpoint(): void
    {
        // Register a route for getting the list of hotels (POST method)
        register_rest_route('reisetopia-hotels/v1', '/hotels/', array(
            'methods' => 'POST',
            'callback' => [$this, 'hotelsListCallBack'],
            'permission_callback' => '__return_true',
        ));

        // Register a route for getting a single hotel by ID (GET method)
        register_rest_route('reisetopia-hotels/v1', '/hotels/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'singleHotelsCallBack'],
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Callback function for the 'hotels/' REST API route.
     * 
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response The REST API response object.
     */
    public function hotelsListCallBack(WP_REST_Request $request): WP_REST_Response
    {
        // Decode JSON body of the request
        $params = json_decode($request->get_body(), true);

        // Extract parameters with fallback to defaults
        $name = $params['name'] ?? '';
        $location = $params['location'] ?? '';
        $sorting = $params['sorting'] ?? 'date';
        $order = $params['order'] ?? 'DESC';
        $max_price = $params['max_price'] ?? '';
        $min_price = $params['min_price'] ?? '';
        $page = $params['page'] ?? 1;

        // Query hotels based on parameters
        $query = new Query();
        [$list, $maxNumPages] = $query->getAllHotels([
            'name' => $name,
            'location' => $location,
            'max_price' => $max_price,
            'min_price' => $min_price,
            'sorting' => $sorting,
            'order' => $order,
            'page' => $page,
        ]);

        // If no hotels are found, return a 404 response
        if (empty($list)) {
            return $this->apiResponse(404, esc_html__('No hotels found'));
        }

        // Return the list of hotels with a 200 status code
        return $this->apiResponse(200, esc_html__('Hotels list'), [
            'page' => intval($page),
            'maxNumPages' => $maxNumPages,
            'list' => $list
        ]);
    }

    /**
     * Callback function for the 'hotels/{id}' REST API route.
     * 
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response The REST API response object.
     */
    public function singleHotelsCallBack(WP_REST_Request $request): WP_REST_Response
    {
        // Get the hotel ID from the request parameters
        $id = $request['id'];

        // Query the hotel by ID
        $query = new Query();
        [$hotel, $data] = $query->getHotelById($id);

        // If the hotel is not found or the post type is incorrect, return a 404 response
        if (!$hotel || $hotel->post_type !== 'reisetopia_hotel') {
            return $this->apiResponse(404, esc_html__('Hotel not found'));
        }

        // Return the hotel data with a 200 status code
        return $this->apiResponse(200, esc_html__('Hotel data'), $data);
    }

    /**
     * Utility function to create a standard REST API response.
     * 
     * @param int $status The HTTP status code for the response.
     * @param string $message The message to include in the response.
     * @param mixed $value The data to include in the response.
     * @return WP_REST_Response The REST API response object.
     */
    public function apiResponse(int $status = 200, string $message = '', $value = null): WP_REST_Response
    {
        // Prepare the response array
        $response = array(
            'message' => $message,
            'value' => $value,
        );

        // Return a WP_REST_Response object with the response data and status code
        return new WP_REST_Response($response, $status);
    }
}
