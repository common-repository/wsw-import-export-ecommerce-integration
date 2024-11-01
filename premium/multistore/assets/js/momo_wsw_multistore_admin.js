/*global jQuery*/
/*global define */
/*global window */
/*global this*/
/*global location*/
/*global document*/
/*global momowsw_admin_multistore*/
/*global momowsw_admin*/
/*global console*/
/*jslint this*/
/**
 * momowsw eBay Admin Script
 */
jQuery(document).ready(function ($) {
    "use strict";
    $("#multistore-select-desired-product").select2({
        placeholder: momowsw_admin_multistore.placeholder,
        templateSelection: function (data, container) {
            if (data.element) {
                $(container).addClass($(data.element).attr("data-type"));
            }
            return data.text;
        },
        ajax: {
            url: momowsw_admin_multistore.ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search query
                    security: momowsw_admin_multistore.momowsw_ajax_nonce,
                    action: 'momowsw_woo_product_list_search'
                };
            },
            processResults: function (data) {
                var options = [];
                if (data) {

                    $.each(data, function (index, text) {
                        options.push({id: text[0], text: text[1]});
                    });

                }
                return {
                    results: options
                };
            },
            cache: true
        },
        minimumInputLength: 3,
        width: 250,
        open: function (e) {
            var data = e.params.data;
            console.log(data);
        }
    });
    /**
     * For multistore
     */
    function close_popbox($container) {
        $container.closest('.fp-popbox-background').removeClass('is-visible');
    }
    function refreshContainer($container) {
        var $working = $container.find('.momo-be-working');
        $working.removeClass('show');
        $container.find('.form-holder').removeClass('blur');
    }
    /* Contact Form Interactions */
    $('.add_edit_store_popbox').on('click', function (event) {
        event.preventDefault();
        var type = $(this).data('type');
        if ('add' === type) {
            $('.fp-popbox-background').addClass('is-visible');
            $('.fp-popbox-background').find('.momo-fp-popbox-container').data('type', 'add');
        } else {
            $('.fp-popbox-background').addClass('is-visible');
            $('.fp-popbox-background').find('.momo-fp-popbox-container').data('type', 'edit');
        }
        var $container = $('.fp-popbox-background').find('.momo-fp-popbox-container');
        refreshContainer($container);
    });

    $('.fp-popbox-background').on('click', function (event) {
        if ($(event.target).is('.fp-popbox-background') || $(event.target).is('.momo-fp-close-pb')) {
            event.preventDefault();
            $(this).removeClass('is-visible');
        }
    });
    $('.fp-popbox-background input').on('focus', function () {
        $('.fp-popbox-background input').removeClass('red');
    });
    $('body').on('click', '.add_edit_store_submit', function (e) {
        e.preventDefault();
        var $container = $(this).closest('.momo-fp-popbox-container');
        refreshContainer($container);

        $container.find('input').removeClass('red');
        var type = $container.data('type');
        var isvalid = true;
        var inputdata = {};
        $container.find('input').each(function () {
            var element = $(this);
            if (element.val() === "") {
                isvalid = false;
                element.addClass('red');
            } else {
                inputdata[element.attr('name')] = element.val();
            }
        });
        if (false === isvalid) {
            return;
        }
        var $working = $container.find('.momo-be-working');
        var ajaxdata = {};
        ajaxdata.input = inputdata;
        ajaxdata.type = type;
        ajaxdata.action = 'momowsw_multistore_update_store_data';
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        $.ajax({
            beforeSend: function () {
                $working.addClass('show');
                $container.find('.form-holder').addClass('blur');
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                if ('good' === data.status) {
                    var $form = $('#momo-momowsw-admin-multistore-multiple-stores-form');
                    $form.find('ul.multistore-server-list').html(data.content);
                    /* $form.find('#multi_stores_data_hidden').val(data.msdata); */
                }
            },
            complete: function () {
                close_popbox($container);
            }
        });
    });
    $('body').on('click', 'ul.multistore-server-list li i', function () {
        var $li = $(this).closest('li');
        var $tab = $li.closest('.momo-be-main-tabcontent');
        var $working = $tab.find('.momo-be-working');
        var shop_url = $li.data('shop');
        var ajaxdata = {};
        ajaxdata.type = 'delete';
        ajaxdata.shop = shop_url;
        ajaxdata.action = 'momowsw_multistore_update_store_data';
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
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
                    var $form = $('#momo-momowsw-admin-multistore-multiple-stores-form');
                    $form.find('ul.multistore-server-list').html(data.content);
                    /* $form.find('#multi_stores_data_hidden').val(data.msdata); */
                }
            },
            complete: function () {
                $working.removeClass('show');
            }
        });
    });
    $('body').on('change', 'input[name="momo_wsw_multistore_multiple_stores[enable_multiple_stores]"]', function (e) {
        var $input = $(this);
        var val = 'off';
        var $tab = $input.closest('.momo-be-main-tabcontent');
        if ($input.is(":checked")) {
            val = 'on';
            $input.val(val);
        } else {
            val = 'off';
            $input.val(val);
        }
        var $working = $tab.find('.momo-be-working');
        var ajaxdata = {};
        ajaxdata.action = 'momowsw_multistore_enable_disable_multi_stores';
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.val = val;
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
                }
            },
            complete: function () {
                $working.removeClass('show');
            }
        });
    });
    /**
     * Clear Order Logs
     */
    $('body').on('click', '.momowsw-admin-clear-multi-store-order-logs', function () {
        var $form = $(this).closest('.momo-be-admin-content');
        var $working = $form.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var $textarea = $form.find('textarea[id="momo_wsw_cron_logs_textarea"]');
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.action = 'momowsw_clear_multi_store_order_logs';
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
                    $textarea.val(data.msg);
                }
            },
            complete: function () {
                $working.removeClass('show');
            }
        });
    });
});