<?php

namespace ArmoMakeup;

class Updater
{
    private $update_checker;

    public function __construct(){
        include_once __DIR__ . '/puc/plugin-update-checker.php';
        add_action('admin_notices', array($this, 'show_new_version_notice'));
    }

    public function check_for_update()
    {
        $this->update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
            'https://github.com/ihamedm/wp-armo-makeup',
            __FILE__,
            'wp-armo-makeup'
        );
        $this->update_checker->setBranch('main');

    }

    public function show_new_version_notice(){
        $state = $this->update_checker->getUpdateState();
        $update = $state->getUpdate();

        // Check if an update is available
        if ($update !== null) {
            $current_version = $this->update_checker->getInstalledVersion();
            $new_version = $update->version;

            // Only show notification if new version is greater than installed version
            if (version_compare($new_version, $current_version, '>')) {
                // Display update notification
                echo '<div class="notice notice-warning is-dismissible">';
                echo '<p><strong>هشدار بروزرسانی به نسخه جدید پلاگین آرایش مجازی آرمو:</strong> نسخه ' . esc_html($new_version) . ' پلاگین آرایش مجازی آرمو منتشر شد. لطفا هر چه سریعتر بروزرسانی کنید. ';
                echo 'نسخه فعلی شما ' . esc_html($current_version) . ' می باشد. ';
                echo '<a href="' . esc_url(admin_url('plugins.php')) . '">برو به صفحه افزونه ها</a></p>';
                echo '</div>';
            }
        }
    }

}