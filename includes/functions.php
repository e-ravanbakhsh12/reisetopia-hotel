<?php

use FlameCore\UserAgent\UserAgent;

/**
 * Checks if the Advanced Custom Fields (ACF) plugin is active.
 * If ACF is not active, it triggers an admin notice to inform the user.
 *
 * @return void
 */
function checkForACF(): void 
{  
    if (!class_exists('ACF')) {  
        add_action('admin_notices', 'missingACFNotice');  
    }  
}  

/**
 * Displays an admin notice if the ACF plugin is missing.
 * The notice includes a link to install and activate ACF.
 *
 * @return void
 */
function missingACFNotice(): void 
{  
    $install_url = wp_nonce_url(
        add_query_arg(
            array(
                'action' => 'install-plugin', 
                'plugin' => 'advanced-custom-fields'
            ), 
            admin_url('update.php')
        ), 
        'install-plugin_advanced-custom-fields'
    );

    $class = 'notice notice-error';  
    $message = sprintf(
        esc_html__('The <b>Advanced Custom Fields</b> plugin must be active for <b>Reisetopia Hotel Challenge</b> to work. Please <a href="%s" target="_blank">install & activate ACF</a>.'),  
        esc_url($install_url)
    );  

    printf('<div class="%s"><p>%s</p></div>', esc_attr($class), $message);  
}  

/**
 * Recursively sanitizes a nested object or array.
 * The function processes each element in the object or array, ensuring that strings are sanitized.
 *
 * @param array|object $object The nested object or array to sanitize.
 * 
 * @return array The sanitized object or array.
 */
function sanitizeNestedObject($object): array 
{
    // Create an empty array to store the sanitized object
    $sanitized_object = array();

    foreach ($object as $key => $value) {
        if (is_array($value) || is_object($value)) {
            // Recursively sanitize nested arrays or objects
            $sanitized_object[$key] = sanitizeNestedObject((array)$value);
        } elseif (is_string($value)) {
            // Sanitize string values
            $sanitized_object[$key] = sanitize_text_field($value);
        } else {
            // Retain non-string values without modification
            $sanitized_object[$key] = $value;
        }
    }

    return $sanitized_object;
}

/**
 * Determines if the current user agent is Googlebot.
 *
 * This function checks the user agent string from the global request
 * and returns true if it matches 'Googlebot'
 *
 * @return bool Returns true if the user agent is Googlebot, otherwise false.
 */
function isGoogleBot()
{
    return isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false;
}
