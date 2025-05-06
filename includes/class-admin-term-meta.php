<?php
namespace ArmoMakeup;

class Admin_Term_Meta {

    private $color_attribute_taxonomy;

    public function init() {
        $this->color_attribute_taxonomy = get_option('virtual_makeup_color_attribute');

        if (!empty($this->color_attribute_taxonomy)) {
            add_action($this->color_attribute_taxonomy . '_add_form_fields', array($this, 'add_term_color_picker_field'), 10, 1);
            add_action($this->color_attribute_taxonomy . '_edit_form_fields', array($this, 'edit_term_color_picker_field'), 10, 2);
            add_action('created_' . $this->color_attribute_taxonomy, array($this, 'save_term_color_picker_field'), 10, 1);
            add_action('edited_' . $this->color_attribute_taxonomy, array($this, 'save_term_color_picker_field'), 10, 1);

            add_action('admin_enqueue_scripts', array($this, 'enqueue_color_picker_scripts'));
        }
    }

    public function add_term_color_picker_field($taxonomy) {
        ?>
        <div class="form-field term-color-wrap">
            <label for="term-color"><?php _e('رنگ', 'wp-armo-makeup');  ?></label>
            <input name="term_color" value="#ffffff" class="color-picker" id="term-color" />
            <p><?php _e('رنگ هگزادسیمال این ترم را انتخاب کنید. برای آرایش مجازی استفاده می‌شود.', 'wp-armo-makeup'); // Translated ?></p>
        </div>
        <?php
    }

    public function edit_term_color_picker_field($term, $taxonomy) {
        $color = get_term_meta($term->term_id, 'color', true);
        $color = !empty($color) ? $color : '#ffffff';
        ?>
        <tr class="form-field term-color-wrap">
            <th scope="row"><label for="term-color"><?php _e('رنگ', 'wp-armo-makeup');  ?></label></th>
            <td>
                <input name="term_color" value="<?php echo esc_attr($color); ?>" class="color-picker" id="term-color" />
                <p class="description"><?php _e('رنگ هگزادسیمال این ترم را انتخاب کنید. برای آرایش مجازی استفاده می‌شود.', 'wp-armo-makeup'); // Translated ?></p>
            </td>
        </tr>
        <?php
    }

    public function save_term_color_picker_field($term_id) {
        if (isset($_POST['term_color']) && !empty($_POST['term_color'])) {
            $color = sanitize_hex_color($_POST['term_color']);
            if ($color) {
                update_term_meta($term_id, 'color', $color);
            } else {
                 delete_term_meta($term_id, 'color');
            }
        } else {
             delete_term_meta($term_id, 'color');
        }
    }

    public function enqueue_color_picker_scripts($hook_suffix) {
        if ('term.php' === $hook_suffix || 'edit-tags.php' === $hook_suffix) {
            $screen = get_current_screen();
            if ($screen && ('term' === $screen->base || 'edit-tags' === $screen->base) && $screen->taxonomy === $this->color_attribute_taxonomy) {
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');
                wp_add_inline_script('wp-color-picker', 'jQuery(document).ready(function($){ $(".color-picker").wpColorPicker(); });');
            }
        }
    }
}