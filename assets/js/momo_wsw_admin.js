/*global jQuery*/
/*global define */
/*global window */
/*global this*/
/*global location*/
/*global document*/
/*global momowsw_admin*/
/*jslint this*/
/*global MomoFormValidator*/
/**
 * momowsw Admin Script
 */
jQuery(document).ready(function ($) {
    "use strict";
    MomoFormValidator.init('#momo-momowsw-admin-settings-form', {validateOnBlur: false, validateOnSubmit: true});
    function changeAdminTab(hash) {
        var mmtmsTable = $('.momo-be-tab-table');
        mmtmsTable.attr('data-tab', hash);
        mmtmsTable.find('.momo-be-admin-content.active').removeClass('active');
        var ul = mmtmsTable.find('ul.momo-be-main-tab');
        ul.find('li a').removeClass('active');
        $(ul).find('a[href=\\' + hash + ']').addClass('active');
        mmtmsTable.find(hash).addClass('active');
        $("html, body").animate({
            scrollTop: 0
        }, 1000);
    }
    function doNothing() {
        var mmtmsTable = $('.momo-be-tab-table');
        mmtmsTable.attr('data-tab', '#momo-eo-ei-event_card');
        return;
    }
    function init() {
        var hash = window.location.hash;
        if (hash === '' || hash === 'undefined' || '#momo-be-wsw-go-pro' === hash) {
            doNothing();
        } else {
            changeAdminTab(hash);
        }
        $('#momo-be-form .switch-input').each(function () {
            var toggleContainer = $(this).parents('.momo-be-toggle-container');
            var afteryes = toggleContainer.attr('momo-be-tc-yes-container');
            if ($(this).is(":checked")) {
                $('#' + afteryes).addClass('active');
            } else {
                $('#' + afteryes).removeClass('active');
            }
        });
    }
    init();
    $('body').on('change', '#momo-be-form  .switch-input', function () {
        var toggleContainer = $(this).parents('.momo-be-toggle-container');
        var afteryes = toggleContainer.attr('momo-be-tc-yes-container');
        if ($(this).is(":checked")) {
            $('#' + afteryes).addClass('active');
        } else {
            $('#' + afteryes).removeClass('active');
            $(this).val('off');
        }
    });
    $('.momo-be-tab-table').on('click', 'ul.momo-be-main-tab li a', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        if ('#momo-be-wsw-go-pro' === href) {
            window.open('https://codecanyon.net/item/wsw-shopify-woocommerce-syncing/38074621', '_blank');
        } else {
            changeAdminTab(href);
            window.location.hash = href;
        }
    });
    $('.momo-be-tab-table').on('click', 'ul.momo-be-main-tab li a[href="momo-be-wsw-go-pro"]', function (e) {
        e.preventDefault();
    });
    /**
     * On Select Change
     */
    $('body').on('change', 'select[name="momowsw_import_by"]', function (e) {
        var val = $(this).val();
        var $form = $(this).closest('#momo-be-import-momo-wsw');
        //var $form = $(this).closest('.momo-be-admin-content.active');
        var $by_pid = $form.find('#momo-by-product-id');
        var $p_all = $form.find('#momo-by-all-product');
        var $fmb = $form.find('.momo-be-fetch-more-box');

        if ('product_id' === val ) {
            $p_all.removeClass('show');
            $by_pid.addClass('show');

            $fmb.show();
        } else if ('all_products' === val) {
            $by_pid.removeClass('show');
            $p_all.addClass('show');

            $fmb.hide();
        }
    });
    /**
     * On Select Change
     */
    $('body').on('change', 'select[name="momowsw_import_by"]', function () {
        var val = $(this).val();
        var $form = $(this).closest('.momo-be-admin-content');
        var $by_pid = $form.find('#momo-by-item-id');
        var $p_all = $form.find('#momo-by-all-item');
        var $s_name = $form.find('#momo-by-search-term');
        var $fmb = $form.find('.momo-be-fetch-more-box');
        if ('item_id' === val) {
            $p_all.removeClass('show');
            $by_pid.addClass('show');
            $s_name.removeClass('show');
            $fmb.show();
        } else if ('all_items' === val) {
            $by_pid.removeClass('show');
            $p_all.addClass('show');
            $s_name.removeClass('show');
            $fmb.hide();
        } else if ('search_term' === val) {
            $by_pid.removeClass('show');
            $p_all.removeClass('show');
            $s_name.addClass('show');
        }
    });
    /**
     * Fetch Product(s)
     */
    $('body').on('click', '.shopify_fetch_by', function (e) {
        e.preventDefault();
        var $form = $(this).closest('#momo-be-import-momo-wsw');
        var $working = $form.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var $main_block = $form.find('.momowsw-import-main');
        var $report_block = $form.find('.momo-be-result-block');
        var current_list = $report_block.find('input[name="momowsw_generated_products"]').val();
        var $dnfip = $form.find('input[name="momowsw_imported_donot_fetch"]');
        var $msg = $main_block.find('.momo-be-msg-block');
        $msg.hide();
        var selectVal = $form.find('select[name="momowsw_import_by"]').val();
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.current_list = current_list;
        if ($dnfip.is(":checked")) {
            ajaxdata.dnfip = 'on';
        } else {
            ajaxdata.dnfip = 'off';
        }
        if ('product_id' === selectVal) {
            var $block = $form.find('#momo-by-product-id.momo-be-option-block');
            var productID = $block.find('input[name="item_id"]').val();
            if ('' === productID) {
                $form.find('input[name="shopify_product_id"]').focus();
                $msg.html(momowsw_admin.empty_product_id);
                $msg.show();
                return;
            }
            ajaxdata.action = 'momowsw_fetch_by_product_id';
            ajaxdata.product_id = productID;
        } else if ('all_products' === selectVal) {
            var $blockp = $form.find('#momo-by-all-product.momo-be-option-block');
            var limit = $blockp.find('select[name="momowsw_product_limit"]').val();
            var plimit = $form.find('select[name="momowsw_page_limit"]').val();
            ajaxdata.action = 'momowsw_fetch_all_products';
            ajaxdata.limit = limit;
            ajaxdata.plimit = plimit;
            ajaxdata.current_page = 1;
        }
        console.log(ajaxdata);
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
                    $report_block.find('input[name="momowsw_generated_products"]').val(data.plist);
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
     * Import Products
     */
    $('body').on('click', '.momowsw-admin-import-products', function (e) {
        e.preventDefault();
        var $tableContainer = $(this).closest('.momo-be-imports-table');
        var $pagination = $tableContainer.find('.momowsw-pagination');
        var $table = $tableContainer.find('table');
        var $form = $(this).closest('#momo-be-import-momo-wsw');
        var $import = $form.find('.momo-be-buttons-block.import-section');
        var $background = $import.find('input[name="momowsw_import_at_background"]');
        var $working = $form.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var $msg = $form.find('.momo-be-msg-block');
        var $catandtags = $import.find('input[name="momowsw_import_categories"]');
        var $variations = $import.find('input[name="momowsw_import_variations"]');
        var $pstatus = $import.find('select[name="product_status"]');
        var ajaxdata = {};
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
            $table.find('tbody > tr').each(function () {
                var $tr = $(this);
                var pid = $tr.data('product_id');
                var status = $tr.data('status');
                var item = {};
                item.item_id = pid;
                item.status = status;
                if ('imported' !== status) {
                    item_data[i++] = item;
                }
                $tr.remove();
            });
            ajaxdata.items = item_data;
            ajaxdata.caller = 'product';
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
                var pid = $(this).data('product_id');
                var status = $(this).data('status');
                var $tr = $(this);
                if ("imported" !== status) {
                    ajaxdata.product_id = pid;
                    ajaxdata.action = 'momowsw_import_single_product';
                    ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
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
    /**
     * Fetch More Product
     */
    $('body').on('click', '.momowsw-admin-fetch-more', function (e) {
        e.preventDefault();
        var $form = $(this).closest('#momo-be-import-momo-wsw');
        var $main_block = $form.find('.momowsw-import-main');
        var $report_block = $form.find('.momo-be-result-block');
        var $back_btn = $main_block.find('.momo-be-back-to-list-block');
        $report_block.slideUp("slow", function () {
            $back_btn.css('display', 'block');
            $main_block.slideDown().delay('300');
        });
    });
    /**
     * Back to Fetched List
     */
    $('body').on('click', '.momowsw-back-to-fetch-list', function (e) {
        e.preventDefault();
        var $form = $(this).closest('#momo-be-import-momo-wsw');
        var $main_block = $form.find('.momowsw-import-main');
        var $report_block = $form.find('.momo-be-result-block');
        var $back_btn = $main_block.find('.momo-be-back-to-list-block');
        $main_block.slideUp("slow", function () {
            $back_btn.css('display', 'none');
            $report_block.slideDown('slow');
        });
    });

    /**
     * Spinner function
     */
    $.fn.momowswsspinnner = function () {
        var $spinner = $(this).find('.momo-be-spinner');
        var $text = $(this).find('.momo-be-spinner-text');
        $spinner.toggleClass('spin');
        $text.toggle();
    };
    /**
     * Spinner function
     */
    $.fn.momowswNormalize = function () {
        $(this).html('');
        $(this).attr('class', 'momo-be-post-sb-message');
    };
    /**
     * Export Product
     */
    $('body').on('click', '#momo-wsw-export-to-shopify', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $box = $btn.closest('.momo-be-post-submitbox');
        var type = $btn.data('type');
        var $msgBox = $box.find('.momo-be-post-sb-message');
        $msgBox.momowswNormalize();
        var product_id = $btn.data('product_id');
        var shopify_id = $btn.data('shopify_id');
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.product_id = product_id;
        ajaxdata.shopify_id = shopify_id;
        ajaxdata.type = type;
        ajaxdata.action = 'momowsw_sync_single_product_to_shopify';
        $.ajax({
            beforeSend: function () {
                $btn.momowswsspinnner();
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
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
    $('body').on('click', '.momo-post-btn-clear-shopify', function (e) {
        e.preventDefault();
        var $me = $(this);
        var $box = $me.closest('.momo-be-post-submitbox');
        var $msgBox = $box.find('.momo-be-post-sb-message');
        var $btn = $box.find('#momo-wsw-export-to-shopify');
        var product_id = $me.data('product_id');
        var shopify_id = $me.data('shopify_id');
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.product_id = product_id;
        ajaxdata.shopify_id = shopify_id;
        ajaxdata.action = 'momowsw_unlink_shopify_id_from_product';
        $.ajax({
            beforeSend: function () {
                $btn.momowswsspinnner();
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                $msgBox.addClass(data.status);
                $msgBox.html(data.msg);
                $msgBox.addClass('show');
                if ('success' === data.status) {
                    $btn.html(momowsw_admin.postexport_btn_text);
                    $btn.attr('data-type', 'insert');
                    $btn.attr('data-shopify_id', '');
                }
            },
            complete: function () {
                $btn.momowswsspinnner();
            }
        });
    });
    /**
     * On pagination click
     */
    $('body').on('click', '.momowsw-pagination-link-product', function (e) {
        var caller = $(this).data('caller');
        var $form = $(this).closest('#momo-be-import-momo-wsw');
        var $working = $form.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var $main_block = $form.find('.momowsw-import-main');
        var $report_block = $form.find('.momo-be-result-block');
        var current_list = '';
        var page_info = $(this).data('page_info');
        var rel = $(this).data('rel');
        var $dnfip = $form.find('input[name="momowsw_imported_donot_fetch"]');
        var $msg = $main_block.find('.momo-be-msg-block');
        $msg.hide();
        var selectVal = $form.find('select[name="momowsw_import_by"]').val();
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.current_list = current_list;
        ajaxdata.caller = caller;
        ajaxdata.pagination = 'yes';
        ajaxdata.rel = rel;
        ajaxdata.pageinfo = page_info;
        if ($dnfip.is(":checked")) {
            ajaxdata.dnfip = 'on';
        } else {
            ajaxdata.dnfip = 'off';
        }
        var $block = $form.find('#momo-by-product-id.momo-be-option-block');
        if ('product_id' === selectVal) {
            var productID = $block.find('input[name="shopify_product_id"]').val();
            if ('' === productID) {
                $form.find('input[name="shopify_product_id"]').focus();
                $msg.html(momowsw_admin.empty_product_id);
                $msg.show();
                return;
            }
            ajaxdata.action = 'momowsw_fetch_by_product_id';
            ajaxdata.product_id = productID;
        } else if ('all_products' === selectVal) {
            var $blockp = $form.find('#momo-by-all-product.momo-be-option-block');
            var limit = $blockp.find('select[name="momowsw_product_limit"]').val();
            var plimit = $form.find('select[name="momowsw_page_limit"]').val();
            ajaxdata.action = 'momowsw_fetch_all_products';
            ajaxdata.limit = limit;
            ajaxdata.plimit = plimit;
            ajaxdata.current_page = 1;
        }
        $.ajax({
            beforeSend: function () {
                $working.addClass('show');
                $report_block.slideUp();
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
                    $tableBody.html(data.html);
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
     * On pagination click
     */
    $('body').on('click', '.momowsw-pagination-link', function (e) {
        var caller = $(this).data('caller');
        var $form = $(this).closest('.momo-be-admin-content');
        var $working = $form.closest('.momo-be-main-tabcontent').find('.momo-be-working');
        var $main_block = $form.find('.momowsw-import-main');
        var $report_block = $form.find('.momo-be-result-block');
        var current_list = '';
        var page_info = $(this).data('page_info');
        var rel = $(this).data('rel');
        var $dnfip = $form.find('input[name="momowsw_imported_donot_fetch"]');
        var $msg = $main_block.find('.momo-be-msg-block');
        $msg.hide();
        var selectVal = $form.find('select[name="momowsw_import_by"]').val();
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.current_list = current_list;
        ajaxdata.caller = caller;
        ajaxdata.pagination = 'yes';
        selectVal = 'all_items';
        ajaxdata.rel = rel;
        ajaxdata.pageinfo = page_info;
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
        $.ajax({
            beforeSend: function () {
                $working.addClass('show');
                $report_block.slideUp();
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
                    $tableBody.html(data.html);
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

    $('body').on('click', '.momo_wsw_clear_transient', function (e) {
        var $box = $(this).closest('.momo-be-note');
        var $btn = $(this);
        var ajaxdata = {};
        ajaxdata.security = momowsw_admin.momowsw_ajax_nonce;
        ajaxdata.action = 'momowsw_clear_transient';
        var $msgBox = $box.find('.momo-be-post-sb-message');
        $msgBox.momowswNormalize();
        $.ajax({
            beforeSend: function () {
                $btn.momowswsspinnner();
            },
            type: 'POST',
            dataType: 'json',
            url: momowsw_admin.ajaxurl,
            data: ajaxdata,
            success: function (data) {
                $msgBox.addClass(data.status);
                $msgBox.html(data.msg);
                $msgBox.addClass('show');
                if ('success' === data.status) {
                    
                }
            },
            complete: function () {
                $btn.momowswsspinnner();
            }
        });
    });
});