<?php

function checkForACF() {  
    if (!class_exists('ACF')) {  
        add_action('admin_notices', 'missingACFNotice');  
    }  
}  

function missingACFNotice() {  
    $install_url = wp_nonce_url(add_query_arg(array('action' => 'install-plugin', 'plugin' => 'advanced-custom-fields'), admin_url('update.php')), 'install-plugin_advanced-custom-fields');  
    $class = 'notice notice-error';  
    $message = sprintf(  
        esc_html__('The <b>Advanced Custom Fields</b> plugin must be active for <b>Reisetopia Hotel Challenge</b> to work. Please <a href="%s" target="_blank">install & activate ACF</a>.'),  
        esc_url($install_url)  
    );  
    
    printf('<div class="%s"><p>%s</p></div>', esc_attr($class), $message);  
}  

function sanitizeNestedObject($object)
{
    // Create an empty array to store the sanitized object
    $sanitized_object = array();

    foreach ($object as $key => $value) {
        if (is_array($value) || is_object($value)) {
            $sanitized_object[$key] = sanitizeNestedObject((array)$value);
        }
        elseif (is_string($value)) {
            $sanitized_object[$key] = sanitize_text_field($value);
        }
        else {
            $sanitized_object[$key] = $value;
        }
    }
    return $sanitized_object;
}
