<?php

/**
 * Plugin Name: Shopify WooCommerce / WordPress Integration and Migration
 * Description: Import Shopify Products to WooCommerce.
 * Text Domain: momowsw
 * Domain Path: /languages
 * Author: MoMo Themes
 * Version: 2.2.1
 * Requires PHP: 7.0
 * Author URI: http://www.momothemes.com/
 * Requires at least: 5.7
 * Tested up to: 6.6.1
 */
add_action( 'plugins_loaded', 'momowsw_check_woocommerce', 50 );
/**
 * Check if WooCommerce is installed.
 */
function momowsw_check_woocommerce() {
    if ( !class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'momowsw_woocommerce_not_installed_notice' );
        return;
    } else {
        if ( !function_exists( 'momowsw_fs' ) ) {
            /**
             * Create a helper function for easy SDK access.
             */
            function momowsw_fs() {
                global $momowsw_fs;
                if ( !isset( $momowsw_fs ) ) {
                    // Include Freemius SDK.
                    require_once __DIR__ . '/freemius/start.php';
                    $momowsw_fs = fs_dynamic_init( array(
                        'id'             => '15012',
                        'slug'           => 'momowsw',
                        'type'           => 'plugin',
                        'public_key'     => 'pk_3298b61801e8cbbe5dc0b646b98a8',
                        'is_premium'     => false,
                        'premium_suffix' => 'Premium',
                        'has_addons'     => false,
                        'has_paid_plans' => true,
                        'menu'           => array(
                            'slug'       => 'momowsw',
                            'first-path' => 'admin.php?page=momowsw-getting-started',
                            'account'    => true,
                        ),
                        'is_live'        => true,
                    ) );
                }
                return $momowsw_fs;
            }

            // Init Freemius.
            momowsw_fs();
            // Signal that SDK was initiated.
            do_action( 'momowsw_fs_loaded' );
        }
    }
}

/**
 * Display admin notice if WooCommerce is not installed.
 */
function momowsw_woocommerce_not_installed_notice() {
    ?>
	<div class="notice notice-error">
		<p><?php 
    esc_html_e( 'Plugin - Shopify WooCommerce / WordPress Integration and Migration needs WooCommerce plugin to store imported product(s). Please install woocommerce first', 'momowsw' );
    ?></p>
	</div>
	<?php 
}

/**
 * Main Plugin Class
 */
class MoMo_WSW_Import_Export {
    /**
     * Plugin Version
     *
     * @var string
     */
    public $version = '2.2.1';

    /**
     * Plugin URL
     *
     * @var string
     */
    public $plugin_url;

    /**
     * Plugin Path
     *
     * @var string
     */
    public $plugin_path;

    /**
     * Plugin Function
     *
     * @var MoMo_WSW_Functions
     */
    public $fn;

    /**
     * Plugin Export Functions
     *
     * @var MoMo_WSW_Export_Functions
     */
    public $efn;

    /**
     * Plugin Export Functions
     *
     * @var MoMo_WSW_Premium_Init
     */
    public $premium;

    /**
     * MoMo_WSW_Logs instance
     *
     * @var MoMo_WSW_Logs
     */
    public $logs;

    /**
     * Export Others Functions
     *
     * @var MoMo_WSW_Export_Others_Functions
     */
    public $eofn;

    /**
     * Plugin URL
     *
     * @var string
     */
    public $momowsw_url;

    /**
     * Plugin Assets
     *
     * @var string
     */
    public $momowsw_assets;

    /**
     * Sharing functions
     *
     * @var MoMo_WSW_Sharing_Init
     */
    public $sharing;

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'plugins_loaded', array($this, 'momowsw_plugin_loaded'), 200 );
    }

    /**
     * Plugin Loaded
     */
    public function momowsw_plugin_loaded() {
        $this->plugin_url = plugin_dir_url( __FILE__ );
        $this->plugin_path = __DIR__ . '/';
        $this->momowsw_url = path_join( plugins_url(), basename( __DIR__ ) );
        $this->momowsw_assets = str_replace( array('http:', 'https:'), '', $this->momowsw_url ) . '/assets/';
        add_action( 'init', array($this, 'momowsw_plugin_init'), 0 );
    }

    /**
     * Plugin Init
     *
     * @return void
     */
    public function momowsw_plugin_init() {
        if ( !class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', array($this, 'momo_wsw_no_wc_notice') );
        } else {
            include_once 'includes/class-momo-wsw-functions.php';
            include_once 'includes/class-momo-wsw-logs.php';
            include_once 'includes/class-momo-wsw-export-functions.php';
            include_once 'includes/class-momo-wsw-export-shopify.php';
            include_once 'includes/class-momo-wsw-background-process.php';
            include_once 'includes/admin/class-momo-wsw-orders.php';
            $this->fn = new MoMo_WSW_Functions();
            $this->efn = new MoMo_WSW_Export_Functions();
            $this->logs = new MoMo_WSW_Logs();
            include_once 'includes/class-momo-wsw-export-others-functions.php';
            $this->eofn = new MoMo_WSW_Export_Others_Functions();
            if ( is_admin() ) {
                include_once 'includes/admin/class-momo-wsw-admin-init.php';
                include_once 'includes/admin/class-momo-wsw-admin-ajax.php';
            }
            // Introducing Freemius with Premium.
            include_once 'premium/class-momo-wsw-premium-init.php';
            $this->premium = new MoMo_WSW_Premium_Init();
            if ( is_admin() ) {
                add_action( 'admin_menu', array($this, 'momowsw_set_getting_started_menu'), 20 );
            }
            // Sharing since v2.3.0.
            /* include_once 'sharing/class-momo-wsw-sharing-init.php';
            			$this->sharing = new MoMo_WSW_Sharing_Init(); */
            $this->momo_run_analytic_purchase();
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function momo_run_analytic_purchase() {
        include_once 'analytics/class-mmt-wsw-analytics.php';
    }

    /**
     * Get getting started menu
     *
     * @return void
     */
    public function momowsw_set_getting_started_menu() {
        add_submenu_page(
            'momowsw',
            esc_html__( 'Getting Started', 'momowsw' ),
            esc_html__( 'Getting Started', 'momowsw' ),
            'manage_options',
            'momowsw-getting-started',
            array($this, 'momowsw_render_getting_started_page'),
            11
        );
    }

    /**
     * Render getting started page
     */
    public function momowsw_render_getting_started_page() {
        global $momowsw;
        require_once $momowsw->plugin_path . 'includes/admin/pages/page-momo-wsw-getting-started.php';
    }

    /**
     * Notify if WooCommerce is not there
     */
    public function momo_wsw_no_wc_notice() {
        ?>
		<div class="message error">
			<p>
				<?php 
        esc_html_e( 'This plugin needs WooCommerce plugin to store imported product(s). Please install woocommerce first', 'momowsw' );
        ?>
			</p>
		</div>
		<?php 
    }

}

$GLOBALS['momowsw'] = new MoMo_WSW_Import_Export();