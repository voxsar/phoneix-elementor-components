<?php
/**
 * Plugin Name: Phoenix Elementor Components
 * Description: Elementor components for WooCommerce product stock display with API integration
 * Version: 1.0.0
 * Author: Phoenix
 * Text Domain: phoenix-elementor-components
 * Requires Plugins: elementor, woocommerce
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('PHOENIX_ELEMENTOR_VERSION', '1.0.0');
define('PHOENIX_ELEMENTOR_PATH', plugin_dir_path(__FILE__));
define('PHOENIX_ELEMENTOR_URL', plugin_dir_url(__FILE__));

/**
 * Main Phoenix Elementor Components Class
 */
final class Phoenix_Elementor_Components {
    
    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
        
        // Load admin settings
        if (is_admin()) {
            require_once PHOENIX_ELEMENTOR_PATH . 'admin/settings.php';
        }
    }

    public function init() {
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }

        // Check if WooCommerce is installed and activated
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_woocommerce']);
            return;
        }

        // Register REST API routes
        add_action('rest_api_init', [$this, 'register_rest_routes']);

        // Register Elementor widgets
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
    }

    public function admin_notice_missing_elementor() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'phoenix-elementor-components'),
            '<strong>' . esc_html__('Phoenix Elementor Components', 'phoenix-elementor-components') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'phoenix-elementor-components') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_missing_woocommerce() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'phoenix-elementor-components'),
            '<strong>' . esc_html__('Phoenix Elementor Components', 'phoenix-elementor-components') . '</strong>',
            '<strong>' . esc_html__('WooCommerce', 'phoenix-elementor-components') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function register_rest_routes() {
        register_rest_route('products/v1', '/get-stocks-on-product-code', [
            'methods' => 'POST',
            'callback' => [$this, 'get_stocks_on_product_code'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function get_stocks_on_product_code($request) {
        $product_code = $request->get_param('code');
        
        if (empty($product_code)) {
            return new WP_Error('missing_code', 'Product code is required', ['status' => 400]);
        }

        // Get external API settings
        $api_url = get_option('phoenix_api_url', '');
        $api_key = get_option('phoenix_api_key', '');

        if (empty($api_url) || empty($api_key)) {
            return new WP_Error('missing_config', 'API configuration is missing', ['status' => 500]);
        }

        // Make request to external API
        $response = wp_remote_get(
            add_query_arg('productCode', $product_code, $api_url . '/api/Cart/GetStocks'),
            [
                'headers' => [
                    'X-API-KEY' => $api_key,
                ],
                'timeout' => 30,
            ]
        );

        if (is_wp_error($response)) {
            return new WP_Error('api_error', $response->get_error_message(), ['status' => 500]);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', 'Invalid JSON response', ['status' => 500]);
        }

        return rest_ensure_response($data);
    }

    public function register_widgets($widgets_manager) {
        require_once PHOENIX_ELEMENTOR_PATH . 'widgets/product-stock-widget.php';
        $widgets_manager->register(new \Phoenix_Product_Stock_Widget());
    }
}

Phoenix_Elementor_Components::instance();
