<?php

namespace RHC\includes;

/**
 * This file contains the main AJAX class of the plugin.
 * 
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The main plugin AJAX class.
 *
 * This class handles AJAX requests and responses for the plugin.
 *
 */
class Ajax
{
    /**
     * @var string $nonce The nonce used for AJAX security.
     */
    protected $nonce;

    /**
     * Constructor method.
     * Initializes the nonce property.
     */
    public function __construct()
    {
        $this->nonce = 'rhc_nonce';
    }

    /**
     * Sends an AJAX response with a status code, message, and optional value.
     *
     * @param int $status The HTTP status code for the response. Defaults to 200.
     * @param string|null $message The message to include in the response. Defaults to null.
     * @param mixed $value The value to include in the response. Defaults to null.
     * @return void
     */
    public function ajaxResponse(int $status = 200, ?string $message = null, $value = null): void
    {
        $response = array(
            'message' => $message,
            'value' => $value,
        );

        wp_send_json($response, $status);
        wp_die();
    }

    /**
     * Handles the AJAX request to get a list of hotels.
     *
     * @return void
     */
    public function getHotelsList(): void
    {
        $this->checkNonce();

        $name = $_POST['name'] ?: '';
        $location = $_POST['location'] ?: '';
        $sorting = $_POST['sorting'] ?: 'date';
        $order = $_POST['order'] ?: 'DESC';
        $max_price = $_POST['max_price'] ?: '';
        $min_price = $_POST['min_price'] ?: '';
        $page = $_POST['page'] ?: 1;

        $query = new Query();
        [$list, $maxNumPages] = $query->getAllHotels2([
            'name' => $name,
            'location' => $location,
            'max_price' => $max_price,
            'min_price' => $min_price,
            'sorting' => $sorting,
            'order' => $order,
            'page' => $page,
        ]);

        if (empty($list)) {
            $this->ajaxResponse(404, 'No hotels found');
        }

        $this->ajaxResponse(200, esc_html__('Hotels list'), [
            'page' => intval($page),
            'maxNumPages' => $maxNumPages,
            'list' => $list
        ]);
    }

    /**
     * Handles the AJAX request to get a hotel by its ID.
     *
     * @return void
     */
    public function getHotelById(): void
    {
        $this->checkNonce();

        $id = $_POST['id'];
        $query = new Query();
        [$hotel, $data] = $query->getHotelById($id);

        if (!$hotel || $hotel->post_type !== 'reisetopia_hotel') {
            $this->ajaxResponse(404, 'Hotel not found');
        }

        $this->ajaxResponse(200, esc_html__('Hotel data'), $data);
    }

    /**
     * Verifies the nonce to ensure the request is legitimate.
     *
     * @return void
     */
    public function checkNonce(): void
    {
        $nonce = (isset($_POST['nonce'])) ? $_POST['nonce'] : $_GET['nonce'];

        if (!wp_verify_nonce($nonce, $this->nonce)) {
            $this->ajaxResponse(400, esc_html__('Are you cheating!!', 'rhc'));
        }
    }

    /**
     * Retrieves the nonce value used in AJAX requests.
     *
     * @return string The nonce value.
     */
    public function getNonce(): string
    {
        return $this->nonce;
    }
}
