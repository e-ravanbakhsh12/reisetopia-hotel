<?php

namespace RHC\includes;

use WP_Query;

/**
 * This file contains the main query class of the plugin.
 * 
 * The class handles querying hotel data from the WordPress database.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;  // Prevent direct access to the file
}

/**
 * The main plugin query class.
 *
 * This class is used to query and retrieve hotel data.
 */
class Query
{
    /**
     * Retrieves a list of hotels based on the specified attributes.
     *
     * @param array $attrs The attributes for querying hotels.
     * - name (string): The name of the hotel.
     * - location (string): The location of the hotel (city or country).
     * - max_price (string|int): The maximum price for filtering.
     * - min_price (string|int): The minimum price for filtering.
     * - sorting (string): The sorting criteria (default: 'date').
     * - order (string): The sorting order ('ASC' or 'DESC').
     * - page (int): The page number for pagination (default: 1).
     *
     * @return array An array containing:
     * - array $return: The list of hotels.
     * - int $result->max_num_pages: The maximum number of pages available.
     */
    public function getAllHotels(array $attrs = []): array
    {
        // Default attributes
        $defaultAttrs = [
            'name' => '',
            'location' => '',
            'max_price' => '',
            'min_price' => '',
            'sorting' => 'date',
            'order' => 'DESC',
            'page' => 1,
        ];

        // Merge provided attributes with defaults
        $attrs = wp_parse_args($attrs, $defaultAttrs);

        $return = [];  // Initialize the return array

        // Set up the query arguments
        $args = array(
            'post_type' => 'reisetopia_hotel',
            'posts_per_page' => 10,
            'paged' => $attrs['page'],
            'order' => $attrs['order'],
            'orderby' => in_array($attrs['sorting'], ['price_range_max', 'price_range_min']) ? 'meta_value_num' : $attrs['sorting'],
        );

        // Add meta key for sorting by price
        if (in_array($attrs['sorting'], ['price_range_max', 'price_range_min'])) {
            $args['meta_key'] = $attrs['sorting'];
        }

        // Add search by hotel name
        if (!empty($attrs['name'])) {
            $args['s'] = $attrs['name'];
        }

        // Add meta query for location filtering
        if (!empty($attrs['location'])) {
            $args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key' => 'city',
                    'value' => $attrs['location'],
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => 'country',
                    'value' => $attrs['location'],
                    'compare' => 'LIKE',
                ),
            );
        }

        // Add meta query for max price filtering
        if (!empty($attrs['max_price'])) {
            $args['meta_query'][] = array(
                'key' => 'price_range_max',
                'value' => $attrs['max_price'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            );
        }

        // Add meta query for min price filtering
        if (!empty($attrs['min_price'])) {
            $args['meta_query'][] = array(
                'key' => 'price_range_min',
                'value' => $attrs['min_price'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }

        // Execute the query
        $result = new WP_Query($args);

        // If there are posts, build the return array
        if ($result->have_posts()) {
            foreach ($result->posts as $hotel) {
                $country = get_post_meta($hotel->ID, 'country', true);
                $city = get_post_meta($hotel->ID, 'city', true);
                $priceMax = get_post_meta($hotel->ID, 'price_range_max', true);
                $priceMin = get_post_meta($hotel->ID, 'price_range_min', true);
                $rate = get_post_meta($hotel->ID, 'rating', true);
                $return[] = [
                    'id' => $hotel->ID,
                    'name' => $hotel->post_title,
                    'city' => $city,
                    'country' => $country,
                    'priceRange' => ['min' => $priceMin, 'max' => $priceMax],
                    'rate' => $rate,
                    'link' => get_permalink($hotel->ID),
                    'img' => get_the_post_thumbnail_url($hotel->ID, 'post-thumbnail'),
                ];
            }
            wp_reset_postdata();  // Reset the global post data after the query
        }

        // Return the list of hotels and the max number of pages
        return [$return, $result->max_num_pages];
    }

    /**
     * Retrieves a list of hotels based on the specified attributes.
     *
     * @param array $attrs The attributes for querying hotels.
     * - params (array): The meta keys to retrieve (e.g., ['country', 'city', 'price_range_max', 'price_range_min', 'rating']).  
     * - name (string): The name of the hotel.  
     * - location (string): The location of the hotel (city or country).  
     * - max_price (string|int): The maximum price for filtering.  
     * - min_price (string|int): The minimum price for filtering.  
     * - status (string): The post status (default: 'publish').  
     * - sorting (string): The sorting criteria (default: 'date').  
     * - order (string): The sorting order ('ASC' or 'DESC').  
     * - page (int): The page number for pagination (default: 1).  
     * - items (int): The items fetch on query (default: 10).  
     *
     * @return array An array containing:
     * - array $return: The list of hotels.
     * - int $result->max_num_pages: The maximum number of pages available.
     */
    public function getAllHotels2(array $args = []): array
    {
        // Default attributes  
        $defaultArgs = [
            'params' => ['country', 'city', 'price_range_max', 'price_range_min', 'rating'],
            'name' => '',
            'location' => '',
            'max_price' => '',
            'min_price' => '',
            'status' => 'publish',
            'sorting' => 'date',
            'order' => 'DESC',
            'page' => 1,
            'items' => 10,
        ];

        // Merge provided attributes with defaults  
        $args = wp_parse_args($args, $defaultArgs);
        global $wpdb;

        $join = '';
        $metaValue = '';
        $conditions = [];
        $orderby = '';
        $prepareParams = [$args['status']];

        if (is_array($args['params']) && count($args['params'])) {
            for ($i = 1; $i <= count($args['params']); $i++) {
                $metaValue .= ", pm{$i}.meta_value as {$args['params'][$i-1]}";
                $join .= "  
                LEFT JOIN {$wpdb->prefix}postmeta pm{$i} ON p.ID = pm{$i}.post_id AND pm{$i}.meta_key = '{$args['params'][$i-1]}'  
            ";
            }
        }

        switch ($args['sorting']) {
            case 'date':
                $orderby = 'ORDER BY p.post_date';
                break;
            case 'name':
                $orderby = "ORDER BY p.post_title";
                break;
            case 'max_price':
                $orderby = "ORDER BY pm3.meta_value";
                break;
            case 'min_price':
                $orderby = "ORDER BY pm4.meta_value";
                break;
            default:
                $orderby = 'ORDER BY p.post_date';
                break;
        }

        // Adding filters for price range  
        if (!empty($args['min_price'])) {
            $conditions[] = 'pm4.meta_value >= %d'; // assuming pm4 for min_price  
            $prepareParams[] = $args['min_price'];
        }
        if (!empty($args['max_price'])) {
            $conditions[] = 'pm3.meta_value <= %d'; // assuming pm3 for max_price  
            $prepareParams[] = $args['max_price'];
        }
        // Adding filter for name  
        if (!empty($args['name'])) {
            $conditions[] = "p.post_title LIKE %s";
            $prepareParams[] = '%' . $wpdb->esc_like($args['name']) . '%';
        }

        // Adding filter for location  
        if (!empty($args['location'])) {
            $conditions[] = "(pm1.meta_value LIKE %s OR pm2.meta_value LIKE %s)";
            $escapedLocation = '%' . $wpdb->esc_like($args['location']) . '%';
            $prepareParams[] = $escapedLocation;
            $prepareParams[] = $escapedLocation;
        }

        // Get total hotel  
        $totalQuery = "  
        SELECT COUNT(DISTINCT p.ID)   
        FROM {$wpdb->prefix}posts p  
        {$join}  
        WHERE p.post_type = 'reisetopia_hotel' AND p.post_status = %s";

        if ($conditions) {
            $totalQuery .= ' AND ' . implode(' AND ', $conditions);
        }

        $totalHotels = $wpdb->get_var($wpdb->prepare($totalQuery, ...array_values($prepareParams)));

        // Pagination  
        $offset = ($args['page'] - 1) * $args['items'];
        $maxNumPages = ceil($totalHotels / $args['items']);
        $prepareParams[] = $offset;
        $prepareParams[] = $args['items'];

        // Main Query  
        $query = "  
        SELECT DISTINCT p.* {$metaValue}   
        FROM {$wpdb->prefix}posts p  
        {$join}  
        WHERE p.post_type = 'reisetopia_hotel' AND p.post_status = %s";

        if ($conditions) {
            $query .= ' AND ' . implode(' AND ', $conditions);
        }

        $query .= " GROUP BY p.ID {$orderby} {$args['order']} LIMIT %d, %d";

        // Prepare the query with the correct order of parameters  
        $results = $wpdb->get_results($wpdb->prepare($query, ...array_values($prepareParams)));
        // $debug =  [$wpdb->last_query,$wpdb->error];
        // Initialize return array  
        $return = [];

        // If there are posts, build the return array  
        if ($results) {
            foreach ($results as $hotel) {
                $return[] = [
                    'id' => $hotel->ID,
                    'name' => $hotel->post_title,
                    'city' => $hotel->city,
                    'country' => $hotel->country,
                    'priceRange' => ['min' => $hotel->price_range_min, 'max' => $hotel->price_range_max],
                    'rate' => $hotel->rating,
                    'link' => get_permalink($hotel->ID),
                    'img' => get_the_post_thumbnail_url($hotel->ID, 'post-thumbnail'),
                ];
            }
        }

        // Return the list of hotels and the max number of pages  
        return [$return, $maxNumPages];
    }

    /**
     * Retrieves a single hotel's data by its ID.
     *
     * @param int $id The ID of the hotel.
     *
     * @return array An array containing:
     * - WP_Post|null $hotel: The hotel post object, or null if not found.
     * - array $data: The hotel's data.
     */
    public function getHotelById(int $id): array
    {
        $data = [];  // Initialize the data array
        $hotel = get_post($id);  // Get the post by ID

        // Check if the post exists and is of the correct post type
        if ($hotel && $hotel->post_type == 'reisetopia_hotel') {
            $country = get_post_meta($id, 'country', true);
            $city = get_post_meta($id, 'city', true);
            $priceMax = get_post_meta($id, 'price_range_max', true);
            $priceMin = get_post_meta($id, 'price_range_min', true);
            $rate = get_post_meta($id, 'rating', true);
            $data = [
                'id' => $id,
                'name' => $hotel->post_title,
                'city' => $city,
                'country' => $country,
                'priceRange' => ['min' => $priceMin, 'max' => $priceMax],
                'rate' => $rate,
                'img' => get_the_post_thumbnail_url($hotel->ID, 'thumbnail'),
            ];
        }

        // Return the hotel object and its data
        return [$hotel, $data];
    }
}
