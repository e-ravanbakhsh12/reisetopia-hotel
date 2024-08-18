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
