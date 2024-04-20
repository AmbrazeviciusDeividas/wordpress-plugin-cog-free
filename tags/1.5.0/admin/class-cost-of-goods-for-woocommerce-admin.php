<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpiron.com/
 * @since      1.0.0
 *
 * @package    Cost_Of_Goods_For_Woocommerce
 * @subpackage Cost_Of_Goods_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cost_Of_Goods_For_Woocommerce
 * @subpackage Cost_Of_Goods_For_Woocommerce/admin
 * @author     WPiron <info@wpiron.com>
 */
class Cost_Of_Goods_For_Woocommerce_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Cost_Of_Goods_For_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Cost_Of_Goods_For_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/cost-of-goods-for-woocommerce-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(  // jQuery
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/cost-of-goods-for-woocommerce-admin.js',
            array('jquery'),
            time(),
            true
        );
    }

    public function save_fields($postId)
    {
        $product = wc_get_product($postId);
        $costOfGoodPrice = filter_var($_POST['cost_of_goods'], FILTER_SANITIZE_STRING);
        $profitFinalPrice = filter_var($_POST['profit'], FILTER_SANITIZE_STRING);

        $costOfGoodPriceFiltered = isset($costOfGoodPrice) ? $costOfGoodPrice : false;
        $profitPriceFiltered = isset($profitFinalPrice) ? $profitFinalPrice : false;

        $error = false;

        if (!$costOfGoodPrice) {
            $error = true;
            $error_type = new \WP_Error(
                'cog_for_woocommerce_string_error',
                __("Cost Of Good price is not correct", 'wc-markup'),
                array('status' => 400)
            );
        }

        if (!$profitFinalPrice) {
            $error = true;
            $error_type = new \WP_Error(
                'cog_for_woocommerce_number_error',
                __("Cost Of Goods profit price is not correct", 'wc-markup'),
                array('status' => 400)
            );
        }

        if (!$error) {
            $product->update_meta_data('cost_of_goods', sanitize_text_field($costOfGoodPriceFiltered));
            $product->update_meta_data('profit', sanitize_text_field($profitPriceFiltered));
            $product->save();
        } else {
            return $error_type;
        }
    }

    public function product_custom_fields()
    {
        global $woocommerce, $post;
        echo '<div class=" product_custom_field ">';
        echo '<br/>';
        woocommerce_wp_text_input(
            array(
                'id' => 'cost_of_goods',
                'label' => __('Cost Of Good', 'cost-of-goods-for-wc'),
                'value' => get_post_meta(get_the_ID(), 'cost_of_goods', true),
            )
        );

        woocommerce_wp_text_input(
            array(
                'id' => 'profit',
                'custom_attributes' => array('readonly' => 'readonly'),
                'label' => __('Profit', 'cost-of-goods-for-wc'),
                'desc_tip' => 'true',
                'value' => get_post_meta(get_the_ID(), 'profit', true),
            )
        );
        echo '</div>';
    }

    public function premium_link($links)
    {
        $url = "https://wpiron.com/products/cost-of-goods-for-woocommerce/#pricing";
        $url2 = "admin.php?page=cost-of-goods-for-wc";

        $settings_link = "<a href='$url2' >" . __('Settings') . '</a> | ';
        $settings_link .= "<a href='$url' style='font-weight: bold; color: green;' target='_blank'>" . __('Get Premium') . '</a>';

        $links[] = $settings_link;
        return $links;
    }

    public function variation_options_pricing($loop, $variationData, $variation)
    {
        echo '<div class="options_group form_group">';

        woocommerce_wp_text_input(
            array(
                'id' => 'cost_of_goods_' . $loop,
                'name' => 'cost_of_goods[' . $loop . ']',
                'wrapper_class' => 'form-row form-row-first',
                'label' => __('Cost Of Good', 'cost-of-goods-for-wc'),
                'value' => get_post_meta($variation->ID, 'cost_of_goods', true),
            )
        );

        woocommerce_wp_text_input(
            array(
                'id' => 'profit_' . $loop,
                'name' => 'profit[' . $loop . ']',
                'wrapper_class' => 'form-row form-row-last',
                'custom_attributes' => array('readonly' => 'readonly'),
                'label' => __('Profit', 'cost-of-goods-for-wc'),
                'desc_tip' => 'true',
                'value' => get_post_meta($variation->ID, 'profit', true),
            )
        );


        ?>
        </div>
        <?php
    }

    public function save_product_variation($variationId, $i)
    {
        $costOfGood = filter_var($_POST['cost_of_goods'][$i], FILTER_SANITIZE_STRING);
        $profit = filter_var($_POST['profit'][$i], FILTER_SANITIZE_STRING);
        if (isset($costOfGood)) {
            update_post_meta($variationId, 'cost_of_goods', esc_attr($costOfGood));
        }
        if (isset($profit)) {
            update_post_meta($variationId, 'profit', esc_attr($profit));
        }
    }

    public function custom_woocommerce_admin_reports__premium($reports)
    {
        $salesByProfit = array(
            'profit_sales_dashboard' => array(
                'title' => __('Profit Sales Dashboard', 'cost-of-goods-for-wc'),
                'description' => '',
                'hide_title' => 1,
                'callback' => [__CLASS__, 'profit_sales_dash_callback'],
            )
        );

        if (isset($reports['orders'])) {
            $reports['orders']['reports'] = array_merge($reports['orders']['reports'], $salesByProfit);
        }

        return $reports;
    }

    public static function profit_sales_dash_callback()
    {
        $report = new \ProfitSalesForWoocommerce();
        $report->output_report();
    }

    public function custom_checkout_field_update_order_meta($order_id)
    {
        $order = wc_get_order($order_id);
        $items = $order->get_items();

        foreach ($items as $item) {
            $costOfGood = get_post_meta($item->get_id(), 'cost_of_goods');
            $profit = get_post_meta($item->get_id(), 'profit');

            if ($profit && $costOfGood) {
                update_post_meta($order_id, 'profit', esc_attr(htmlspecialchars($profit)));
                update_post_meta($order_id, 'cost_of_goods', esc_attr(htmlspecialchars($costOfGood)));
            }
        }
    }

    public function wc_markup_admin_menu_page()
    {
        add_menu_page(
            $this->plugin_name,
            'COG For WC',
            'administrator',
            $this->plugin_name,
            array($this, 'displayPluginAdminDashboard'),
            'dashicons-money',
            26
        );
    }

    public function displayPluginAdminDashboard()
    {
        require_once 'partials/' . $this->plugin_name . '-admin-display.php';
    }

    public function displayPluginAdminSettings()
    {
        $tab = filter_var($_GET['tab'], FILTER_SANITIZE_STRING);

        $active_tab = $tab ?? 'general';
        if (isset($_GET['error_message'])) {
            add_action('admin_notices', array($this, 'pluginNameSettingsMessages'));
            do_action('admin_notices', $_GET['error_message']);
        }
        require_once 'partials/' . $this->plugin_name . '-admin-settings-display.php';
    }

    function wpiron_costofgoods_admin_notice() {
        global $current_user;

        $siteUrl = site_url();
        $uniqueUserId = md5($siteUrl);

        $api_url = 'https://uwozfs6rgi.execute-api.us-east-1.amazonaws.com/prod/notifications';
        $body = json_encode([
            'pluginName' => 'wpiron-wc-cog-free',
            'status' => true,
            'user_id' => $uniqueUserId
        ], JSON_THROW_ON_ERROR);

        $args = [
            'body' => $body,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'method' => 'POST',
            'data_format' => 'body',
        ];

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true, 512);
        $status_code = $data['statusCode'];

        if (!empty($data) && $status_code === 200 && $data['body'] !== '[]') {
            $dataEncoded = json_decode($data['body'], true)[0];
            if ($dataEncoded['content'] && $dataEncoded['dismissed'] === false) {
                $content = $dataEncoded['content'];
                $message_id = $dataEncoded['message_id']; // Get the message ID

                ?>
                <div class="notice notice-success is-dismissible">
                    <?php
                    echo $content; ?>
                    <hr>
                    <a style="margin-bottom: 10px; position: relative; display: block;" href="?cost_of_goods_-notice&message_id=<?php echo urlencode($message_id); ?>"><b>Dismiss this notice</b></a>
                </div>
                <?php
            }
        }
    }

    public function wpiron_costofgoods_ignore_notice_wcmarkup() {
        global $current_user;

        $siteUrl = site_url();
        $uniqueUserId = md5($siteUrl);

        if (isset($_GET['cost_of_goods_-notice'])) {
            $message_id = $_GET['message_id'];
            $apiRequestBody = json_encode(array(
                'user_id' => $uniqueUserId,
                'plugin_name' => 'wpiron-wc-cog-free',
                'message_id' => $message_id,
            ));

            $apiResponse = wp_remote_post(
                'https://uwozfs6rgi.execute-api.us-east-1.amazonaws.com/prod/notifications',
                array(
                    'body' => $apiRequestBody,
                    'headers' => array(
                        'Content-Type' => 'application/json',
                    ),
                )
            );

            if (is_wp_error($apiResponse)) {
                $error_message = $apiResponse->get_error_message();
                return;
            }
        }
    }

}