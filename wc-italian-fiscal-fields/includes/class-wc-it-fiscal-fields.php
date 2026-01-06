<?php
/**
 * Classe principale del plugin WooCommerce Italian Fiscal Fields
 *
 * @package WC_IT_Fiscal_Fields
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe WC_IT_Fiscal_Fields
 */
class WC_IT_Fiscal_Fields {

	/**
	 * Istanza singleton della classe
	 *
	 * @var WC_IT_Fiscal_Fields
	 */
	private static $instance = null;

	/**
	 * Chiavi dei campi custom
	 *
	 * @var array
	 */
	private $field_keys = array(
		'user_type'        => 'billing_user_type',
		'ragione_sociale'  => 'billing_ragione_sociale',
		'codice_fiscale'   => 'billing_codice_fiscale',
		'partita_iva'      => 'billing_partita_iva',
	);

	/**
	 * Istanza classe Options
	 *
	 * @var WC_IT_Fiscal_Options
	 */
	private $options = null;

	/**
	 * Istanza classe Validator
	 *
	 * @var WC_IT_Fiscal_Validator
	 */
	private $validator = null;

	/**
	 * Istanza classe Admin Settings
	 *
	 * @var WC_IT_Fiscal_Admin_Settings
	 */
	private $admin_settings = null;

	/**
	 * Ottiene l'istanza singleton
	 *
	 * @return WC_IT_Fiscal_Fields
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Costruttore - registra tutti gli hook
	 */
	private function __construct() {
		// Inizializza le dipendenze
		$this->options   = new WC_IT_Fiscal_Options();
		$this->validator = new WC_IT_Fiscal_Validator( $this->options );

		// Inizializza admin settings solo in admin
		if ( is_admin() ) {
			$this->admin_settings = new WC_IT_Fiscal_Admin_Settings();
		}

		// Hook per aggiungere i campi al checkout
		add_filter( 'woocommerce_checkout_fields', array( $this, 'add_checkout_fields' ) );

		// Hook per caricare script e stili
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Hook per validazione campi
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_checkout_fields' ), 10, 2 );

		// Hook per salvare i dati dell'ordine
		add_action( 'woocommerce_checkout_create_order', array( $this, 'save_order_meta' ), 10, 2 );

		// Hook per visualizzare i dati nel frontend
		add_action( 'woocommerce_thankyou', array( $this, 'display_order_data_thankyou' ), 20 );
		add_action( 'woocommerce_view_order', array( $this, 'display_order_data_my_account' ), 20 );

		// Hook per visualizzare i dati nel backend admin
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_admin_order_meta' ) );
	}

	/**
	 * Aggiunge i campi fiscali al checkout
	 *
	 * IMPORTANTE: Le priority determinano l'ordine dei campi.
	 * Modificare questi valori per cambiare la posizione dei campi.
	 *
	 * @param array $fields Array dei campi checkout.
	 * @return array
	 */
	public function add_checkout_fields( $fields ) {
		// Campo: Tipologia Utente (select dropdown)
		// Priority 45: dopo telefono (40), prima di azienda (50)
		if ( $this->options->is_field_enabled( 'user_type' ) ) {
			$fields['billing'][ $this->field_keys['user_type'] ] = array(
				'type'     => 'select',
				'label'    => $this->options->get_field_label( 'user_type' ),
				'required' => true,
				'class'    => array( 'form-row-wide', 'wc-it-fiscal-user-type' ),
				'priority' => $this->options->get_field_priority( 'user_type' ),
				'options'  => array(
					'persona_fisica'    => __( 'Persona Fisica', 'wc-it-fiscal-fields' ),
					'azienda'           => __( 'Azienda', 'wc-it-fiscal-fields' ),
					'associazione_ente' => __( 'Associazione/Ente', 'wc-it-fiscal-fields' ),
				),
				'default'  => 'persona_fisica',
			);
		}

		// Campo: Ragione Sociale (NUOVO in v2.0.0)
		// Priority 46: dopo tipologia utente (45), prima di codice fiscale (47)
		if ( $this->options->is_field_enabled( 'ragione_sociale' ) ) {
			$fields['billing'][ $this->field_keys['ragione_sociale'] ] = array(
				'type'        => 'text',
				'label'       => $this->options->get_field_label( 'ragione_sociale' ),
				'required'    => false, // Obbligatorietà gestita dinamicamente via JS e validazione PHP
				'class'       => array( 'form-row-wide', 'wc-it-fiscal-ragione-sociale' ),
				'priority'    => $this->options->get_field_priority( 'ragione_sociale' ),
				'placeholder' => $this->options->get_field_placeholder( 'ragione_sociale' ),
			);
		}

		// Campo: Codice Fiscale
		// Priority 47: dopo ragione sociale (46)
		if ( $this->options->is_field_enabled( 'cf' ) ) {
			$fields['billing'][ $this->field_keys['codice_fiscale'] ] = array(
				'type'        => 'text',
				'label'       => $this->options->get_field_label( 'cf' ),
				'required'    => false, // Obbligatorietà gestita dinamicamente via JS e validazione PHP
				'class'       => array( 'form-row-wide', 'wc-it-fiscal-codice-fiscale' ),
				'priority'    => $this->options->get_field_priority( 'cf' ),
				'placeholder' => $this->options->get_field_placeholder( 'cf' ),
				'maxlength'   => 16,
			);
		}

		// Campo: Partita IVA
		// Priority 49: dopo codice fiscale (47), prima di azienda (50)
		if ( $this->options->is_field_enabled( 'piva' ) ) {
			$fields['billing'][ $this->field_keys['partita_iva'] ] = array(
				'type'        => 'text',
				'label'       => $this->options->get_field_label( 'piva' ),
				'required'    => false, // Obbligatorietà gestita dinamicamente via JS e validazione PHP
				'class'       => array( 'form-row-wide', 'wc-it-fiscal-partita-iva' ),
				'priority'    => $this->options->get_field_priority( 'piva' ),
				'placeholder' => $this->options->get_field_placeholder( 'piva' ),
				'maxlength'   => 11,
			);
		}

		return $fields;
	}

	/**
	 * Carica script e stili solo nella pagina checkout
	 */
	public function enqueue_scripts() {
		if ( is_checkout() && ! is_order_received_page() ) {
			// JavaScript per logica show/hide
			wp_enqueue_script(
				'wc-it-fiscal-checkout',
				WC_IT_FISCAL_PLUGIN_URL . 'assets/js/checkout-fields.js',
				array( 'jquery' ),
				WC_IT_FISCAL_VERSION,
				true
			);

			// Localizza configurazione per JavaScript
			wp_localize_script(
				'wc-it-fiscal-checkout',
				'WC_IT_Fiscal_Config',
				array(
					'enable_ragione_sociale' => $this->options->is_field_enabled( 'ragione_sociale' ) ? 1 : 0,
					'enable_cf'              => $this->options->is_field_enabled( 'cf' ) ? 1 : 0,
					'enable_piva'            => $this->options->is_field_enabled( 'piva' ) ? 1 : 0,
					'rules'                  => array(
						'ragione_sociale_required_azienda'       => $this->options->is_field_required_for_user_type( 'ragione_sociale', 'azienda' ) ? 1 : 0,
						'ragione_sociale_required_associazione'  => $this->options->is_field_required_for_user_type( 'ragione_sociale', 'associazione_ente' ) ? 1 : 0,
						'cf_required_persona_fisica'             => $this->options->is_field_required_for_user_type( 'cf', 'persona_fisica' ) ? 1 : 0,
						'cf_required_associazione'               => $this->options->is_field_required_for_user_type( 'cf', 'associazione_ente' ) ? 1 : 0,
						'piva_required_azienda'                  => $this->options->is_field_required_for_user_type( 'piva', 'azienda' ) ? 1 : 0,
						'piva_required_associazione'             => $this->options->is_field_required_for_user_type( 'piva', 'associazione_ente' ) ? 1 : 0,
					),
				)
			);

			// CSS per stili custom (opzionale)
			wp_enqueue_style(
				'wc-it-fiscal-checkout',
				WC_IT_FISCAL_PLUGIN_URL . 'assets/css/checkout-fields.css',
				array(),
				WC_IT_FISCAL_VERSION
			);
		}
	}

	/**
	 * Validazione server-side dei campi fiscali
	 *
	 * IMPORTANTE: Questa validazione previene manipolazioni del DOM.
	 * Modificare le regole di obbligatorietà qui se necessario.
	 *
	 * @param array    $data   Dati del checkout.
	 * @param WP_Error $errors Oggetto errori WooCommerce.
	 */
	public function validate_checkout_fields( $data, $errors ) {
		$user_type        = isset( $data[ $this->field_keys['user_type'] ] ) ? sanitize_text_field( $data[ $this->field_keys['user_type'] ] ) : '';
		$ragione_sociale  = isset( $data[ $this->field_keys['ragione_sociale'] ] ) ? sanitize_text_field( $data[ $this->field_keys['ragione_sociale'] ] ) : '';
		$codice_fiscale   = isset( $data[ $this->field_keys['codice_fiscale'] ] ) ? sanitize_text_field( $data[ $this->field_keys['codice_fiscale'] ] ) : '';
		$partita_iva      = isset( $data[ $this->field_keys['partita_iva'] ] ) ? sanitize_text_field( $data[ $this->field_keys['partita_iva'] ] ) : '';

		// Verifica che la tipologia utente sia selezionata
		if ( empty( $user_type ) ) {
			$errors->add( 'billing_user_type', __( 'Seleziona la tipologia di utente.', 'wc-it-fiscal-fields' ) );
			return;
		}

		// Validazione in base alla tipologia utente
		switch ( $user_type ) {
			case 'persona_fisica':
				// Persona fisica: Codice Fiscale obbligatorio
				if ( empty( $codice_fiscale ) && $this->options->is_field_required_for_user_type( 'cf', 'persona_fisica' ) ) {
					$errors->add( 'billing_codice_fiscale', __( 'Il Codice Fiscale è obbligatorio per persone fisiche.', 'wc-it-fiscal-fields' ) );
				}
				// Ragione Sociale non dovrebbe essere compilata
				if ( ! empty( $ragione_sociale ) ) {
					$errors->add( 'billing_ragione_sociale', __( 'La Ragione Sociale non è richiesta per persone fisiche.', 'wc-it-fiscal-fields' ) );
				}
				// Partita IVA non dovrebbe essere compilata
				if ( ! empty( $partita_iva ) ) {
					$errors->add( 'billing_partita_iva', __( 'La Partita IVA non è richiesta per persone fisiche.', 'wc-it-fiscal-fields' ) );
				}
				break;

			case 'azienda':
				// Azienda: Ragione Sociale obbligatoria
				if ( empty( $ragione_sociale ) && $this->options->is_field_required_for_user_type( 'ragione_sociale', 'azienda' ) ) {
					$errors->add( 'billing_ragione_sociale', __( 'La Ragione Sociale è obbligatoria per aziende.', 'wc-it-fiscal-fields' ) );
				}
				// Azienda: Partita IVA obbligatoria
				if ( empty( $partita_iva ) && $this->options->is_field_required_for_user_type( 'piva', 'azienda' ) ) {
					$errors->add( 'billing_partita_iva', __( 'La Partita IVA è obbligatoria per aziende.', 'wc-it-fiscal-fields' ) );
				}
				// Codice Fiscale non dovrebbe essere compilato (nascosto nel frontend)
				if ( ! empty( $codice_fiscale ) ) {
					$errors->add( 'billing_codice_fiscale', __( 'Il Codice Fiscale non è richiesto per aziende.', 'wc-it-fiscal-fields' ) );
				}
				break;

			case 'associazione_ente':
				// Associazione/Ente: Ragione Sociale obbligatoria
				if ( empty( $ragione_sociale ) && $this->options->is_field_required_for_user_type( 'ragione_sociale', 'associazione_ente' ) ) {
					$errors->add( 'billing_ragione_sociale', __( 'La Ragione Sociale è obbligatoria per associazioni/enti.', 'wc-it-fiscal-fields' ) );
				}
				// Associazione/Ente: almeno uno tra CF e P.IVA obbligatorio
				if ( empty( $codice_fiscale ) && empty( $partita_iva ) ) {
					$errors->add( 'billing_fiscal_data', __( 'Inserisci almeno il Codice Fiscale o la Partita IVA.', 'wc-it-fiscal-fields' ) );
				}
				break;
		}

		// Validazione formato Ragione Sociale
		if ( ! empty( $ragione_sociale ) && ! $this->validator->validate_ragione_sociale( $ragione_sociale ) ) {
			$errors->add( 'billing_ragione_sociale', __( 'La Ragione Sociale non è valida (minimo 2 caratteri, massimo 100).', 'wc-it-fiscal-fields' ) );
		}

		// Validazione formato Codice Fiscale (usa validator con algoritmo opzionale)
		if ( ! empty( $codice_fiscale ) && ! $this->validator->validate_codice_fiscale( $codice_fiscale, $user_type ) ) {
			$errors->add( 'billing_codice_fiscale', __( 'Il Codice Fiscale non è valido.', 'wc-it-fiscal-fields' ) );
		}

		// Validazione formato Partita IVA (usa validator con algoritmo opzionale)
		if ( ! empty( $partita_iva ) && ! $this->validator->validate_partita_iva( $partita_iva ) ) {
			$errors->add( 'billing_partita_iva', __( 'La Partita IVA non è valida.', 'wc-it-fiscal-fields' ) );
		}
	}

	/**
	 * Salva i dati fiscali come meta dell'ordine
	 *
	 * @param WC_Order $order Oggetto ordine.
	 * @param array    $data  Dati del checkout.
	 */
	public function save_order_meta( $order, $data ) {
		if ( isset( $data[ $this->field_keys['user_type'] ] ) ) {
			$order->update_meta_data( '_billing_user_type', sanitize_text_field( $data[ $this->field_keys['user_type'] ] ) );
		}

		if ( isset( $data[ $this->field_keys['ragione_sociale'] ] ) && ! empty( $data[ $this->field_keys['ragione_sociale'] ] ) ) {
			$order->update_meta_data( '_billing_ragione_sociale', sanitize_text_field( $data[ $this->field_keys['ragione_sociale'] ] ) );
		}

		if ( isset( $data[ $this->field_keys['codice_fiscale'] ] ) && ! empty( $data[ $this->field_keys['codice_fiscale'] ] ) ) {
			$order->update_meta_data( '_billing_codice_fiscale', sanitize_text_field( strtoupper( $data[ $this->field_keys['codice_fiscale'] ] ) ) );
		}

		if ( isset( $data[ $this->field_keys['partita_iva'] ] ) && ! empty( $data[ $this->field_keys['partita_iva'] ] ) ) {
			$order->update_meta_data( '_billing_partita_iva', sanitize_text_field( $data[ $this->field_keys['partita_iva'] ] ) );
		}
	}

	/**
	 * Visualizza i dati fiscali nella pagina "Grazie per l'ordine"
	 *
	 * @param int $order_id ID dell'ordine.
	 */
	public function display_order_data_thankyou( $order_id ) {
		$this->display_order_fiscal_data( $order_id );
	}

	/**
	 * Visualizza i dati fiscali nella pagina "Il mio account - Dettaglio ordine"
	 *
	 * @param WC_Order $order Oggetto ordine.
	 */
	public function display_order_data_my_account( $order ) {
		$this->display_order_fiscal_data( $order->get_id() );
	}

	/**
	 * Metodo helper per visualizzare i dati fiscali nel frontend
	 *
	 * @param int $order_id ID dell'ordine.
	 */
	private function display_order_fiscal_data( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$user_type        = $order->get_meta( '_billing_user_type' );
		$ragione_sociale  = $order->get_meta( '_billing_ragione_sociale' );
		$codice_fiscale   = $order->get_meta( '_billing_codice_fiscale' );
		$partita_iva      = $order->get_meta( '_billing_partita_iva' );

		// Se non ci sono dati fiscali, non mostrare nulla
		if ( empty( $user_type ) && empty( $ragione_sociale ) && empty( $codice_fiscale ) && empty( $partita_iva ) ) {
			return;
		}

		?>
		<section class="woocommerce-order-fiscal-data">
			<h2 class="woocommerce-column__title"><?php esc_html_e( 'Dati Fiscali', 'wc-it-fiscal-fields' ); ?></h2>
			<address>
				<?php if ( $user_type ) : ?>
					<p>
						<strong><?php esc_html_e( 'Tipologia:', 'wc-it-fiscal-fields' ); ?></strong>
						<?php echo esc_html( $this->get_user_type_label( $user_type ) ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $ragione_sociale ) : ?>
					<p>
						<strong><?php esc_html_e( 'Ragione Sociale:', 'wc-it-fiscal-fields' ); ?></strong>
						<?php echo esc_html( $ragione_sociale ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $codice_fiscale ) : ?>
					<p>
						<strong><?php esc_html_e( 'Codice Fiscale:', 'wc-it-fiscal-fields' ); ?></strong>
						<?php echo esc_html( $codice_fiscale ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $partita_iva ) : ?>
					<p>
						<strong><?php esc_html_e( 'Partita IVA:', 'wc-it-fiscal-fields' ); ?></strong>
						<?php echo esc_html( $partita_iva ); ?>
					</p>
				<?php endif; ?>
			</address>
		</section>
		<?php
	}

	/**
	 * Visualizza i dati fiscali nel backend admin (box Dati Fiscali)
	 *
	 * @param WC_Order $order Oggetto ordine.
	 */
	public function display_admin_order_meta( $order ) {
		$user_type        = $order->get_meta( '_billing_user_type' );
		$ragione_sociale  = $order->get_meta( '_billing_ragione_sociale' );
		$codice_fiscale   = $order->get_meta( '_billing_codice_fiscale' );
		$partita_iva      = $order->get_meta( '_billing_partita_iva' );

		// Se non ci sono dati fiscali, non mostrare il box
		if ( empty( $user_type ) && empty( $ragione_sociale ) && empty( $codice_fiscale ) && empty( $partita_iva ) ) {
			return;
		}

		?>
		<div class="order_data_column" style="clear:both; margin-top: 15px;">
			<h3><?php esc_html_e( 'Dati Fiscali', 'wc-it-fiscal-fields' ); ?></h3>
			<div class="address">
				<?php if ( $user_type ) : ?>
					<p>
						<strong><?php esc_html_e( 'Tipologia utente:', 'wc-it-fiscal-fields' ); ?></strong><br>
						<?php echo esc_html( $this->get_user_type_label( $user_type ) ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $ragione_sociale ) : ?>
					<p>
						<strong><?php esc_html_e( 'Ragione Sociale:', 'wc-it-fiscal-fields' ); ?></strong><br>
						<?php echo esc_html( $ragione_sociale ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $codice_fiscale ) : ?>
					<p>
						<strong><?php esc_html_e( 'Codice Fiscale:', 'wc-it-fiscal-fields' ); ?></strong><br>
						<?php echo esc_html( $codice_fiscale ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $partita_iva ) : ?>
					<p>
						<strong><?php esc_html_e( 'Partita IVA:', 'wc-it-fiscal-fields' ); ?></strong><br>
						<?php echo esc_html( $partita_iva ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Helper per ottenere l'etichetta tradotta della tipologia utente
	 *
	 * @param string $user_type Valore del campo user_type.
	 * @return string
	 */
	private function get_user_type_label( $user_type ) {
		$labels = array(
			'persona_fisica'    => __( 'Persona Fisica', 'wc-it-fiscal-fields' ),
			'azienda'           => __( 'Azienda', 'wc-it-fiscal-fields' ),
			'associazione_ente' => __( 'Associazione/Ente', 'wc-it-fiscal-fields' ),
		);

		return isset( $labels[ $user_type ] ) ? $labels[ $user_type ] : $user_type;
	}
}
