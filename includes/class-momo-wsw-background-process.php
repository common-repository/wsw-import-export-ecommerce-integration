<?php
/**
 * Shopify Import using Action Schedule
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.2.0
 */
class MoMo_WSW_Background_Process {
	/**
	 * Message string
	 *
	 * @var string
	 */
	private $message = '';
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'momowsw_run_background_import_process', array( $this, 'momowsw_action_run_background_import_process' ), 10, 2 );
	}
	/**
	 * Run background Import process
	 *
	 * @param array $items Items.
	 * @param array $args Arguments.
	 */
	public function momowsw_action_run_background_import_process( $items, $args ) {
		global $momowsw;
		// Process the one-time task.
		$catandtags = isset( $args['catandtags'] ) ? sanitize_text_field( wp_unslash( $args['catandtags'] ) ) : 'off';
		$variations = isset( $args['variations'] ) ? sanitize_text_field( wp_unslash( $args['variations'] ) ) : 'off';
		$pstatus    = isset( $args['pstatus'] ) ? sanitize_text_field( wp_unslash( $args['pstatus'] ) ) : 'publish';
		$caller     = isset( $args['caller'] ) ? sanitize_text_field( wp_unslash( $args['caller'] ) ) : 'product';
		$message    = '';
		foreach ( $items as $item ) {
			$item_id  = isset( $item['item_id'] ) ? sanitize_text_field( wp_unslash( $item['item_id'] ) ) : 0;
			$response = $momowsw->fn->momowsw_import_shopify_product( $item_id, $catandtags, $pstatus, $variations );

			if ( $response ) {
				/* translators: %s: caller (product, page, blog) */
				$message .= sprintf( esc_html__( '%s(s) imported successfully.', 'momowsw' ), ucfirst( $caller ) );
			} else {
				$message .= esc_html__( 'Import Error.', 'momowsw' );
			}
		}
		$this->message = $message;
		add_action( 'admin_notices', array( $this, 'momowsw_render_admin_notice' ) );
	}
	/**
	 * Display Admin message
	 */
	public function momowsw_render_admin_notice() {
		printf( '<div class="updated">%s</div>', esc_html( $this->message ) );
	}
}
new MoMo_WSW_Background_Process();
