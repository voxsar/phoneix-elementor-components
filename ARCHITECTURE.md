# Component Architecture

## File Structure

```
phoenix-elementor-components/
├── admin/
│   └── settings.php                 # Admin settings page
├── widgets/
│   └── product-stock-widget.php     # Elementor widget
├── phoenix-elementor-components.php # Main plugin file
├── .gitignore                       # Git ignore rules
├── CHANGELOG.md                     # Version history
├── IMPLEMENTATION_SUMMARY.md        # Implementation details
├── INSTALLATION.md                  # Installation guide
└── README.md                        # Main documentation
```

## Component Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    WordPress Installation                    │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│              Phoenix Elementor Components Plugin             │
│  ┌───────────────────────────────────────────────────────┐  │
│  │         phoenix-elementor-components.php              │  │
│  │  - Plugin initialization                              │  │
│  │  - Dependency checking                                │  │
│  │  - REST API registration                              │  │
│  │  - Widget registration                                │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
         │                    │                     │
         ▼                    ▼                     ▼
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│  Admin Settings │  │  REST API       │  │ Elementor Widget│
│  (admin/)       │  │  Endpoint       │  │ (widgets/)      │
│                 │  │                 │  │                 │
│  - API URL      │  │  POST /products │  │  - Controls     │
│  - API Key      │  │  /v1/get-stocks │  │  - Rendering    │
│  - Save/Load    │  │  -on-product-   │  │  - AJAX calls   │
│                 │  │  code           │  │  - Styling      │
└─────────────────┘  └─────────────────┘  └─────────────────┘
                              │                     │
                              │                     │
                              └──────────┬──────────┘
                                        ▼
                        ┌──────────────────────────┐
                        │   External API Request   │
                        │  GET /api/Cart/GetStocks │
                        │  Header: X-API-KEY       │
                        └──────────────────────────┘
                                        │
                                        ▼
                        ┌──────────────────────────┐
                        │   External API Response  │
                        │  JSON: Stock Data Array  │
                        └──────────────────────────┘
                                        │
                                        ▼
                        ┌──────────────────────────┐
                        │  Frontend Display        │
                        │  - Location codes        │
                        │  - Stock quantities      │
                        │  - Color coding          │
                        └──────────────────────────┘
```

## Data Flow

### Admin Configuration Flow

```
Admin User
    │
    ├──► Settings > Phoenix Components
    │
    ├──► Enter API URL
    │
    ├──► Enter API Key
    │
    └──► Save Settings
           │
           └──► WordPress Options Database
                  - phoenix_api_url
                  - phoenix_api_key
```

### Widget Display Flow

```
User visits Product Page
    │
    ├──► Elementor renders page
    │
    ├──► Phoenix Product Stock Widget loads
    │         │
    │         └──► Check if product page
    │                   │
    │                   └──► Get product SKU
    │                           │
    │                           └──► Display loading spinner
    │
    └──► JavaScript AJAX Call
              │
              ├──► POST to WordPress REST API
              │    /products/v1/get-stocks-on-product-code
              │    with product code
              │
              └──► WordPress REST API Handler
                     │
                     ├──► Validate product code
                     │
                     ├──► Load API settings from options
                     │
                     ├──► Call external API
                     │    GET {API_URL}/api/Cart/GetStocks
                     │    ?productCode={code}
                     │    Header: X-API-KEY: {key}
                     │
                     └──► Return stock data to frontend
                            │
                            └──► JavaScript processes response
                                   │
                                   ├──► Parse JSON data
                                   │
                                   ├──► Generate HTML
                                   │
                                   ├──► Apply color coding
                                   │
                                   └──► Display stock info
```

## Component Integration Points

### WordPress Integration
- **Hooks Used:**
  - `plugins_loaded` - Initialize plugin
  - `admin_notices` - Show dependency warnings
  - `rest_api_init` - Register REST API routes
  - `elementor/widgets/register` - Register Elementor widgets
  - `admin_menu` - Add settings page
  - `admin_init` - Register settings

### Elementor Integration
- **Widget Class:** `Phoenix_Product_Stock_Widget`
- **Base Class:** `\Elementor\Widget_Base`
- **Category:** `woocommerce-elements`
- **Methods:**
  - `get_name()` - Widget identifier
  - `get_title()` - Widget display name
  - `get_icon()` - Widget icon
  - `register_controls()` - Add controls
  - `render()` - Frontend output
  - `content_template()` - Editor preview

### WooCommerce Integration
- **Functions Used:**
  - `is_product()` - Check if product page
  - `global $product` - Access product object
  - `$product->get_sku()` - Get product SKU

### REST API Integration
- **Namespace:** `products/v1`
- **Route:** `/get-stocks-on-product-code`
- **Method:** `POST`
- **Parameters:**
  - `code` (required) - Product SKU
- **Authentication:** Public endpoint (permission_callback: `__return_true`)
- **Response:** JSON array of stock objects

## Security Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     Security Layers                      │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  1. Input Layer                                         │
│     - sanitize_text_field()                             │
│     - esc_url_raw()                                     │
│     - Validation checks                                 │
│                                                          │
│  2. Storage Layer                                       │
│     - WordPress Options API                             │
│     - Encrypted at database level (if configured)       │
│                                                          │
│  3. Processing Layer                                    │
│     - Capability checking (manage_options)              │
│     - Nonce verification (wp_rest nonce)                │
│     - Server-side API calls                             │
│                                                          │
│  4. Output Layer                                        │
│     - esc_html()                                        │
│     - esc_attr()                                        │
│     - esc_url()                                         │
│                                                          │
│  5. API Layer                                           │
│     - API key sent in headers (not URL)                 │
│     - Server-side requests only                         │
│     - 30-second timeout                                 │
│     - Error handling                                    │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

## Widget Control Structure

```
Phoenix Product Stock Widget
│
├── Content Tab
│   ├── Title (Text Control)
│   ├── Show Location (Switcher)
│   └── Show Quantity (Switcher)
│
└── Style Tab
    ├── Title Color (Color Control)
    ├── Title Typography (Typography Group)
    └── Item Spacing (Slider Control)
```

## CSS Class Structure

```
.phoenix-product-stock-widget              # Main container
│
├── .phoenix-stock-title                   # Title heading
│
├── .phoenix-stock-loading                 # Loading state
│   └── .spinner                           # Loading spinner
│
├── .phoenix-stock-list                    # Stock items container
│   └── .phoenix-stock-item               # Individual stock item
│       ├── .phoenix-stock-location       # Location code
│       └── .phoenix-stock-qty            # Quantity display
│           ├── .low-stock                # Low stock modifier
│           └── .out-of-stock             # Out of stock modifier
│
└── .phoenix-stock-error                   # Error message
```

## API Response Structure

```json
{
  "description": "Array of stock objects",
  "type": "array",
  "items": {
    "type": "object",
    "properties": {
      "locationCode": {
        "type": "string",
        "description": "Store/warehouse location code",
        "example": "BM"
      },
      "productCode": {
        "type": "string",
        "description": "Product SKU/code",
        "example": "FGBASC15LT02"
      },
      "qty": {
        "type": "integer",
        "description": "Available quantity",
        "example": 8
      }
    }
  }
}
```

## Error Handling Flow

```
Error Source
    │
    ├──► Missing Product Code
    │       └──► Return WP_Error (400)
    │
    ├──► Missing API Configuration
    │       └──► Return WP_Error (500)
    │
    ├──► External API Request Failure
    │       └──► Return WP_Error (500)
    │
    ├──► Invalid JSON Response
    │       └──► Return WP_Error (500)
    │
    └──► JavaScript/Network Error
            └──► Display error message in widget
```

## Performance Characteristics

- **Page Load Impact:** None (AJAX after load)
- **API Call Timing:** After DOM ready
- **Timeout:** 30 seconds
- **Caching:** Not implemented (ready for transient cache)
- **Database Queries:** 2 (get_option calls)
- **HTTP Requests:** 1 per product view
- **JavaScript Dependencies:** jQuery (bundled with WordPress)
- **CSS:** Inline in widget (no external file)

## Browser Compatibility

- **Modern Browsers:** Chrome, Firefox, Safari, Edge
- **JavaScript:** ES5 compatible
- **CSS:** Standard CSS3
- **Fallbacks:** Error messages for failed requests
- **Progressive Enhancement:** Works without JavaScript (shows loading state)
