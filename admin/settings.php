<?php
/**
 * Admin Settings Page
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Phoenix_Admin_Settings {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_admin_menu() {
        add_options_page(
            esc_html__('Phoenix Elementor Components', 'phoenix-elementor-components'),
            esc_html__('Phoenix Components', 'phoenix-elementor-components'),
            'manage_options',
            'phoenix-elementor-components',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('phoenix_elementor_settings', 'phoenix_api_url', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => '',
        ]);

        register_setting('phoenix_elementor_settings', 'phoenix_api_key', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ]);

        add_settings_section(
            'phoenix_api_settings',
            esc_html__('API Configuration', 'phoenix-elementor-components'),
            [$this, 'render_section_info'],
            'phoenix-elementor-components'
        );

        add_settings_field(
            'phoenix_api_url',
            esc_html__('API URL', 'phoenix-elementor-components'),
            [$this, 'render_api_url_field'],
            'phoenix-elementor-components',
            'phoenix_api_settings'
        );

        add_settings_field(
            'phoenix_api_key',
            esc_html__('API Key (X-API-KEY)', 'phoenix-elementor-components'),
            [$this, 'render_api_key_field'],
            'phoenix-elementor-components',
            'phoenix_api_settings'
        );
    }

    public function render_section_info() {
        echo '<p>' . esc_html__('Configure the external API settings for fetching product stock information.', 'phoenix-elementor-components') . '</p>';
    }

    public function render_api_url_field() {
        $value = get_option('phoenix_api_url', '');
        ?>
        <input type="url" 
               name="phoenix_api_url" 
               value="<?php echo esc_attr($value); ?>" 
               class="regular-text" 
               placeholder="https://example.com" />
        <p class="description">
            <?php echo esc_html__('Enter the base URL of the external API (e.g., http://phoenix-pos-test.evisionmicro.com)', 'phoenix-elementor-components'); ?>
        </p>
        <?php
    }

    public function render_api_key_field() {
        $value = get_option('phoenix_api_key', '');
        ?>
        <input type="text" 
               name="phoenix_api_key" 
               value="<?php echo esc_attr($value); ?>" 
               class="regular-text" 
               placeholder="Your API Key" />
        <p class="description">
            <?php echo esc_html__('Enter the API key that will be sent as X-API-KEY header', 'phoenix-elementor-components'); ?>
        </p>
        <?php
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'phoenix_messages',
                'phoenix_message',
                esc_html__('Settings Saved', 'phoenix-elementor-components'),
                'updated'
            );
        }

        settings_errors('phoenix_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('phoenix_elementor_settings');
                do_settings_sections('phoenix-elementor-components');
                submit_button(esc_html__('Save Settings', 'phoenix-elementor-components'));
                ?>
            </form>
        </div>
        <?php
    }
}

new Phoenix_Admin_Settings();
