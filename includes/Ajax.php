<?php

namespace RHC\includes;

/**
 * This file is the maine class of ajax of plugin
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


/**
 * The main plugin ajax class.
 *
 * this class used to evaluate and display all thing about ajax
 *
 */
class Ajax
{
    protected $nonce;
    public function __construct()
    {
        $this->nonce = 'rhc_nonce';
    }

    /**
     * Ajax array response for wp_send_json
     */
    public  function ajaxResponse($status = 200, $message = null, $value = null)
    {

        $response = array(
            'message' => $message,
            'value' => $value,
        );

        wp_send_json($response, $status);
        wp_die();
    }

    public function getHotelsList()
    {
        $this->checkNonce();
        $name = $_POST['name'] ?: '';
        $location = $_POST['location'] ?: '';
        $sorting = $_POST['sorting'] ?: 'date';
        $order = $_POST['order'] ?: 'DESC';
        $max_price = $_POST['max_price'] ?: '';
        $min_price = $_POST['min_price'] ?: '';
        $query = new Query();
        $data = $query->getAllHotels([
            'name' => $name,
            'location' => $location,
            'max_price' => $max_price,
            'min_price' => $min_price,
            'sorting' => $sorting,
            'order' => $order
        ]);

        if (empty($data)) {
            $this->ajaxResponse(404, 'No hotels found');
        }
        $this->ajaxResponse(200, esc_html__('hotels list'), $data);
    }

    public function getHotelById()
    {
        $this->checkNonce();
        $id = $_POST['id'];
        $query = new Query();
        [$hotel, $data] = $query->getHotelById($id);
        if (!$hotel || $hotel->post_type !== 'reisetopia_hotel') {
            $this->ajaxResponse(404, 'Hotel not found');
        }
        $this->ajaxResponse(200, esc_html__('hotel data'), $data);
    }

    public function checkNonce()
    {
        $nonce  = (isset($_POST['nonce'])) ? $_POST['nonce'] : $_GET['nonce'];

        if (!wp_verify_nonce($nonce, $this->nonce)) {
            $this->ajaxResponse(400, esc_html__('Are you cheating!!', 'rhc'));
        }
    }

    public function getNonce()
    {
        return $this->nonce;
    }
}
