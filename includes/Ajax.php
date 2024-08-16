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
    public  function ajaxResponse($success = true, $message = null, $value = null)
    {

        $response = array(
            'success' => $success,
            'message' => $message,
            'value' => $value,
        );

        wp_send_json($response);
        wp_die();
    }


    public function localizeArray()
    {
        return array(
            'ajaxUrl'   => admin_url('admin-ajax.php'),
            'homeUrl'   => home_url(),
            'nonce' => wp_create_nonce($this->nonce),
            'path' => RHC_DIR,
        );
    }

    public function getHotelsList()
    {
        $this->checkNonce();
        $name = $_POST['name'] ?: '';
        $location = $_POST['location'] ?: '';
        $max_price = $_POST['max_price'] ?: '';
        $query = new Query();
        $data = $query->getAllHotels($name, $location, $max_price);

        if (empty($data)) {
            $this->ajaxResponse(false, 'No hotels found');
        }
        $this->ajaxResponse(true, 'hotels list', $data);
    }

    public function getHotelById()
    {
        $this->checkNonce();
        $id = $_POST['id'];
        $query = new Query();
        [$hotel, $data] = $query->getHotelById($id);
        if (!$hotel || $hotel->post_type !== 'reisetopia_hotel') {
            $this->ajaxResponse(false, 'Hotel not found');
        }
        $this->ajaxResponse(true, 'hotel data', $data);
    }

    public function checkNonce()
    {
        $nonce  = (isset($_POST['nonce'])) ? $_POST['nonce'] : $_GET['nonce'];

        if (!wp_verify_nonce($nonce, $this->nonce)) {
            $this->ajaxResponse(false, esc_html__('Are you cheating!!', 'rhc'));
        }
    }
}
