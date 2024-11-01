/*global jQuery*/
/*global document*/
/*global momowsw_admin*/
/*jslint this*/
/**
 * momowsw Admin Script
 */
jQuery(document).ready(function ($) {
    "use strict";
    /**
     * Export Other (Page, Blog, Order)
     */
    $('body').on('click', '#momo-wsw-export-to-shopify-others', function (e) {
        console.log('Yesloow');
        e.preventDefault();
        var $btn = $(this);
        var $box = $btn.closest('.momo-be-post-submitbox');
        var type = $btn.data('type');
        var $msgBox = $box.find('.momo-be-post-sb-message');
        $msgBox.momowswNormalize();
        var post_id = $btn.data('post_id');
        var shopify_id = $btn.data('shopify_id');
        var ptype = $btn.data('ptype');
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.post_id = post_id;
        ajaxdata.shopify_id = shopify_id;
        ajaxdata.type = type;
        ajaxdata.ptype = ptype;
        ajaxdata.action = 'momowsw_sync_single_others_to_shopify';
        $.ajax({
            beforeSend: function () {
                $btn.momowswsspinnner();
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {console.log(data);
                $msgBox.addClass(data.status);
                $msgBox.html(data.msg);
                $msgBox.addClass('show');
                if ('success' === data.status) {
                    if ('insert' === type) {
                        $btn.html(momowsw_admin.postupdate_btn_text);
                        $btn.attr('data-type', 'update');
                        $btn.attr('data-shopify_id', data.shopify_id);
                    }
                }
            },
            complete: function () {
                $btn.momowswsspinnner();
            }
        });
    });
});