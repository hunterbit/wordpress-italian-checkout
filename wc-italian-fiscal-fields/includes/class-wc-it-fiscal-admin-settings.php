<?php
/**
 * Classe per la pagina di configurazione admin
 *
 * Integra il plugin con WooCommerce Settings API aggiungendo
 * un tab "Campi Fiscali" nella sezione impostazioni.
 *
 * @package WC_IT_Fiscal_Fields
 * @author  Rocco Fusella
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe WC_IT_Fiscal_Admin_Settings
 */
class WC_IT_Fiscal_Admin_Settings {

	/**
	 * ID del tab settings
	 *
	 * @var string
	 */
	private $tab_id = 'wc_it_fiscal';

	/**
	 * Costruttore - Registra gli hook
	 */
	public function __construct() {
		// Aggiunge il tab alle impostazioni WooCommerce
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );

		// Renderizza i campi settings
		add_action( 'woocommerce_settings_' . $this->tab_id, array( $this, 'render_settings' ) );

		// Salva le opzioni
		add_action( 'woocommerce_update_options_' . $this->tab_id, array( $this, 'save_settings' ) );

		// Sanitizzazione custom (opzionale)
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_options' ), 10, 3 );
	}

	/**
	 * Aggiunge il tab "Campi Fiscali" al menu impostazioni WooCommerce
	 *
	 * @param array $tabs Tabs esistenti.
	 * @return array
	 */
	public function add_settings_tab( $tabs ) {
		$tabs[ $this->tab_id ] = __( 'Campi Fiscali', 'wc-it-fiscal-fields' );
		return $tabs;
	}

	/**
	 * Renderizza i campi della pagina settings
	 */
	public function render_settings() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Salva le opzioni della pagina settings
	 */
	public function save_settings() {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Sanitizzazione custom per le opzioni (se necessario)
	 *
	 * @param mixed  $value  Valore da sanitizzare.
	 * @param array  $option Configurazione opzione.
	 * @param mixed  $raw_value Valore raw prima della sanitizzazione.
	 * @return mixed
	 */
	public function sanitize_options( $value, $option, $raw_value ) {
		// Priority: assicura che sia un numero positivo
		if ( isset( $option['id'] ) && strpos( $option['id'], '_priority' ) !== false ) {
			$value = absint( $value );
			if ( $value < 1 ) {
				$value = 1;
			}
		}

		return $value;
	}

	/**
	 * Ottiene la configurazione dei campi settings
	 *
	 * @return array
	 */
	private function get_settings() {
		// Carica la definizione campi dal file config
		$settings_file = WC_IT_FISCAL_PLUGIN_DIR . 'config/settings-fields.php';

		if ( file_exists( $settings_file ) ) {
			return include $settings_file;
		}

		// Fallback se il file non esiste (non dovrebbe succedere)
		return array();
	}
}
