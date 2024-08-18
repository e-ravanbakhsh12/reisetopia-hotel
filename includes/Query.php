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

    public function  getAllHotels($attrs = [])
    {
        $defaultAttrs = [
            'name' => '',
            'location' => '',
            'max_price' => '',
            'min_price' => '',
            'sorting' => 'date',
            'order' => 'DESC',
            'page'=>1,
        ];
        $attrs = wp_parse_args($attrs, $defaultAttrs);
        $return = [];
        $args = array(
            'post_type'  => 'reisetopia_hotel',
            'posts_per_page' => 2,
            'paged'=>$attrs['page'],
            'order' => $attrs['order'],
            'orderby' => $attrs['sorting'] == 'price_range_max' || $attrs['sorting'] == 'price_range_min' ? 'meta_value_num' : $attrs['sorting'],
        );
        if ($attrs['sorting'] == 'price_range_max' || $attrs['sorting'] == 'price_range_min') $args['meta_key'] = $attrs['sorting'];
        if (!empty($attrs['name'])) $args['s'] = $attrs['name'];
        if (!empty($attrs['location'])) $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key'     => 'city',
                'value'   => $attrs['location'],
                'compare' => 'LIKE',
            ),
            array(
                'key'     => 'country',
                'value'   => $attrs['location'],
                'compare' => 'LIKE',
            ),
        );
        if (!empty($attrs['max_price'])) $args['meta_query'][] = array(
            'key'     => 'price_range_max',
            'value'   => $attrs['max_price'],
            'compare' => '<=',
            'type'    => 'NUMERIC',
        );
        if (!empty($attrs['min_price'])) $args['meta_query'][] = array(
            'key'     => 'price_range_min',
            'value'   => $attrs['min_price'],
            'compare' => '>=',
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
                    'link' => get_permalink($hotel->ID),
                    'img' => get_the_post_thumbnail_url($hotel->ID, 'post-thumbnail'),
                ];
            }
            wp_reset_postdata();
        }
        return [$return,$result->max_num_pages];
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
                'img' => get_the_post_thumbnail_url($hotel->ID, 'thumbnail'),
            ];
        }
        return [$hotel, $data];
    }
}
