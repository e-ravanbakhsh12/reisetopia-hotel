<?php

namespace RHC\includes;

use WP_Query;

/**
 * This file is the maine class of Query of plugin
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


/**
 * The main plugin query class.
 *
 * this class used to evaluate and display all thing about query
 *
 */
class Query
{

    public function  getAllHotels($name = '', $location = '', $max_price = '')
    {
        $return = [];
        $args = array(
            'post_type'  => 'reisetopia_hotel',
            'posts_per_page' => -1,
        );
        if (!empty($name)) $args['s'] = $name;
        if (!empty($location)) $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key'     => 'city',
                'value'   => $location,
                'compare' => 'like',
            ),
            array(
                'key'     => 'country',
                'value'   => $location,
                'compare' => 'like',
            ),
        );
        if (!empty($max_price)) $args['meta_query'][] = array(
            'key'     => 'max_price',
            'value'   => $max_price,
            'compare' => '<=',
            'type'    => 'NUMERIC',
        );


        $result = new WP_Query($args);

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
                ];
            }
            wp_reset_postdata();
        }
        return $return;
    }

    function getHotelById($id)
    {
        $data = [];
        $hotel = get_post($id);
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
            ];
        }
        return [$hotel, $data];
    }
}
