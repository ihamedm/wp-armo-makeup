<?php
namespace ArmoMakeup;

class Routes {
    public function init() {
        add_action('init', array($this, 'add_makeup_rewrite_rule'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'handle_makeup_template'));
    }

    public function add_makeup_rewrite_rule() {
        add_rewrite_rule(
            'makeup/([0-9]+)/?$',
            'index.php?makeup=1&product_id=$matches[1]',
            'top'
        );
    }

    public function add_query_vars($vars) {
        $vars[] = 'makeup';
        $vars[] = 'product_id';
        return $vars;
    }

    public function handle_makeup_template() {
        if (get_query_var('makeup')) {
            $product_id = get_query_var('product_id');
            
            if (!$product_id || !wc_get_product($product_id)) {
                wp_redirect(home_url());
                exit;
            }

            include WP_ARMO_MAKEUP_PATH . 'templates/makeup.php';
            exit;
        }
    }
}