
<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class WC_Gateway_Gafah extends WC_Payment_Gateway {

    // Declare the properties
    private $apikey;
    private $success_url;
    private $fail_url;

    public function __construct() {
        $this->id                 = 'gafah';
        // $this->icon               = plugins_url('../assets/img/gafah-icon.png', __FILE__);
        $this->has_fields         = false;
        // $this->method_title       = 'Gafah Payment Gateway';
        // $this->method_description = 'Accept payments through Gafah.';

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        $this->enabled      = $this->get_option('enabled');
        $this->title        = $this->get_option('title');
        $this->description  = $this->get_option('description');
        $this->apikey       = $this->get_option('apikey');

        // Save admin settings
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // Handle the Gafah success URL as a webhook
        add_action('woocommerce_api_gafah_success', array($this, 'gafah_success_handler'));
    }

    // Initialize plugin settings fields
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'       => 'Enable/Disable',
                'label'       => 'Enable Gafah Payment',
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no',
            ),
            'title' => array(
                'title'       => 'Title',
                'type'        => 'text',
                'description' => 'This controls the title which the user sees during checkout.',
                'default'     => 'Gafah Payment',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Description',
                'type'        => 'textarea',
                'description' => 'Payment method description that the customer will see on your checkout.',
                'default'     => 'Pay securely through Gafah.',
            ),
            'apikey' => array(
                'title'       => 'API Key',
                'type'        => 'text',
                'description' => 'Enter your Gafah API Key.',
            ),
        );
    }

    // Process the payment and create dynamic success/fail URLs
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);

        // Dynamically generate the success and fail URLs
        $success_url = add_query_arg('wc-api', 'gafah_success', $this->get_return_url($order));
        $fail_url    = wc_get_checkout_url();  // Redirect to checkout page in case of failure

        // Prepare data to send to Gafah's external server
        $payment_data = array(
            'success_url' => $success_url,  // Redirect URL on successful payment
            'fail_url'    => $fail_url,     // Redirect URL on failed payment
            'currency'    => $order->get_currency(),
            'amount'      => $order->get_total(),
            'orderid'    => $order->get_order_key(),     // Include WooCommerce order ID for tracking
        );

        // Send request to Gafah external API
        $response = wp_remote_post('https://gafah-external-wallet.azurewebsites.net/api/v1/initiateInvoice', array(
            'method'  => 'POST',
            'body'    => json_encode($payment_data),
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key'    => $this->apikey,  // API key
            ),
        ));

        if (is_wp_error($response)) {
            wc_add_notice('Payment error: ' . $response->get_error_message(), 'error');
            return;
        }

        print_r($response);
        $response_body = json_decode(wp_remote_retrieve_body($response), true);

        // Redirect to Gafah payment page if successful
         // Check if the API response contains the invoice_url
         if (isset($response_body['data']['invoice_url'])) {
            

            print_r($success_url);
            // Redirect to the Gafah payment page
            return array(
                'result'   => 'success',
                'redirect' => $response_body['data']['invoice_url']
            );
        } else {
            wc_add_notice('Payment error: Invalid response from Gafah server.', 'error');
            return;
        }
    }
    // Handle the success callback from Gafah
    public function gafah_success_handler() {
        if (isset($_GET['key'])) {
            $order_key = sanitize_text_field($_GET['key']);
            $order = wc_get_order(wc_get_order_id_by_order_key($order_key));
    
            if ($order) {
                // Call an external API to check payment status
                $response = wp_remote_get("https://gafah-external-wallet.azurewebsites.net/api/v1/paymentStatus?order_id={$order_key}", array(
                    'headers' => array(
                        'x-api-key' => $this->apikey,  // API key sent in the headers
                    ),
                ));
    
                // Check if API call failed
                if (is_wp_error($response)) {
                    error_log('API Error: ' . $response->get_error_message());
                    wc_add_notice('API error: ' . $response->get_error_message(), 'error');
                    wp_die('API Error: ' . $response->get_error_message()); // Display error message in WordPress
                }
    
                // Get and decode the API response body
                $response_body = wp_remote_retrieve_body($response);
                $decoded_response = json_decode($response_body, true);
    
                // Log the raw API response and decoded response
                error_log('Raw API response: ' . $response_body);
                error_log('Decoded API response: ' . print_r($decoded_response, true));
    
                // Display the decoded response for testing purposes
                // wp_die('<pre>' . print_r($decoded_response["data"]['status'], true) . '</pre>'); // Use wp_die to show the result and stop execution
                // wp_die('<pre>' . print_r($decoded_response, true) . '</pre>'); // Use wp_die to show the result and stop execution
                // wp_die('<pre>' . print_r(($decoded_response['status']) && $decoded_response['data']['status'] === 'Completed', true) . '</pre>'); // Use wp_die to show the result and stop execution
                // Check payment status and handle accordingly
                if (isset($decoded_response['success']) && $decoded_response['data']['status'] === 'Completed') {
                    // wp_die('<pre>' . print_r($decoded_response["data"]['status'], true) . '</pre>'); // Use wp_die to show the result and stop execution

                    // If payment is successful, mark the order as complete
                    $order->payment_complete();
                    $order->add_order_note('Payment successfully completed via Gafah.');
                    // Redirect the customer to the thank you page
                    wp_redirect($this->get_return_url($order));
                    exit;
                } else {
                    // Handle failed or pending payments
                    $order->update_status('failed', 'Payment failed via Gafah.');
                    wc_add_notice('Payment failed. Please try again.', 'error');
                    // Redirect to the checkout page to retry payment
                    wp_redirect(wc_get_checkout_url());
                    exit;
                }
            } else {
                error_log('Order not found: ' . $order_key);
                wc_add_notice('Order not found!', 'error');
                wp_redirect(wc_get_checkout_url());
                exit;
            }
        } else {
            error_log('Missing order key in request');
            wc_add_notice('Missing order key!', 'error');
            wp_redirect(wc_get_checkout_url());
            exit;
        }
    }
     
    
}

