# Phoenix Elementor Components

A WordPress plugin that provides Elementor widgets for WooCommerce product stock display with external API integration.

## Features

- **Product Stock Display Widget**: Elementor widget to display product stock information from multiple locations
- **REST API Integration**: Custom REST API endpoint to fetch stock data from external sources
- **WooCommerce Integration**: Seamlessly integrates with WooCommerce product pages
- **Customizable Display**: Control what information to show (location, quantity, etc.)
- **Real-time Updates**: AJAX-based stock information fetching

## Requirements

- WordPress 5.0 or higher
- Elementor (free or pro version)
- WooCommerce
- PHP 7.4 or higher

## Installation

1. Upload the plugin files to the `/wp-content/plugins/phoenix-elementor-components` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the API settings in **Settings > Phoenix Components**
4. Add the "Product Stock Display" widget to your WooCommerce product pages using Elementor

## Configuration

### API Settings

1. Go to **Settings > Phoenix Components** in WordPress admin
2. Enter your **API URL** (e.g., `http://phoenix-pos-test.evisionmicro.com`)
3. Enter your **API Key** (X-API-KEY header value)
4. Click **Save Settings**

### Using the Widget

1. Edit a WooCommerce product page with Elementor
2. Search for "Product Stock Display" widget
3. Drag and drop it to your desired location
4. Configure the widget settings:
   - **Title**: Custom title for the stock section
   - **Show Location**: Toggle to show/hide location codes
   - **Show Quantity**: Toggle to show/hide quantity information
5. Customize the styling using the Style tab
6. Update the page

## API Endpoint

The plugin provides a REST API endpoint for fetching product stock information:

### Endpoint

```
POST /wp-json/products/v1/get-stocks-on-product-code
```

### Parameters

- `code` (string, required): The product code/SKU

### Example Request

```bash
curl -X POST "https://yoursite.com/wp-json/products/v1/get-stocks-on-product-code" \
  -H "Content-Type: application/json" \
  -d '{"code": "FGBASC15LT02"}'
```

### Example Response

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

## External API Integration

The plugin integrates with an external API to fetch stock information. The external API should provide:

- **Endpoint**: `/api/Cart/GetStocks?productCode={code}`
- **Method**: GET
- **Headers**: `X-API-KEY: {your-api-key}`
- **Response**: JSON array of stock objects with `locationCode`, `productCode`, and `qty` fields

## Widget Customization

The Product Stock Display widget offers several customization options:

### Content Controls

- **Title**: Set a custom title for the stock display
- **Show Location**: Toggle location code visibility
- **Show Quantity**: Toggle quantity information visibility

### Style Controls

- **Title Color**: Customize the title text color
- **Title Typography**: Adjust font size, weight, and family
- **Item Spacing**: Control spacing between stock items

### Stock Quantity Colors

- **Green**: Stock available (5+ items)
- **Orange**: Low stock (1-4 items)
- **Red**: Out of stock (0 items)

## Development

### File Structure

```
phoenix-elementor-components/
├── admin/
│   └── settings.php          # Admin settings page
├── widgets/
│   └── product-stock-widget.php  # Elementor widget
├── phoenix-elementor-components.php  # Main plugin file
└── README.md
```

## Troubleshooting

### Widget not showing on product pages

- Ensure the product has a SKU set
- Check that WooCommerce and Elementor are active
- Verify the page template includes the widget

### No stock information displayed

- Verify API URL and API Key are configured correctly in Settings
- Check browser console for JavaScript errors
- Ensure the external API is accessible from your server
- Verify the product SKU matches the external API product codes

### API errors

- Check that the external API is online and responding
- Verify the API key is correct
- Ensure the API endpoint format matches the expected structure

## Support

For support, please contact the plugin author or open an issue in the repository.

## License

This plugin is proprietary software. All rights reserved.