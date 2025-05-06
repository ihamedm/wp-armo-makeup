<?php
namespace ArmoMakeup;

class Admin_Settings {

    public function init() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_submenu_page(
            'woocommerce',
            'تنظیمات آرایش مجازی آرمو',
            'آرایش مجازی',
            'manage_options',
            'virtual-makeup-settings',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('virtual_makeup_settings', 'virtual_makeup_token');
        register_setting('virtual_makeup_settings', 'virtual_makeup_color_attribute');

        add_settings_section(
            'virtual_makeup_main_section',
            'تنظیمات API', // Translated
            null,
            'virtual-makeup-settings'
        );

        add_settings_field(
            'virtual_makeup_token',
            'توکن API', // Translated
            array($this, 'render_token_field'),
            'virtual-makeup-settings',
            'virtual_makeup_main_section'
        );

        add_settings_field(
            'virtual_makeup_color_attribute',
            'ویژگی رنگ', // Translated
            array($this, 'render_color_attribute_field'),
            'virtual-makeup-settings',
            'virtual_makeup_main_section'
        );
    }

    public function render_token_field() {
        $token = get_option('virtual_makeup_token');
        ?>
        <input type="text"
               name="virtual_makeup_token"
               value="<?php echo esc_attr($token); ?>"
               class="regular-text"
               placeholder="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...">
        <p class="description">
            <?php _e('توکن API آرایش مجازی خود را وارد کنید.', 'wp-armo-makeup'); // Translated ?>
        </p>
        <?php
    }

    public function render_color_attribute_field() {
        $attributes = wc_get_attribute_taxonomies();
        $options = array('' => __('یک ویژگی انتخاب کنید', 'wp-armo-makeup')); // Translated
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $options['pa_' . $attribute->attribute_name] = $attribute->attribute_label;
            }
        }
        $selected_attribute = get_option('virtual_makeup_color_attribute');
        ?>
        <select name="virtual_makeup_color_attribute" class="regular-text">
            <?php foreach ($options as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_attribute, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">
            <?php _e('ویژگی محصول ووکامرس که برای رنگ‌ها استفاده می‌شود را انتخاب کنید.', 'wp-armo-makeup'); ?>
             <?php _e('هر ویژگی که انتخاب شود یک فیلد انتخاب رنگ به مقادیر آن اضافه خواهد شد.', 'wp-armo-makeup');?>
        </p>
        <?php
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('virtual_makeup_settings');
                do_settings_sections('virtual-makeup-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}