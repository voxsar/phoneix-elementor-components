# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2025-10-30

### Added
- Initial release of Phoenix Elementor Components
- Product Stock Display Elementor widget for WooCommerce
- REST API endpoint: `POST /wp-json/products/v1/get-stocks-on-product-code`
- External API integration for fetching product stock from multiple locations
- Admin settings page for API configuration (URL and API key)
- AJAX-based stock information loading
- Customizable widget controls:
  - Title text
  - Show/hide location codes
  - Show/hide quantity information
- Style controls:
  - Title color
  - Title typography
  - Item spacing
- Color-coded stock quantities:
  - Green for adequate stock (5+ items)
  - Orange for low stock (1-4 items)
  - Red for out of stock (0 items)
- Loading spinner for better UX
- Error handling and user-friendly error messages
- Comprehensive documentation (README.md and INSTALLATION.md)
- Security features:
  - Input sanitization and validation
  - Secure API key storage
  - Server-side API requests to hide credentials

### Features
- Seamless WooCommerce integration
- Elementor editor preview support
- Responsive design
- Clean, modern UI
- Real-time stock data fetching
- Multiple location stock display
- Product SKU-based stock lookup

### Technical Details
- WordPress 5.0+ compatibility
- PHP 7.4+ support
- Elementor integration
- WooCommerce integration
- REST API implementation
- jQuery-based AJAX calls
- WordPress coding standards compliance
