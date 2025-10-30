# Quick Reference Card

## Installation (3 Steps)

1. **Upload & Activate**
   ```bash
   # Copy to WordPress plugins directory
   cp -r phoenix-elementor-components /path/to/wp-content/plugins/
   ```
   - Go to WordPress Admin → Plugins → Activate "Phoenix Elementor Components"

2. **Configure API**
   - Go to Settings → Phoenix Components
   - Enter API URL: `http://phoenix-pos-test.evisionmicro.com`
   - Enter API Key: `ASDF` (your actual key)
   - Click Save

3. **Add to Product Page**
   - Edit product with Elementor
   - Add "Product Stock Display" widget
   - Customize and publish

## Widget Settings

### Content
| Setting | Description | Default |
|---------|-------------|---------|
| Title | Widget heading | "Stock Availability" |
| Show Location | Display location codes | Yes |
| Show Quantity | Display stock quantities | Yes |

### Style
| Setting | Description |
|---------|-------------|
| Title Color | Custom title color |
| Title Typography | Font size, weight, family |
| Item Spacing | Space between items (px) |

## Stock Color Codes

- 🟢 **Green** - Good stock (5+ items)
- 🟠 **Orange** - Low stock (1-4 items)
- 🔴 **Red** - Out of stock (0 items)

## API Endpoints

### WordPress REST API
```
POST /wp-json/products/v1/get-stocks-on-product-code
Body: {"code": "PRODUCT_SKU"}
Response: [{"locationCode": "BM", "productCode": "SKU", "qty": 8}, ...]
```

### External API
```
GET {API_URL}/api/Cart/GetStocks?productCode=SKU
Header: X-API-KEY: YOUR_KEY
Response: [{"locationCode": "BM", "productCode": "SKU", "qty": 8}, ...]
```

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Widget not showing | Check Elementor & WooCommerce are active |
| No stock data | Verify API settings, check product has SKU |
| API errors | Test external API with curl, verify API key |
| JavaScript errors | Check browser console, ensure jQuery loaded |

## Requirements

- ✅ WordPress 5.0+
- ✅ PHP 7.4+
- ✅ Elementor (free/pro)
- ✅ WooCommerce
- ✅ Product SKUs configured
- ✅ Valid API credentials

## File Structure

```
phoenix-elementor-components/
├── phoenix-elementor-components.php  # Main plugin
├── admin/settings.php                # Settings page
├── widgets/product-stock-widget.php  # Elementor widget
├── README.md                         # Full documentation
├── INSTALLATION.md                   # Setup guide
└── ARCHITECTURE.md                   # Technical details
```

## Support Commands

### Test Plugin Syntax
```bash
php -l phoenix-elementor-components.php
php -l widgets/product-stock-widget.php
php -l admin/settings.php
```

### Test External API
```bash
curl "http://phoenix-pos-test.evisionmicro.com/api/Cart/GetStocks?productCode=FGBASC15LT02" \
  -H "X-API-KEY: ASDF"
```

### Enable WordPress Debug
Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```
Check: `/wp-content/debug.log`

## Common Use Cases

### Basic Setup
1. Install plugin
2. Configure API
3. Add widget to product template
4. Done!

### Custom Styling
1. Add widget to page
2. Go to Style tab
3. Adjust colors and typography
4. Preview changes
5. Update page

### Multiple Locations
- Widget automatically shows all locations from API
- Each location displays separately
- Quantities are color-coded per location

## Security Notes

- ✅ API key stored securely in database
- ✅ Server-side API requests (key not exposed)
- ✅ Input sanitization on all inputs
- ✅ Output escaping on all outputs
- ✅ WordPress nonce verification
- ✅ No direct database queries

## Performance Tips

- Widget loads via AJAX (doesn't block page)
- 30-second API timeout prevents hanging
- Consider adding caching for high-traffic sites
- One API call per product page view

## Quick Test Checklist

- [ ] Plugin activates without errors
- [ ] Settings page accessible
- [ ] API settings save correctly
- [ ] Widget appears in Elementor
- [ ] Widget shows on product page
- [ ] Stock data loads successfully
- [ ] Loading spinner appears
- [ ] Stock data displays correctly
- [ ] Color coding works
- [ ] Error handling works (test with wrong API key)

## Getting Help

1. Check browser console for JavaScript errors
2. Enable WordPress debug mode
3. Test external API directly with curl
4. Verify all requirements met
5. Check product has SKU set
6. Review documentation files

## Version

**Current Version:** 1.0.0
**Release Date:** 2025-10-30

## License

Proprietary software. All rights reserved.
