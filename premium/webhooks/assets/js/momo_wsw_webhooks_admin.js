/*global jQuery*/
/*global define */
/*global window */
/*global this*/
/*global location*/
/*global document*/
/*global momowsw_admin*/
/*global console*/
/*jslint this*/
/**
 * momowsw Admin Script
 */
jQuery(document).ready(function ($) {
    "use strict";
    /********************************/
    /** For Webhooks Enable Disable */
    /********************************/
    $('body').on('click', '.momowsw-webhooks-enable', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $form = $btn.closest('#momowsw-momo-wsw-cs-webhooks');
        var $working = $btn.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var type = $btn.data('type');
        var source = $btn.data('source');
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.action = 'momowsw_enable_webhooks';
        ajaxdata.type = type;
        ajaxdata.source = source;
        $.ajax({
            beforeSend: function () {
                $form.slideUp();
                $working.addClass('show');
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                if ('success' === data.status) {
                    console.log(data.message);
                }
            },
            complete: function () {
                $form.slideDown();
                $working.removeClass('show');
                location.reload();
            }
        });
    });
    $('body').on('click', '.momowsw-webhooks-delete', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $form = $btn.closest('#momowsw-momo-wsw-cs-webhooks');
        var $working = $btn.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var type = $btn.data('type');
        var source = $btn.data('source');
        var id = $btn.data('id');
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.action = 'momowsw_delete_webhooks';
        ajaxdata.type = type;
        ajaxdata.source = source;
        ajaxdata.id = id;
        $.ajax({
            beforeSend: function () {
                $form.slideUp();
                $working.addClass('show');
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                if ('success' === data.status) {
                    console.log(data.message);
                }
            },
            complete: function () {
                $form.slideDown();
                $working.removeClass('show');
                location.reload();
            }
        });
    });
    $('body').on('click', '.momowsw-wsw-webhooks-enable', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $form = $btn.closest('#momowsw-momo-wsw-cs-webhooks');
        var $working = $btn.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var type = $btn.data('type');
        var source = $btn.data('source');
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.action = 'momowsw_wsw_enable_webhooks';
        ajaxdata.type = type;
        ajaxdata.source = source;
        $.ajax({
            beforeSend: function () {
                $form.slideUp();
                $working.addClass('show');
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                if ('success' === data.status) {
                    console.log(data.message);
                }
            },
            complete: function () {
                $form.slideDown();
                $working.removeClass('show');
                location.reload();
            }
        });
    });
    $('body').on('click', '.momowsw-wsw-webhooks-delete', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $form = $btn.closest('#momowsw-momo-wsw-cs-webhooks');
        var $working = $btn.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var type = $btn.data('type');
        var source = $btn.data('source');
        var id = $btn.data('id');
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.action = 'momowsw_wsw_delete_webhooks';
        ajaxdata.type = type;
        ajaxdata.source = source;
        ajaxdata.id = id;
        $.ajax({
            beforeSend: function () {
                $form.slideUp();
                $working.addClass('show');
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                if ('success' === data.status) {
                    console.log(data.message);
                }
            },
            complete: function () {
                $form.slideDown();
                $working.removeClass('show');
                location.reload();
            }
        });
    });
});
