<?php
/**
 * Plugin Name: آرایش مجازی آرمو برای ووکامرس
 * Description: افزونه آرایش مجازی سرویس آرمو برای ووکامرس
 * Version: 1.0.3
 * Author: Hamed Movasaqpoor
 * Author URI: https://github.com/ihamedm/
 * Plugin URI: https://github.com/ihamedm/wp-armo-makeup
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-armo-makeup
 * Domain Path: /languages
 * Requires PHP: 7.2
 * WC requires at least: 3.0.0
 * WC tested up to: 8.0.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Declare HPOS compatibility
add_action('before_woocommerce_init', function() {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

// Define plugin constants
define('WP_ARMO_MAKEUP_VERSION', '1.0.3');
define('WP_ARMO_MAKEUP_PATH', plugin_dir_path(__FILE__));
define('WP_ARMO_MAKEUP_URL', plugin_dir_url(__FILE__));

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'ArmoMakeup\\';
    $base_dir = WP_ARMO_MAKEUP_PATH . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    // Simple conversion: Namespace\ClassName -> class-classname.php
    $file = $base_dir . 'class-' . str_replace('_', '-', strtolower($relative_class)) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function wp_armo_makeup_init() {
    // Check if necessary classes exist before instantiating
    if (class_exists('ArmoMakeup\\Loader')) {
        $loader = new ArmoMakeup\Loader();
        $loader->run();
    } else {
        // Handle error: Loader class not found
        // Maybe log an error or display an admin notice
        error_log('WP Armo Makeup: Loader class not found. Autoloading might have failed.');
    }
}
add_action('plugins_loaded', 'wp_armo_makeup_init');

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'wp_armo_makeup_activate');
register_deactivation_hook(__FILE__, 'wp_armo_makeup_deactivate');

// Add Settings link to plugin actions
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wp_armo_makeup_add_settings_link');

function wp_armo_makeup_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=virtual-makeup-settings') . '">' . __('تنظیمات', 'wp-armo-makeup') . '</a>';
    array_unshift($links, $settings_link); // Add to the beginning of the links array
    return $links;
}


function wp_armo_makeup_activate() {
    // Add rewrite rules
    add_rewrite_rule(
        'makeup/([0-9]+)/?$',
        'index.php?makeup=1&product_id=$matches[1]',
        'top'
    );
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

function wp_armo_makeup_deactivate() {
    // Flush rewrite rules on deactivation
    flush_rewrite_rules();
}