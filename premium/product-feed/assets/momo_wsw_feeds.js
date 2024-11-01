/*global jQuery*/
/*global momowsw_admin*/
/**
 * Product Feeds Regenration
*/
jQuery(document).ready(function ($) {
    "use strict";
    $('body').on('click', '#momowsw-regenerate-pffeeds', function (e) {
        console.log('Regenerating Feeds');
        var $button = $(this);
        var $container = $button.closest('.momo-be-admin-content');
        var $date = $container.find('.momowsw-generated-at-value');
        var $tab = $container.closest('.momo-be-main-tabcontent');
        var $working = $tab.find('.momo-be-working');
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.action = 'momowsw_regenerate_product_feeds';
        $.ajax({
            beforeSend: function () {
                $working.addClass('show');
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                if ('good' === data.status) {
                    $date.html(data.date);
                }
            },
            complete: function () {
                $working.removeClass('show');
            }
        });
    });
});