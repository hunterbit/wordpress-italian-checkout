<?php
/**
 * Plugin Name: WooCommerce Italian Fiscal Fields
 * Plugin URI: https://github.com/roccofusella/wc-italian-fiscal-fields
 * Description: Aggiunge campi fiscali italiani (Tipologia Utente, Ragione Sociale, Codice Fiscale, Partita IVA) al checkout WooCommerce con logica condizionale e pagina di configurazione admin.
 * Version: 2.0.1
 * Author: Rocco Fusella
 * Author URI: https://roccofusella.it
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-it-fiscal-fields
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 6.0
 * WC tested up to: 8.5
 */

// Impedisce accesso diretto al file
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Definizioni costanti del plugin
define( 'WC_IT_FISCAL_VERSION', '2.0.1' );
define( 'WC_IT_FISCAL_PLUGIN_FILE', __FILE__ );
define( 'WC_IT_FISCAL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WC_IT_FISCAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Verifica che WooCommerce sia attivo prima di inizializzare il plugin
 */
function wc_it_fiscal_check_woocommerce() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_it_fiscal_woocommerce_missing_notice' );
		return false;
	}
	return true;
}

/**
 * Notice se WooCommerce non è attivo
 */
function wc_it_fiscal_woocommerce_missing_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'WooCommerce Italian Fiscal Fields richiede WooCommerce per funzionare. Per favore installa e attiva WooCommerce.', 'wc-it-fiscal-fields' ); ?></p>
	</div>
	<?php
}

/**
 * Inizializza il plugin
 */
function wc_it_fiscal_init() {
	if ( ! wc_it_fiscal_check_woocommerce() ) {
		return;
	}

	// Carica le classi del plugin
	require_once WC_IT_FISCAL_PLUGIN_DIR . 'includes/class-wc-it-fiscal-options.php';
	require_once WC_IT_FISCAL_PLUGIN_DIR . 'includes/class-wc-it-fiscal-validator.php';
	require_once WC_IT_FISCAL_PLUGIN_DIR . 'includes/class-wc-it-fiscal-admin-settings.php';
	require_once WC_IT_FISCAL_PLUGIN_DIR . 'includes/class-wc-it-fiscal-fields.php';

	// Inizializza il plugin
	WC_IT_Fiscal_Fields::get_instance();
}
add_action( 'plugins_loaded', 'wc_it_fiscal_init' );

/**
 * Carica text domain per le traduzioni
 */
function wc_it_fiscal_load_textdomain() {
	load_plugin_textdomain( 'wc-it-fiscal-fields', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'wc_it_fiscal_load_textdomain' );

/**
 * Dichiara la compatibilità con WooCommerce HPOS (High-Performance Order Storage)
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
