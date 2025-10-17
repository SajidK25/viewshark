# 🎨 EasyStream Branding System

## Overview

The EasyStream Branding System is a comprehensive customization framework that allows users to fully customize every visual aspect of their video streaming platform. From colors and fonts to logos and layouts, everything can be adjusted to match your brand identity.

## ✨ Features

### 🎯 Complete Customization
- **Colors**: Primary, secondary, success, warning, danger, info colors
- **Typography**: Font families, sizes, weights, line heights
- **Layout**: Spacing, borders, shadows, dimensions
- **Logos**: Main logo, small logo, favicon, default images
- **Badges**: Verified, premium, live, new badges with custom colors
- **Buttons**: Fully customizable button styles
- **Backgrounds**: Header, footer, card, main backgrounds

### 🚀 Advanced Features
- **Theme Presets**: Quick theme switching with pre-built themes
- **Dark Mode**: Built-in dark mode support
- **Live Preview**: Real-time preview of changes
- **CSS Generation**: Dynamic CSS generation with caching
- **Template Integration**: Easy integration with existing templates
- **Cache Management**: Intelligent caching for performance

## 📁 File Structure

```
f_core/f_classes/class.branding.php          # Core branding class
f_core/f_functions/functions.branding.php    # Helper functions
f_modules/m_backend/branding_management.php  # Admin interface
deploy/create_branding_tables.sql            # Database structure
dynamic_theme.php                            # CSS endpoint
test_branding_system.php                     # Demo/test page
```

## 🗄️ Database Structure

### Tables Created
- `db_branding_settings` - Individual branding settings
- `db_branding_presets` - Theme presets for quick switching
- `db_branding_cache` - CSS and data caching

### Setting Categories
- **general** - Site name, tagline, description
- **logos** - Logo files and images
- **colors** - All color settings
- **backgrounds** - Background colors
- **text** - Text colors and styles
- **borders** - Border and shadow colors
- **buttons** - Button styling
- **typography** - Font settings
- **layout** - Spacing and dimensions
- **badges** - Badge configurations
- **social** - Social media brand colors
- **advanced** - Advanced features
- **player** - Video player styling
- **email** - Email template colors

## 🛠️ Installation

### 1. Database Setup
```sql
-- Run the branding tables creation script
SOURCE deploy/create_branding_tables.sql;
```

### 2. Include in Core Config
Add to your `f_core/config.core.php`:
```php
// Load branding functions
include_once 'f_core/f_functions/functions.branding.php';

// Initialize branding system
$branding = VBranding::getInstance();
```

### 3. Add CSS Link
Add to your HTML head:
```php
<?php echo dynamicCSSLink(); ?>
<?php echo inlineCSSVariables(); ?>
```

## 🎨 Usage Examples

### Basic Usage
```php
// Get branding instance
$branding = getBranding();

// Get a setting
$primaryColor = brandingGet('color_primary', '#007bff');

// Generate site logo
echo siteLogo('main', 'header-logo');

// Generate badges
echo badge('verified');
echo badge('premium');
echo badge('live');

// Generate buttons
echo button('Click Me', 'primary', '/action');
```

### Template Integration
```php
// In your template files
<?php echo brandingMetaTags(); ?>
<?php echo brandedHeader(true); ?>

<!-- Your content here -->

<?php echo brandedFooter(); ?>
```

### Video Cards
```php
$video = [
    'title' => 'My Video',
    'author' => 'Creator Name',
    'views' => 1000,
    'date' => '1 day ago',
    'badges' => ['verified', 'premium']
];

echo videoCard($video);
```

### User Avatars
```php
$user = [
    'username' => 'johndoe',
    'avatar' => '/path/to/avatar.jpg',
    'verified' => true,
    'premium' => true
];

echo userAvatar($user, 'medium', true);
```

### Notifications
```php
echo notification('Success message!', 'success');
echo notification('Error occurred!', 'error');
echo notification('Warning message!', 'warning');
echo notification('Info message!', 'info');
```

## 🎛️ Admin Interface

### Access
Navigate to: `/f_modules/m_backend/branding_management.php`

### Features
- **Category Tabs**: Organized settings by category
- **Live Preview**: See changes in real-time
- **Color Picker**: Visual color selection
- **Preset Management**: Save and apply theme presets
- **CSS Export**: Download generated CSS
- **Form Validation**: CSRF protection and input validation

### Available Categories
1. **General** - Basic site information
2. **Logos** - Logo and image settings
3. **Colors** - Primary color palette
4. **Backgrounds** - Background colors
5. **Text** - Text colors and styles
6. **Borders** - Border and shadow settings
7. **Buttons** - Button styling options
8. **Typography** - Font and text settings
9. **Layout** - Spacing and dimensions
10. **Badges** - Badge configurations
11. **Social** - Social media colors
12. **Advanced** - Advanced features
13. **Player** - Video player styling
14. **Email** - Email template colors

## 🎨 CSS Variables

The system generates CSS custom properties for easy styling:

```css
:root {
  /* Colors */
  --color-primary: #007bff;
  --color-primary-dark: #0056b3;
  --color-primary-light: #66b3ff;
  --color-success: #28a745;
  --color-warning: #ffc107;
  --color-danger: #dc3545;
  --color-info: #17a2b8;
  
  /* Backgrounds */
  --color-bg-main: #ffffff;
  --color-bg-secondary: #f8f9fa;
  --color-bg-card: #ffffff;
  
  /* Typography */
  --font-family-primary: "Segoe UI", Roboto, Arial, sans-serif;
  --font-size-base: 16px;
  --line-height-base: 1.5;
  
  /* Layout */
  --layout-max-width: 1200px;
  --layout-spacing-small: 8px;
  --layout-spacing-medium: 16px;
  --layout-spacing-large: 24px;
  --layout-border-radius: 8px;
  
  /* Animation */
  --animation-duration: 300ms;
}
```

## 🌙 Dark Mode

### Enable Dark Mode
```php
// In admin panel, set enable_dark_mode to true
$branding->set('enable_dark_mode', true, 'boolean');
```

### Add Theme Switcher
```php
echo themeSwitcher();
```

### CSS Classes
```css
.dark-theme {
  --color-bg-main: #121212;
  --color-bg-secondary: #1e1e1e;
  --color-bg-card: #2d2d2d;
  --color-text-primary: #ffffff;
  --color-text-secondary: #b3b3b3;
}
```

## 🎭 Theme Presets

### Built-in Presets
1. **Default Blue** - Clean blue theme
2. **Dark Mode** - Dark theme for low light
3. **YouTube Red** - YouTube-inspired red theme
4. **Twitch Purple** - Twitch-inspired purple theme
5. **Green Nature** - Nature-inspired green theme

### Create Custom Preset
```php
$settings = [
    'color_primary' => '#ff6b6b',
    'color_primary_dark' => '#ee5a52',
    'color_bg_main' => '#ffffff'
];

$branding->savePreset('My Custom Theme', 'A beautiful custom theme', $settings);
```

### Apply Preset
```php
$branding->applyPreset('Dark Mode');
```

## 🚀 Performance

### Caching
- CSS is cached for 1 hour by default
- Database queries are optimized
- Settings are loaded once per request

### Cache Management
```php
// Clear CSS cache when settings change
$branding->clearCache('css');

// Set custom cache duration
$branding->setCacheDuration(7200); // 2 hours
```

## 🔧 API Reference

### VBranding Class Methods

#### Core Methods
- `getInstance()` - Get singleton instance
- `get($key, $default)` - Get setting value
- `set($key, $value, $type)` - Set setting value
- `getByCategory($category)` - Get settings by category
- `getCategories()` - Get all categories

#### CSS Generation
- `generateCSS()` - Generate dynamic CSS
- `clearCache($type)` - Clear cache

#### Presets
- `applyPreset($name)` - Apply theme preset
- `savePreset($name, $description, $settings)` - Save preset
- `getPresets()` - Get all presets

#### Utilities
- `getLogo($type, $class, $alt)` - Generate logo HTML
- `getBadge($type, $text, $class)` - Generate badge HTML
- `getSiteInfo()` - Get site information

### Helper Functions

#### Basic Functions
- `getBranding()` - Get branding instance
- `brandingGet($key, $default)` - Get setting value
- `siteInfo()` - Get site information
- `siteLogo($type, $class, $alt)` - Generate logo
- `badge($type, $text, $class)` - Generate badge

#### Template Functions
- `brandingMetaTags()` - Generate meta tags
- `dynamicCSSLink()` - Generate CSS link
- `inlineCSSVariables()` - Generate inline CSS vars
- `brandedHeader($includeNav)` - Generate header
- `brandedFooter()` - Generate footer

#### Component Functions
- `videoCard($video, $showBadges)` - Generate video card
- `userAvatar($user, $size, $showBadges)` - Generate avatar
- `notification($message, $type, $dismissible)` - Generate notification
- `button($text, $type, $href, $attributes)` - Generate button
- `formInput($name, $type, $value, $attributes)` - Generate input

#### Theme Functions
- `isDarkMode()` - Check if dark mode enabled
- `getCurrentTheme()` - Get current theme name
- `themeSwitcher()` - Generate theme switcher

## 🧪 Testing

### Test Page
Visit `/test_branding_system.php` to see all features in action.

### Features Demonstrated
- Color palette display
- Badge showcase
- Button styles
- Video cards
- User avatars
- Form elements
- Notifications
- Theme switching
- Live preview

## 🔒 Security

### CSRF Protection
All admin forms include CSRF tokens:
```php
echo VSecurity::getCSRFField('branding_update');
```

### Input Validation
- Setting keys are validated against whitelist
- Values are sanitized based on type
- SQL injection protection with prepared statements

### Access Control
- Admin interface requires authentication
- Settings modification requires proper permissions

## 🎯 Best Practices

### Performance
1. Use CSS variables for consistent theming
2. Cache generated CSS
3. Minimize database queries
4. Optimize image assets

### Design
1. Maintain color contrast ratios
2. Test in both light and dark modes
3. Ensure mobile responsiveness
4. Use consistent spacing

### Development
1. Use helper functions in templates
2. Validate all user inputs
3. Handle errors gracefully
4. Document custom modifications

## 🚀 Future Enhancements

### Planned Features
- **Advanced Animations** - Custom animation presets
- **Gradient Support** - Gradient color options
- **Font Upload** - Custom font file uploads
- **Theme Marketplace** - Share and download themes
- **A/B Testing** - Test different themes
- **Import/Export** - Theme backup and restore
- **Mobile Themes** - Separate mobile theming
- **Seasonal Themes** - Automatic seasonal changes

### Integration Ideas
- **Social Login** - Branded social login buttons
- **Email Templates** - Branded email designs
- **Mobile App** - Theme sync with mobile app
- **Analytics** - Theme performance tracking

## 📞 Support

For questions or issues with the branding system:

1. Check the test page: `/test_branding_system.php`
2. Review the admin interface: `/f_modules/m_backend/branding_management.php`
3. Examine the database tables for data integrity
4. Check browser console for JavaScript errors
5. Verify file permissions for uploads

---

**The EasyStream Branding System gives you complete control over your platform's appearance. Make it truly yours!** 🎨✨