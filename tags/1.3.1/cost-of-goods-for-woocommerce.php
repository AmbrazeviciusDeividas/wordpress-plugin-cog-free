<?php
/**
 * @link              https://wpiron.com/products/cost-of-goods-for-woocommerce/
 * @since             1.0.0
 * @package           Cost_Of_Goods
 *
 * @wordpress-plugin
 * Plugin Name:       Cost Of Goods
 * Plugin URI:        https://wpiron.com
 * Description:       add your cost of goods to your products and track your profit in reports
 * Version:           1.3.1
 * Author:            WPiron
 * Author URI:        https://wpiron.com/
 * Text Domain:       cost-of-goods
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('COST_OF_GOODS_FOR_WOOCOMMERCE_VERSION', '1.3.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cost-of-goods-for-woocommerce-activator.php
 */
function activate_cost_of_goods_for_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-cost-of-goods-for-woocommerce-activator.php';
    send_activation_request('Cost Of Goods', true);
    Cost_Of_Goods_For_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cost-of-goods-for-woocommerce-deactivator.php
 */
function deactivate_cost_of_goods_for_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-cost-of-goods-for-woocommerce-deactivator.php';
    send_activation_request('Cost Of Goods', false);
    Cost_Of_Goods_For_Woocommerce_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_cost_of_goods_for_woocommerce');
register_deactivation_hook(__FILE__, 'deactivate_cost_of_goods_for_woocommerce');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-cost-of-goods-for-woocommerce.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cost_of_goods_for_woocommerce()
{
    $plugin = new Cost_Of_Goods_For_Woocommerce();
    $plugin->run();
}


function generateUUIDv4() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), // 32 bits for "time_low"
        mt_rand(0, 0xffff), // 16 bits for "time_mid"
        mt_rand(0, 0x0fff) | 0x4000, // 16 bits for "time_hi_and_version", four most significant bits holds version number 4
        mt_rand(0, 0x3fff) | 0x8000, // 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low", two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff) // 48 bits for "node"
    );
}

function send_activation_request($pluginName, $active) {
    $api_url = 'https://pyknajcze5.execute-api.us-east-1.amazonaws.com/prod/activatePlugin';

    // Prepare the inner data
    $inner_data = [
        'pluginActivated' => $active,
        'pluginName' => $pluginName,
        'userId' => strval(generateUUIDv4()), // Ensure you have defined generateUUIDv4() function
        'siteUrl' => get_site_url()
    ];

    // JSON encode the inner data, then set it as the value of 'body' key in another array
    $body = wp_json_encode([
        'body' => wp_json_encode($inner_data)
    ]);

    $options = [
        'body'        => $body,
        'headers'     => [
            'Content-Type' => 'application/json',
        ],
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => true,
        'data_format' => 'body'
    ];

    $response = wp_remote_post($api_url, $options);

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log('Activation request failed: ' . $error_message);
    } else {
        $response_body = wp_remote_retrieve_body($response);
        error_log('Activation request successful: ' . $response_body);
    }
}


run_cost_of_goods_for_woocommerce();
