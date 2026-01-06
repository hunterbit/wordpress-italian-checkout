<?php
/**
 * Classe per la gestione delle opzioni del plugin
 *
 * Gestisce tutte le opzioni salvate in wp_options e fornisce metodi getter
 * per accedere ai valori di configurazione.
 *
 * @package WC_IT_Fiscal_Fields
 * @author  Rocco Fusella
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe WC_IT_Fiscal_Options
 */
class WC_IT_Fiscal_Options {

	/**
	 * Prefisso per tutte le opzioni del plugin
	 */
	const OPTION_PREFIX = 'wc_it_fiscal_';

	/**
	 * Cache opzioni in memoria per performance
	 *
	 * @var array
	 */
	private $cache = array();

	/**
	 * Verifica se un campo è abilitato
	 *
	 * @param string $field Nome campo: 'user_type', 'ragione_sociale', 'cf', 'piva'.
	 * @return bool
	 */
	public function is_field_enabled( $field ) {
		$key = self::OPTION_PREFIX . 'enable_' . $field;
		return 'yes' === $this->get_option( $key, 'yes' );
	}

	/**
	 * Verifica se un campo è obbligatorio per una specifica tipologia utente
	 *
	 * @param string $field     Nome campo: 'ragione_sociale', 'cf', 'piva'.
	 * @param string $user_type Tipologia: 'persona_fisica', 'azienda', 'associazione_ente'.
	 * @return bool
	 */
	public function is_field_required_for_user_type( $field, $user_type ) {
		// Mappa user_type al suffisso corretto
		$suffix_map = array(
			'persona_fisica'    => 'persona_fisica',
			'azienda'           => 'azienda',
			'associazione_ente' => 'associazione',
		);

		$suffix = isset( $suffix_map[ $user_type ] ) ? $suffix_map[ $user_type ] : $user_type;
		$key    = self::OPTION_PREFIX . $field . '_required_' . $suffix;

		// Ottieni valore con default
		$default = $this->get_default_requirement( $field, $user_type );
		return 'yes' === $this->get_option( $key, $default );
	}

	/**
	 * Ottiene l'etichetta personalizzata di un campo
	 *
	 * @param string $field Nome campo.
	 * @return string
	 */
	public function get_field_label( $field ) {
		$key     = self::OPTION_PREFIX . $field . '_label';
		$default = $this->get_default_label( $field );
		return $this->get_option( $key, $default );
	}

	/**
	 * Ottiene la priority di un campo
	 *
	 * @param string $field Nome campo.
	 * @return int
	 */
	public function get_field_priority( $field ) {
		$key     = self::OPTION_PREFIX . $field . '_priority';
		$default = $this->get_default_priority( $field );
		return intval( $this->get_option( $key, $default ) );
	}

	/**
	 * Ottiene il placeholder di un campo
	 *
	 * @param string $field Nome campo.
	 * @return string
	 */
	public function get_field_placeholder( $field ) {
		$key     = self::OPTION_PREFIX . $field . '_placeholder';
		$default = $this->get_default_placeholder( $field );
		return $this->get_option( $key, $default );
	}

	/**
	 * Verifica se la validazione algoritmica avanzata è abilitata
	 *
	 * @return bool
	 */
	public function is_advanced_validation_enabled() {
		$key = self::OPTION_PREFIX . 'advanced_validation';
		return 'yes' === $this->get_option( $key, 'no' );
	}

	/**
	 * Ottiene il tipo di campo per Tipologia Utente
	 *
	 * @return string 'select' o 'radio'
	 */
	public function get_user_type_field_type() {
		// Sempre 'select' nella v2.0.0
		return 'select';
	}

	/**
	 * Metodo helper per ottenere un'opzione con cache
	 *
	 * @param string $key     Chiave opzione.
	 * @param mixed  $default Valore di default.
	 * @return mixed
	 */
	private function get_option( $key, $default = '' ) {
		if ( isset( $this->cache[ $key ] ) ) {
			return $this->cache[ $key ];
		}

		$value                = get_option( $key, $default );
		$this->cache[ $key ] = $value;

		return $value;
	}

	/**
	 * Ottiene il valore di default per l'obbligatorietà di un campo
	 *
	 * IMPORTANTE: Questi default replicano il comportamento della v1.0.0
	 * per garantire backward compatibility.
	 *
	 * @param string $field     Nome campo.
	 * @param string $user_type Tipologia utente.
	 * @return string 'yes' o 'no'
	 */
	private function get_default_requirement( $field, $user_type ) {
		$defaults = array(
			'ragione_sociale' => array(
				'persona_fisica'    => 'no',
				'azienda'           => 'yes',
				'associazione_ente' => 'yes',
			),
			'cf'              => array(
				'persona_fisica'    => 'yes',
				'azienda'           => 'no',
				'associazione_ente' => 'no',
			),
			'piva'            => array(
				'persona_fisica'    => 'no',
				'azienda'           => 'yes',
				'associazione_ente' => 'no',
			),
		);

		if ( isset( $defaults[ $field ][ $user_type ] ) ) {
			return $defaults[ $field ][ $user_type ];
		}

		return 'no';
	}

	/**
	 * Ottiene l'etichetta di default per un campo
	 *
	 * @param string $field Nome campo.
	 * @return string
	 */
	private function get_default_label( $field ) {
		$labels = array(
			'user_type'        => __( 'Tipologia utente', 'wc-it-fiscal-fields' ),
			'ragione_sociale'  => __( 'Ragione Sociale', 'wc-it-fiscal-fields' ),
			'cf'               => __( 'Codice Fiscale', 'wc-it-fiscal-fields' ),
			'piva'             => __( 'Partita IVA', 'wc-it-fiscal-fields' ),
		);

		return isset( $labels[ $field ] ) ? $labels[ $field ] : '';
	}

	/**
	 * Ottiene la priority di default per un campo
	 *
	 * @param string $field Nome campo.
	 * @return int
	 */
	private function get_default_priority( $field ) {
		$priorities = array(
			'user_type'       => 45,
			'ragione_sociale' => 46,
			'cf'              => 47,
			'piva'            => 49,
		);

		return isset( $priorities[ $field ] ) ? $priorities[ $field ] : 50;
	}

	/**
	 * Ottiene il placeholder di default per un campo
	 *
	 * @param string $field Nome campo.
	 * @return string
	 */
	private function get_default_placeholder( $field ) {
		$placeholders = array(
			'ragione_sociale' => __( 'Es: Nome Azienda Srl', 'wc-it-fiscal-fields' ),
			'cf'              => __( 'Es: RSSMRA80A01H501U', 'wc-it-fiscal-fields' ),
			'piva'            => __( 'Es: 12345678901', 'wc-it-fiscal-fields' ),
		);

		return isset( $placeholders[ $field ] ) ? $placeholders[ $field ] : '';
	}
}
