=== Gafah Payment Gateway ===
Contributors: Gafah
Tags: woocommerce, payment gateway, Gafah, ecommerce, aed
Requires at least: 5.0
Tested up to: 6.3
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gafah Payment Gateway for WooCommerce allows customers to pay using Gafah's external payment platform. This plugin integrates seamlessly with WooCommerce, accepting payments in AED currency.

== Description ==

**Gafah Payment Gateway** enables WooCommerce stores to integrate Gafah's external payment platform for secure payment processing. With this plugin, customers are redirected to the Gafah platform to complete their payment and are brought back to the WooCommerce "Thank You" page upon successful completion.

Features:
- Supports payments in AED currency.
- Secure payment processing using Gafah's external API.
- Customizable payment gateway title and description.
- Success and fail URLs can be configured from the admin settings.
- Redirects customers to the Gafah payment platform for a smooth checkout experience.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/gafah-payment-gateway/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to WooCommerce > Settings > Payments and configure the Gafah Payment Gateway by entering your API key, Success URL, and Fail URL.
4. Make sure that your WooCommerce store uses AED as the currency, as Gafah Payment Gateway only supports AED.

== Configuration ==

To configure the plugin:
1. Navigate to WooCommerce > Settings > Payments.
2. Click on **Gafah Payment Gateway** to configure the settings.
3. Enter your API key, Success URL, Fail URL, and other relevant information.
4. Ensure your WooCommerce currency is set to AED (United Arab Emirates Dirham) in **WooCommerce > Settings > General**.

== Frequently Asked Questions ==

= Does this plugin support currencies other than AED? =

No, the Gafah Payment Gateway only supports AED as the currency.

= How do I get the API key? =

You can obtain your API key from your Gafah account. Please contact Gafah support if you need assistance.

= What happens after the customer completes the payment? =

After the customer completes the payment on the Gafah platform, they are redirected back to your WooCommerce store's "Thank You" page. The payment status is verified through the Gafah API, and the order is marked as complete if successful.

== Screenshots ==

1. Gafah Payment Gateway settings page in WooCommerce.
2. Payment method selection in WooCommerce checkout.

== Changelog ==

= 1.0.0 =
* Initial release of Gafah Payment Gateway for WooCommerce.

== Upgrade Notice ==

= 1.0.0 =
Initial release of the plugin.

== License ==

This plugin is licensed under the GPLv2 or later license. For more information, see https://www.gnu.org/licenses/gpl-2.0.html.
