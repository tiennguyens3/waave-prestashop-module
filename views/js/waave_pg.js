jQuery(document).ready(function () {
    jQuery.fancybox({
        content: 'Thank you for your order. We are now redirecting you to Waave to make payment.',
        height: 'auto',
        closeBtn: false,
        closeClick: false,
        helpers: {
            overlay: {
                closeClick: false
            }
        },
        afterShow: function() {
            jQuery("#waave_payment_form").submit();
        }
    });
});