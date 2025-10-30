<?php
/**
 * Product Stock Widget
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Phoenix_Product_Stock_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'phoenix_product_stock';
    }

    public function get_title() {
        return esc_html__('Product Stock Display', 'phoenix-elementor-components');
    }

    public function get_icon() {
        return 'eicon-product-stock';
    }

    public function get_categories() {
        return ['woocommerce-elements'];
    }

    public function get_keywords() {
        return ['woocommerce', 'product', 'stock', 'inventory'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'phoenix-elementor-components'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'phoenix-elementor-components'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Stock Availability', 'phoenix-elementor-components'),
                'placeholder' => esc_html__('Enter title', 'phoenix-elementor-components'),
            ]
        );

        $this->add_control(
            'show_location',
            [
                'label' => esc_html__('Show Location', 'phoenix-elementor-components'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'phoenix-elementor-components'),
                'label_off' => esc_html__('Hide', 'phoenix-elementor-components'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_quantity',
            [
                'label' => esc_html__('Show Quantity', 'phoenix-elementor-components'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'phoenix-elementor-components'),
                'label_off' => esc_html__('Hide', 'phoenix-elementor-components'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'phoenix-elementor-components'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'phoenix-elementor-components'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .phoenix-stock-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Title Typography', 'phoenix-elementor-components'),
                'selector' => '{{WRAPPER}} .phoenix-stock-title',
            ]
        );

        $this->add_control(
            'stock_item_spacing',
            [
                'label' => esc_html__('Item Spacing', 'phoenix-elementor-components'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .phoenix-stock-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Check if we're on a product page
        if (!is_product()) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="elementor-alert elementor-alert-info">';
                echo esc_html__('This widget will display product stock information on product pages.', 'phoenix-elementor-components');
                echo '</div>';
            }
            return;
        }

        global $product;
        
        if (!$product) {
            return;
        }

        $product_sku = $product->get_sku();
        
        if (empty($product_sku)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="elementor-alert elementor-alert-warning">';
                echo esc_html__('Product SKU is required to fetch stock information.', 'phoenix-elementor-components');
                echo '</div>';
            }
            return;
        }

        ?>
        <div class="phoenix-product-stock-widget" data-product-code="<?php echo esc_attr($product_sku); ?>">
            <?php if (!empty($settings['title'])) : ?>
                <h3 class="phoenix-stock-title"><?php echo esc_html($settings['title']); ?></h3>
            <?php endif; ?>
            
            <div class="phoenix-stock-loading">
                <span class="spinner"></span>
                <?php echo esc_html__('Loading stock information...', 'phoenix-elementor-components'); ?>
            </div>
            
            <div class="phoenix-stock-list" style="display: none;">
                <!-- Stock items will be populated here via JavaScript -->
            </div>
            
            <div class="phoenix-stock-error" style="display: none; color: red;">
                <!-- Error message will be shown here if API fails -->
            </div>
        </div>

        <style>
            .phoenix-product-stock-widget {
                padding: 20px;
                border: 1px solid #e0e0e0;
                border-radius: 4px;
                background-color: #fff;
            }
            
            .phoenix-stock-title {
                margin-top: 0;
                margin-bottom: 15px;
            }
            
            .phoenix-stock-loading {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .phoenix-stock-loading .spinner {
                display: inline-block;
                width: 20px;
                height: 20px;
                border: 3px solid rgba(0, 0, 0, 0.1);
                border-radius: 50%;
                border-top-color: #333;
                animation: phoenix-spin 1s ease-in-out infinite;
            }
            
            @keyframes phoenix-spin {
                to { transform: rotate(360deg); }
            }
            
            .phoenix-stock-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            .phoenix-stock-item {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                border-bottom: 1px solid #f0f0f0;
            }
            
            .phoenix-stock-item:last-child {
                border-bottom: none;
            }
            
            .phoenix-stock-location {
                font-weight: 600;
            }
            
            .phoenix-stock-qty {
                color: #4CAF50;
                font-weight: 600;
            }
            
            .phoenix-stock-qty.low-stock {
                color: #FF9800;
            }
            
            .phoenix-stock-qty.out-of-stock {
                color: #F44336;
            }
        </style>

        <script>
        (function($) {
            'use strict';
            
            $(document).ready(function() {
                var $widget = $('.phoenix-product-stock-widget');
                
                if ($widget.length === 0) {
                    return;
                }
                
                var productCode = $widget.data('product-code');
                var showLocation = <?php echo $settings['show_location'] === 'yes' ? 'true' : 'false'; ?>;
                var showQuantity = <?php echo $settings['show_quantity'] === 'yes' ? 'true' : 'false'; ?>;
                
                $.ajax({
                    url: '<?php echo esc_url(rest_url('products/v1/get-stocks-on-product-code')); ?>',
                    method: 'POST',
                    data: {
                        code: productCode
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce('wp_rest'); ?>');
                    },
                    success: function(response) {
                        $widget.find('.phoenix-stock-loading').hide();
                        
                        if (response && response.length > 0) {
                            var $list = $widget.find('.phoenix-stock-list');
                            $list.empty();
                            
                            response.forEach(function(stock) {
                                var qty = parseInt(stock.qty);
                                var qtyClass = '';
                                
                                if (qty === 0) {
                                    qtyClass = 'out-of-stock';
                                } else if (qty < 5) {
                                    qtyClass = 'low-stock';
                                }
                                
                                var html = '<div class="phoenix-stock-item">';
                                
                                if (showLocation) {
                                    html += '<span class="phoenix-stock-location">' + stock.locationCode + '</span>';
                                }
                                
                                if (showQuantity) {
                                    html += '<span class="phoenix-stock-qty ' + qtyClass + '">' + qty + ' in stock</span>';
                                }
                                
                                html += '</div>';
                                
                                $list.append(html);
                            });
                            
                            $list.show();
                        } else {
                            $widget.find('.phoenix-stock-error')
                                .text('No stock information available.')
                                .show();
                        }
                    },
                    error: function(xhr, status, error) {
                        $widget.find('.phoenix-stock-loading').hide();
                        $widget.find('.phoenix-stock-error')
                            .text('Failed to load stock information. Please try again later.')
                            .show();
                    }
                });
            });
        })(jQuery);
        </script>
        <?php
    }

    protected function content_template() {
        ?>
        <#
        if (settings.title) {
            #>
            <div class="phoenix-product-stock-widget">
                <h3 class="phoenix-stock-title">{{{ settings.title }}}</h3>
                <div class="phoenix-stock-list">
                    <div class="phoenix-stock-item">
                        <span class="phoenix-stock-location">BM</span>
                        <span class="phoenix-stock-qty">8 in stock</span>
                    </div>
                    <div class="phoenix-stock-item">
                        <span class="phoenix-stock-location">PN</span>
                        <span class="phoenix-stock-qty">32 in stock</span>
                    </div>
                </div>
            </div>
            <#
        }
        #>
        <?php
    }
}
