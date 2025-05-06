jQuery(document).ready(function($) {
    MicroModal.init({
        disableFocus: false,
        disableScroll: true,
        awaitOpenAnimation: false,
        awaitCloseAnimation: false,
        onShow: function(modal) {
            loadMakeupIframe();
        },
        onClose: function(modal) {
            $('#modal-virtual-makeup-content').html(
                '<div class="loading-spinner" style="display: none;">در حال دریافت اطلاعات...</div>'
            );
        }
    });

});

function loadMakeupIframe() {
    const $content = jQuery('#modal-virtual-makeup-content');
    const $spinner = $content.find('.loading-spinner');
    
    $spinner.show();


    jQuery.ajax({
        url: armoMakeup.ajaxUrl,
        type: 'POST',
        data: {
            action: 'load_makeup_iframe',
            product_id: armoMakeup.productId,
            nonce: armoMakeup.nonce
        },
        success: function(response) {
            $spinner.hide();
            if (response.success) {
                $content.html(response.data.iframe);
            } else {
                throw new Error(response.data || 'Unknown error occurred');
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
            $spinner.hide();
            $content.html('<div class="error">خطا در بارگذاری آرایش مجازی</div>');
        }
    });
}