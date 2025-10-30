# Installation and Testing Guide

## Quick Start

### 1. Install the Plugin

1. Copy this plugin directory to your WordPress installation:
   ```bash
   cp -r phoenix-elementor-components /path/to/wordpress/wp-content/plugins/
   ```

2. Log in to your WordPress admin panel

3. Navigate to **Plugins** > **Installed Plugins**

4. Find "Phoenix Elementor Components" and click **Activate**

### 2. Configure API Settings

1. Go to **Settings** > **Phoenix Components**

2. Enter your API configuration:
   - **API URL**: `http://phoenix-pos-test.evisionmicro.com`
   - **API Key**: `ASDF` (or your actual API key)

3. Click **Save Settings**

### 3. Add Widget to Product Page

1. Go to **Products** > select a product to edit

2. Ensure the product has a **SKU** set (this is used as the product code)

3. Click **Edit with Elementor**

4. In the Elementor panel, search for "Product Stock Display"

5. Drag and drop the widget to your desired location on the page

6. Configure the widget settings:
   - **Title**: e.g., "Stock Availability"
   - **Show Location**: Yes/No
   - **Show Quantity**: Yes/No

7. Customize styling in the **Style** tab if needed

8. Click **Update** to save

### 4. Test the Widget

1. View the product page on the frontend

2. The widget should:
   - Show a loading spinner initially
   - Make an AJAX call to fetch stock data
   - Display stock information from multiple locations
   - Show quantities with color coding:
     - Green: 5+ items in stock
     - Orange: 1-4 items (low stock)
     - Red: 0 items (out of stock)

## API Testing

### Test the WordPress REST API Endpoint

You can test the internal WordPress API endpoint directly:

```bash
# Get WordPress nonce first (requires authentication)
# Then make the request:

curl -X POST "http://your-wordpress-site.com/wp-json/products/v1/get-stocks-on-product-code" \
  -H "Content-Type: application/json" \
  -d '{"code": "FGBASC15LT02"}'
```

### Expected Response

```json
[
  {
    "locationCode": "BM",
    "productCode": "FGBASC15LT02",
    "qty": 8
  },
  {
    "locationCode": "PN",
    "productCode": "FGBASC15LT02",
    "qty": 32
  }
]
```

## Troubleshooting

### Widget Not Appearing

1. Check that both Elementor and WooCommerce are installed and activated
2. Verify you're editing a WooCommerce product page
3. Clear Elementor cache: **Elementor** > **Tools** > **Regenerate CSS & Data**

### No Stock Data Loading

1. Open browser developer console (F12) and check for errors
2. Verify API settings are correct in **Settings** > **Phoenix Components**
3. Test the external API directly:
   ```bash
   curl "http://phoenix-pos-test.evisionmicro.com/api/Cart/GetStocks?productCode=FGBASC15LT02" \
     -H "X-API-KEY: ASDF"
   ```
4. Check if the product has a SKU set
5. Verify the SKU matches a product code in the external API

### JavaScript Errors

1. Ensure jQuery is loaded on the page
2. Check for conflicts with other plugins
3. Try disabling other plugins temporarily to isolate the issue

## Development Testing

### Test in Elementor Editor

The widget includes preview functionality in the Elementor editor. When editing:
- It will show sample stock data
- The actual API call only happens on the frontend

### Debug Mode

To enable WordPress debug mode for troubleshooting, add to `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check `/wp-content/debug.log` for any errors.

## Example Product Setup

### Create a Test Product

1. Go to **Products** > **Add New**
2. Set product title: "Test Product"
3. Set **SKU**: `FGBASC15LT02` (or any product code that exists in your external API)
4. Set a price
5. Publish the product
6. Click **Edit with Elementor**
7. Add the "Product Stock Display" widget
8. Update and view the product page

## Security Notes

- The API key is stored securely in WordPress options
- The REST API endpoint is public (for frontend access) but can be restricted if needed
- API requests are made server-side to hide the API key from the frontend
- Input is sanitized and validated

## Performance Considerations

- Stock data is fetched via AJAX after page load (doesn't block page rendering)
- API requests have a 30-second timeout
- Consider implementing caching if the external API is slow or rate-limited
- Stock data is not stored in WordPress (fetched in real-time)

## Customization

### Modify Stock Colors

Edit `widgets/product-stock-widget.php` and adjust the CSS classes:
- `.phoenix-stock-qty` - Default color
- `.phoenix-stock-qty.low-stock` - Low stock color
- `.phoenix-stock-qty.out-of-stock` - Out of stock color

### Change Stock Thresholds

In the JavaScript section of `widgets/product-stock-widget.php`, modify:

```javascript
if (qty === 0) {
    qtyClass = 'out-of-stock';
} else if (qty < 5) {  // Change this threshold
    qtyClass = 'low-stock';
}
```

### Add Caching

To add caching to reduce API calls, use WordPress transients:

```php
$transient_key = 'phoenix_stock_' . $product_code;
$cached_data = get_transient($transient_key);

if ($cached_data !== false) {
    return rest_ensure_response($cached_data);
}

// ... make API call ...

// Cache for 5 minutes
set_transient($transient_key, $data, 5 * MINUTE_IN_SECONDS);
```
