<?php
namespace ArmoMakeup;

/**
 * Main Admin class for the plugin.
 * Initializes all admin-related components.
 */
class Admin {

    /**
     * Initializes the admin components of the plugin.
     *
     * Instantiates and initializes classes responsible for:
     * - Product meta boxes
     * - Plugin settings page
     * - Term meta fields (color picker)
     */
    public function init() {
        // Instantiate and initialize each admin component
        $admin_product_meta = new Admin_Product_Meta();
        $admin_product_meta->init();

        $admin_settings = new Admin_Settings();
        $admin_settings->init();

        $admin_term_meta = new Admin_Term_Meta();
        $admin_term_meta->init();

    }

}