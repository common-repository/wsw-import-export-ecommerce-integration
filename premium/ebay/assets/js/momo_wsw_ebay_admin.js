/*global jQuery*/
/*global define */
/*global window */
/*global this*/
/*global location*/
/*global document*/
/*global momowsw_admin_ebay*/
/*global console*/
/*jslint this*/
/**
 * momowsw eBay Admin Script
 */
jQuery(document).ready(function ($) {
    "use strict";
    /**
     * Reset transient on Authorization Button click
     */
    $('body').on('click', '.momowsw-reset-ebay-transient', function (e) {
        var $btn = $(this);
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin_ebay.momowsw_ajax_nonce;
        ajaxdata.action = 'momowsw_reset_ebay_keys_transient';
        $.ajax({
            beforeSend: function () {
                $btn.momowswsspinnner();
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
                $btn.momowswsspinnner();
            }
        });
    });
    /**
     * Export Product eBay
     */
    $('body').on('click', '#momo-wsw-export-to-ebay', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $box = $btn.closest('.momo-be-post-submitbox');
        var type = $btn.data('type');
        var $msgBox = $box.find('.momo-be-post-sb-message');
        $msgBox.momowswNormalize();
        var product_id = $btn.data('product_id');
        var ebay_id = $btn.data('ebay_id');

        var ajaxdata = {};
        ajaxdata.security = momowsw_admin_ebay.momowsw_ajax_nonce;
        ajaxdata.product_id = product_id;
        ajaxdata.ebay_id = ebay_id;
        ajaxdata.type = type;
        ajaxdata.action = 'momowsw_sync_single_product_to_ebay';
        $.ajax({
            beforeSend: function () {
                $btn.momowswsspinnner();
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin_ebay.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                $msgBox.addClass(data.status);
                $msgBox.html(data.msg);
                $msgBox.addClass('show');
                if ('success' === data.status) {
                    if ('insert' === type) {
                        $btn.html(momowsw_admin_ebay.postupdate_btn_text);
                        $btn.attr('data-type', 'update');
                        $btn.attr('data-ebay_id', data.ebay_id);
                    }
                }
            },
            complete: function () {
                $btn.momowswsspinnner();
            }
        });
    });
    $('body').on('click', '.momowsw-ebay-location-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var type = $btn.data('type');
        var $main = $btn.closest('.momowsw-es-location-main');
        var $old = $main.find('.ebay-old-location-form');
        var $new = $main.find('.ebay-new-location-form');
        var $empty = $main.find('.location-empty-form');
        if ('new' === type) {
            $old.slideUp();
            $new.slideDown();
        } else {
            $new.slideUp();
            $old.slideDown();
        }
        $empty.slideUp();
    });
    $('body').on('click', '.momowsw-ebay-back-to-location', function (e) {
        var $btn = $(this);
        var $main = $btn.closest('.momowsw-es-location-main');
        var $old = $main.find('.ebay-old-location-form');
        var $new = $main.find('.ebay-new-location-form');
        var $empty = $main.find('.location-empty-form');
        $old.slideUp();
        $new.slideUp();
        $empty.show().slideDown();
    });
    $('body').on('click', '.momowsw-ebay-generate-location-id', function (e) {
        var $btn = $(this);
        var $form = $btn.closest('.ebay-new-location-form');
        var $working = $form.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var $main = $btn.closest('.momowsw-es-location-main');
        var $mBox = $main.find('.momo-be-msg-block');
        var data = {};
        $mBox.hide();
        var empty = false;
        $form.find('input').each(function () {
            if ($(this).val() === '') {
                empty = true;
            }
            data[$(this).attr('name')] = $(this).val();
        });
        $form.find('select').each(function () {
            if ($(this).val() === '') {
                empty = true;
            }
            data[$(this).attr('name')] = $(this).val();
        });
        if (empty === true) {
            $mBox.html('Please enter all required field(s).');
            $mBox.show();
            return;
        }
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin_ebay.momowsw_ajax_nonce;
        ajaxdata.data = data;
        ajaxdata.action = 'momowsw_create_and_save_merchant_location';
        $.ajax({
            beforeSend: function () {
                $form.slideUp();
                $working.addClass('show');
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin_ebay.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                if (data.status === 'error') {
                    $mBox.html(data.message);
                    $mBox.show();
                } else if (data.status === 'good') {
                    $mBox.html(data.message);
                    $mBox.show();
                }
            },
            complete: function () {
                $working.removeClass('show');
            }
        });
    });
    $('body').on('click', '.momowsw-ebay-save-old-location', function (e) {
        var $btn = $(this);
        var $form = $btn.closest('.ebay-old-location-form');
        var $working = $form.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var $main = $btn.closest('.momowsw-es-location-main');
        var $mBox = $main.find('.momo-be-msg-block');
        $mBox.hide();
        var empty = false;
        var location_key = $form.find('input[name="old_unique_id"]').val();
        if ('' === location_key) {
            empty = true;
        }
        if (empty === true) {
            $mBox.html('Please enter all required field(s).');
            $mBox.show();
            return;
        }
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin_ebay.momowsw_ajax_nonce;
        ajaxdata.location_key = location_key;
        ajaxdata.action = 'momowsw_check_and_save_old_location';
        $.ajax({
            beforeSend: function () {
                $form.slideUp();
                $working.addClass('show');
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin_ebay.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                if (data.status === 'error') {
                    $mBox.html(data.message);
                    $mBox.show();
                } else if (data.status === 'good') {
                    $mBox.html(data.message);
                    $mBox.show();
                }
            },
            complete: function () {
                $working.removeClass('show');
            }
        });
    });
    /**
     * Fetch Product(s)
     */
    $('body').on('click', '.ebay_fetch_by', function (e) {
        e.preventDefault();
        var caller = $(this).data('caller');
        var $form = $(this).closest('.momo-be-admin-content');
        var $working = $form.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var $main_block = $form.find('.momowsw-import-main');
        var $report_block = $form.find('.momo-be-result-block');
        var current_list = $report_block.find('input[name="momowsw_generated_items"]').val();
        var $dnfip = $form.find('input[name="momowsw_imported_donot_fetch"]');
        var $msg = $main_block.find('.momo-be-msg-block');
        $msg.hide();
        var selectVal = $form.find('select[name="momowsw_import_by"]').val();

       /*  var selectServer = $form.find('select[name="momowsw_shop_url"]').val(); */

        var ajaxdata = {};
       /*  ajaxdata.selected_server = selectServer; */
        ajaxdata.security = momowsw_admin_ebay.momowsw_ajax_nonce;
        ajaxdata.current_list = current_list;
        ajaxdata.caller = caller;
        if ($dnfip.is(":checked")) {
            ajaxdata.dnfip = 'on';
        } else {
            ajaxdata.dnfip = 'off';
        }
        var $block = $form.find('.momo-wsw-by-item-id.momo-be-option-block');
        if ('item_id' === selectVal) {
            var SKU = $block.find('input[name="ebay_sku"]').val();
            if ('' === SKU || 'unidefined' === SKU) {
                $form.find('input[name="ebay_sku"]').focus();
                var name = 'empty_' + caller + '_sku';
                $msg.html(momowsw_admin_ebay[name]);
                $msg.show();
                return;
            }
            ajaxdata.action = 'momowsw_ebay_fetch_by_sku';
            ajaxdata.sku_id = SKU;
        } else if ('all_items' === selectVal) {
            var $blockp = $form.find('#momo-by-all-item.momo-be-option-block');
            var limit = $blockp.find('select[name="momowsw_product_limit"]').val();
            var status = $form.find('select[name="momowsw_page_status"]').val();
            var olimit = $form.find('select[name="momowsw_page_limit"]').val();
            ajaxdata.action = 'momowsw_fetch_all_items';
            ajaxdata.olimit = olimit;
            ajaxdata.limit = limit;
            ajaxdata.current_page = 1;
            ajaxdata.status = status;
        } else if ('search_term' === selectVal) {
            var $blocks = $form.find('.momo-wsw-by-search-name.momo-be-option-block');
            var searchName = $blocks.find('input[name="shopify_search_term"]').val();
            if ('' === searchName || 'unidefined' === searchName) {
                $form.find('input[name="shopify_search_term"]').focus();
                var name_ = 'empty_' + caller + '_term';
                $msg.html(momowsw_admin_ebay[name_]);
                $msg.show();
                return;
            }
            ajaxdata.action = 'momowsw_fetch_all_items';
            ajaxdata.search_term = searchName;
            ajaxdata.search = true;
        }
        /* console.log(ajaxdata); */
        $.ajax({
            beforeSend: function () {
                $working.addClass('show');
                $main_block.slideUp();
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                if ('bad' === data.status) {
                    $main_block.slideDown();
                    $msg.html(data.msg);
                    $msg.show();
                } else if ('good' === data.status) {
                    var $container = $form.find('.momo-be-imports-table');
                    var $pagination = $container.find('.momowsw-pagination');
                    var $tableBody = $container.find('table tbody');
                    var $msgBody = $report_block.find('.momo-be-msg-block').show();
                    $msgBody.html(data.info);
                    var old = $tableBody.html();
                    $tableBody.html(old + data.html);
                    $pagination.html(data.pagination);
                    $report_block.find('input[name="momowsw_generated_items"]').val(data.plist);
                    $container.show();
                    $container.attr('data-product_id', data.product_id);
                    $container.show();
                    $report_block.slideDown();
                }
            },
            complete: function () {
                $working.removeClass('show');
            }
        });
    });
});