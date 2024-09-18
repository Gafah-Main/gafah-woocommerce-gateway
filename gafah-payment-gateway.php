<?php
/**
 * Plugin Name: Gafah Payment Gateway
 * Description: Custom WooCommerce payment gateway for Gafah.
 * Author: Gafah
 * Version: 1.0
 * Text Domain: gafah-payment-gateway
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include Gafah Payment Gateway class
add_action('plugins_loaded', 'gafah_payment_gateway_init', 11);

function gafah_payment_gateway_init() {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    include_once 'includes/class-wc-gateway-gafah.php';

    // Add Gafah payment gateway to WooCommerce
    add_filter('woocommerce_payment_gateways', 'add_gafah_payment_gateway');
    function add_gafah_payment_gateway($gateways) {
        $gateways[] = 'WC_Gateway_Gafah';
        return $gateways;
    }
}
