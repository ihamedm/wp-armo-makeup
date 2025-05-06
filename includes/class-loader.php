<?php
namespace ArmoMakeup;


class Loader {
    private $frontend;
    private $admin;
    private $routes;
    private $updater;

    public function __construct() {
        $this->frontend = new Frontend();
        $this->admin = new Admin();
        $this->updater = new Updater();
        $this->routes = new Routes();
    }

    public function run() {
        // Initialize frontend
        $this->frontend->init();

        // Initialize admin only in admin area
        if (is_admin()) {
            $this->admin->init();
            $this->updater->check_for_update();
        }

        // Initialize routes
        $this->routes->init();
    }
}