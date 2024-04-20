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
 * Version:           1.3.3
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
define('COST_OF_GOODS_FOR_WOOCOMMERCE_VERSION', '1.3.3');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cost-of-goods-for-woocommerce-activator.php
 */
function activate_cost_of_goods_for_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-cost-of-goods-for-woocommerce-activator.php';
    Cost_Of_Goods_For_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cost-of-goods-for-woocommerce-deactivator.php
 */
function deactivate_cost_of_goods_for_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-cost-of-goods-for-woocommerce-deactivator.php';
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


run_cost_of_goods_for_woocommerce();
