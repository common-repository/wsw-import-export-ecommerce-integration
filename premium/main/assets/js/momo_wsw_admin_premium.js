/*global jQuery*/
/*global define */
/*global window */
/*global this*/
/*global location*/
/*global document*/
/*global momowsw_admin*/
/*jslint this*/
/**
 * momowsw Admin Script
 */
jQuery(document).ready(function ($) {
    "use strict";
    $('body').on('click', '.shopify_fetch_by_premium', function (e) {
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

        var selectServer = $form.find('select[name="momowsw_shop_url"]').val();
 
        var ajaxdata = {};
        ajaxdata.selected_server = selectServer;
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.current_list = current_list;
        ajaxdata.caller = caller;
        if ($dnfip.is(":checked")) {
            ajaxdata.dnfip = 'on';
        } else {
            ajaxdata.dnfip = 'off';
        }
        var $block = $form.find('.momo-wsw-by-item-id.momo-be-option-block');
        if ('item_id' === selectVal) {
            var itemID = $block.find('input[name="shopify_item_id"]').val();
            if ('' === itemID || 'unidefined' === itemID) {
                $form.find('input[name="shopify_item_id"]').focus();
                var name = 'empty_' + caller + '_id';
                $msg.html(momowsw_admin[name]);
                $msg.show();
                return;
            }
            ajaxdata.action = 'momowsw_fetch_by_item_id';
            ajaxdata.item_id = itemID;
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
                $msg.html(momowsw_admin[name_]);
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
    /**
     * Import Items
     */
    $('body').on('click', '.momowsw-admin-import-items', function (e) {
        e.preventDefault();
        var $tableContainer = $(this).closest('.momo-be-imports-table');
        var $pagination = $tableContainer.find('.momowsw-pagination');
        var $isection = $(this).closest('.import-section');
        var $background = $isection.find('input[name="momowsw_import_at_background"]');
        var caller = $(this).data('caller');
        var $table = $tableContainer.find('table');
        var $form = $(this).closest('.momo-be-admin-content');
        var $import = $form.find('.momo-be-buttons-block.import-section');
        var $working = $form.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var $msg = $form.find('.momo-be-msg-block');
        var $catandtags = $import.find('input[name="momowsw_import_categories"]');
        var $variations = $import.find('input[name="momowsw_import_variations"]');
        var $pstatus = $import.find('select[name="item_status"]');
        var ajaxdata = {};
        ajaxdata.caller = caller;

        var selectServer = $form.find('select[name="momowsw_shop_url"]').val();
        ajaxdata.selected_server = selectServer;

        if ($background.is(":checked")) {
            ajaxdata.background = 'on';
        } else {
            ajaxdata.background = 'off';
        }
        if ($catandtags.is(":checked")) {
            ajaxdata.catandtags = 'on';
        } else {
            ajaxdata.catandtags = 'off';
        }
        if ($variations.is(":checked")) {
            ajaxdata.variations = 'on';
        } else {
            ajaxdata.variations = 'off';
        }
        ajaxdata.pstatus = $pstatus.val();
        $msg.html('').hide();
        var xhrs = [];
        var count = 0;
        if ('on' === ajaxdata.background) { // Runs in background
            var item_data = {};
            var i = 0;
            $working.addClass('show');
            $pagination.hide();
            $table.find('tbody > tr.data-item').each(function () {
                var $tr = $(this);
                var pid = $tr.data('item_id');
                var status = $tr.data('status');
                var item = {};
                item.item_id = pid;
                item.status = status;
                if ('imported' !== status) {
                    item_data[i++] = item;
                }
                $tr.remove();
            });
            console.log(item_data);
            ajaxdata.items = item_data;
            ajaxdata.caller = caller;
            ajaxdata.action = 'momowsw_schedule_import_data_background';
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
                    if (data.status === 'good') {
                        $msg.html(data.msg).show();
                    }
                },
                complete: function () {
                    $working.removeClass('show');
                }
            });
        } else { // Live importing
            $table.find('tr').each(function () {
                var pid = $(this).data('item_id');
                var status = $(this).data('status');
                var $tr = $(this);
                if ("imported" !== status) {
                    ajaxdata.item_id = pid;
                    ajaxdata.action = 'momowsw_import_single_item';
                    ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
                    console.log(ajaxdata);
                    var xhr = $.ajax({
                        beforeSend: function () {
                            $working.addClass('show');
                        },
                        type: 'POST',
                        dataType: 'json',
                        url: momowsw_admin.ajaxurl,
                        data: ajaxdata,
                        success: function (data) {
                            if (data.status === 'bad') {
                                $tr.addClass('bad');
                                $tr.find('.status').html(data.msg);
                            } else if (data.status === 'good') {
                                count = count + 1;
                                $tr.addClass('good');
                                $tr.attr('data-status', 'imported');
                                $tr.find('.status').html(momowsw_admin.imported_span);
                            }
                        }
                    });
                    xhrs.push(xhr);
                }
            });
            $.when.apply($, xhrs).done(function () {
                $working.removeClass('show');
                $msg.html(count + momowsw_admin.imported_message).show();
            });
        }
    });
});