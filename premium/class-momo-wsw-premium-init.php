<?php

/**
 * WSW Premium Init
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v2.0.0
 */
class MoMo_WSW_Premium_Init {
    /**
     * MoMo_WSW_Multistore_Functions instance
     *
     * @var MoMo_WSW_Multistore_Functions
     */
    public $fpfn;

    /**
     * MoMo_WSW_Multistore_Cron instance
     *
     * @var MoMo_WSW_Multistore_Cron
     */
    public $fpcron;

    /**
     * MoMo_WSW_Multi_Store_Order_Auto_Sync instance
     *
     * @var MoMo_WSW_Multi_Store_Order_Auto_Sync
     */
    public $osync;

    /**
     * MoMo_WSW_Multistore_Multi_Stores instance
     *
     * @var MoMo_WSW_Multistore_Multi_Stores
     */
    public $multi;

    /**
     * Webhooks
     *
     * @var MoMo_WSW_Webhooks
     */
    public $webhooks;

    /**
     * CWebhooks
     *
     * @var MoMo_WSW_Custom_Webhooks
     */
    public $cwebhooks;

    /**
     * Webhooks Functions
     *
     * @var MoMo_WSW_Webhooks_Functions
     */
    public $whfn;

    /**
     * Premium Functions
     *
     * @var MoMo_WSW_Functions_Premium
     */
    public $fn;

    /**
     * Feed Generation
     *
     * @var MoMo_WSW_Feed_Generation
     */
    public $feeds;

    /**
     * Product Feed Cron
     *
     * @var MoMo_WSW_PF_Cron
     */
    public $pfcron;

    /**
     * Ebay API
     *
     * @var MoMo_WSW_Ebay_API
     */
    public $eapi;

    /**
     * Ebay Functions
     *
     * @var MoMo_WSW_Ebay_Functions
     */
    public $ebayfn;

    /**
     * Order Auto Sync
     *
     * @var MoMo_WSW_Order_Auto_Sync
     */
    public $synco;

    /**
     * Order Cron
     *
     * @var MoMo_WSW_Order_Cron
     */
    public $ocron;

    /**
     * Other Cron
     *
     * @var MoMo_WSW_Other_Cron
     */
    public $othercron;

    /**
     * Other Auto Sync
     *
     * @var MoMo_WSW_Other_Auto_Sync
     */
    public $syncother;

    /**
     * Cron
     *
     * @var MoMo_WSW_Cron
     */
    public $cron;

    /**
     * AutoSync
     *
     * @var MoMo_WSW_Auto_Sync
     */
    public $sync;

    /**
     * Constructor
     */
    public function __construct() {
        global $momowsw;
        if ( is_admin() ) {
            include_once 'main/admin/class-momo-wsw-export-init.php';
            include_once 'main/admin/pages/class-momo-wsw-settings-premium.php';
        }
        if ( is_admin() ) {
            include_once 'orders/admin/class-momo-wsw-admin-orders-init.php';
        }
        if ( is_admin() ) {
            include_once 'webhooks/admin/class-momo-wsw-admin-webhooks.php';
            include_once 'webhooks/admin/class-momo-wsw-webhooks-admin-ajax.php';
        }
        if ( is_admin() ) {
            include_once 'multistore/admin/class-momo-wsw-admin-multistore.php';
            include_once 'multistore/admin/class-momo-wsw-multistore-admin-ajax.php';
        }
        include_once 'othersync/admin/class-momo-wsw-auto-sync.php';
        $this->sync = new MoMo_WSW_Auto_Sync();
        if ( is_admin() ) {
            include_once 'othersync/admin/class-momo-wsw-admin-osync-init.php';
        }
        // Product Feed (Google Shopping Feed).
        include_once 'product-feed/class-momo-wsw-country-list.php';
        if ( is_admin() ) {
            include_once 'product-feed/admin/class-momo-wsw-admin-product-feed.php';
        }
        if ( is_admin() ) {
            include_once 'ebay/admin/class-momo-wsw-admin-ebay-sync.php';
            include_once 'ebay/class-momo-wsw-export-ebay.php';
            include_once 'ebay/admin/class-momo-wsw-ebay-admin-ajax.php';
        }
    }

}
