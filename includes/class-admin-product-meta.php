<?php
namespace ArmoMakeup;

class Admin_Product_Meta {

    public function init() {
        add_filter('woocommerce_product_data_tabs', array($this, 'add_virtual_makeup_product_data_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'add_virtual_makeup_product_data_panel'));
        add_action('woocommerce_process_product_meta', array($this, 'save_virtual_makeup_fields'));
    }

    public function add_virtual_makeup_product_data_tab($tabs) {
        $tabs['virtual_makeup'] = array(
            'label'    => 'آرایش مجازی',
            'target'   => 'virtual_makeup_product_data',
            'class'    => array('show_if_simple', 'show_if_variable'),
            'priority' => 21
        );
        return $tabs;
    }

    public function add_virtual_makeup_product_data_panel() {
        global $post;
        ?>
        <div id="virtual_makeup_product_data" class="panel woocommerce_options_panel">
            <?php
            woocommerce_wp_checkbox(array(
                'id'          => 'is_enable_virtual_makeup',
                'label'       =>'نمایش آرایش مجازی',
                'desc_tip'    => true,
            ));

            woocommerce_wp_select(array(
                'id'          => 'face',
                'label'       => 'المان آرایشی',
                'desc_tip'    => true,
                'description' => 'یکی از المان های از پیش تعریف شده را می توانید انتخاب کنید.',
                'options'     => array(
                    'lips' => 'لب‌ها',
                    'eyeshadow' => 'سایه چشم',
                    'eyepencil' => 'مداد چشم',
                    'eyelashes' => 'مژه‌ها',
                    'blush' => 'رژگونه',
                    'concealer' => 'کانسیلر',
                    'foundation' => 'کرم پودر',
                    'brows' => 'ابروها',
                    'eyeliner' => 'خط چشم',
                ),
            ));
            ?>
        </div>
        <?php
    }

    public function save_virtual_makeup_fields($post_id) {
        $is_enabled = isset($_POST['is_enable_virtual_makeup']) ? 'yes' : 'no';
        update_post_meta($post_id, 'is_enable_virtual_makeup', $is_enabled);

        if (isset($_POST['face'])) {
            update_post_meta($post_id, 'face', sanitize_text_field($_POST['face']));
        }
    }
}