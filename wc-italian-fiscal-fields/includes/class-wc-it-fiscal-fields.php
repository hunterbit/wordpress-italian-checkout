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
		'user_type'       => 'billing_user_type',
		'codice_fiscale'  => 'billing_codice_fiscale',
		'partita_iva'     => 'billing_partita_iva',
	);

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
		// Campo: Tipologia Utente (radio buttons)
		// Priority 45: dopo telefono (40), prima di azienda (50)
		$fields['billing'][ $this->field_keys['user_type'] ] = array(
			'type'     => 'radio',
			'label'    => __( 'Tipologia utente', 'wc-it-fiscal-fields' ),
			'required' => true,
			'class'    => array( 'form-row-wide', 'wc-it-fiscal-user-type' ),
			'priority' => 45,
			'options'  => array(
				'persona_fisica'    => __( 'Persona Fisica', 'wc-it-fiscal-fields' ),
				'azienda'           => __( 'Azienda', 'wc-it-fiscal-fields' ),
				'associazione_ente' => __( 'Associazione/Ente', 'wc-it-fiscal-fields' ),
			),
			'default'  => 'persona_fisica',
		);

		// Campo: Codice Fiscale
		// Priority 47: dopo tipologia utente (45)
		$fields['billing'][ $this->field_keys['codice_fiscale'] ] = array(
			'type'        => 'text',
			'label'       => __( 'Codice Fiscale', 'wc-it-fiscal-fields' ),
			'required'    => false, // Obbligatorietà gestita dinamicamente via JS e validazione PHP
			'class'       => array( 'form-row-wide', 'wc-it-fiscal-codice-fiscale' ),
			'priority'    => 47,
			'placeholder' => __( 'Es: RSSMRA80A01H501U', 'wc-it-fiscal-fields' ),
			'maxlength'   => 16,
		);

		// Campo: Partita IVA
		// Priority 49: dopo codice fiscale (47), prima di azienda (50)
		$fields['billing'][ $this->field_keys['partita_iva'] ] = array(
			'type'        => 'text',
			'label'       => __( 'Partita IVA', 'wc-it-fiscal-fields' ),
			'required'    => false, // Obbligatorietà gestita dinamicamente via JS e validazione PHP
			'class'       => array( 'form-row-wide', 'wc-it-fiscal-partita-iva' ),
			'priority'    => 49,
			'placeholder' => __( 'Es: 12345678901', 'wc-it-fiscal-fields' ),
			'maxlength'   => 11,
		);

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
		$user_type       = isset( $data[ $this->field_keys['user_type'] ] ) ? sanitize_text_field( $data[ $this->field_keys['user_type'] ] ) : '';
		$codice_fiscale  = isset( $data[ $this->field_keys['codice_fiscale'] ] ) ? sanitize_text_field( $data[ $this->field_keys['codice_fiscale'] ] ) : '';
		$partita_iva     = isset( $data[ $this->field_keys['partita_iva'] ] ) ? sanitize_text_field( $data[ $this->field_keys['partita_iva'] ] ) : '';

		// Verifica che la tipologia utente sia selezionata
		if ( empty( $user_type ) ) {
			$errors->add( 'billing_user_type', __( 'Seleziona la tipologia di utente.', 'wc-it-fiscal-fields' ) );
			return;
		}

		// Validazione in base alla tipologia utente
		switch ( $user_type ) {
			case 'persona_fisica':
				// Persona fisica: Codice Fiscale obbligatorio
				if ( empty( $codice_fiscale ) ) {
					$errors->add( 'billing_codice_fiscale', __( 'Il Codice Fiscale è obbligatorio per persone fisiche.', 'wc-it-fiscal-fields' ) );
				}
				// Partita IVA non dovrebbe essere compilata
				if ( ! empty( $partita_iva ) ) {
					$errors->add( 'billing_partita_iva', __( 'La Partita IVA non è richiesta per persone fisiche.', 'wc-it-fiscal-fields' ) );
				}
				break;

			case 'azienda':
				// Azienda: Partita IVA obbligatoria
				if ( empty( $partita_iva ) ) {
					$errors->add( 'billing_partita_iva', __( 'La Partita IVA è obbligatoria per aziende.', 'wc-it-fiscal-fields' ) );
				}
				// Codice Fiscale non dovrebbe essere compilato (nascosto nel frontend)
				if ( ! empty( $codice_fiscale ) ) {
					$errors->add( 'billing_codice_fiscale', __( 'Il Codice Fiscale non è richiesto per aziende.', 'wc-it-fiscal-fields' ) );
				}
				break;

			case 'associazione_ente':
				// Associazione/Ente: entrambi possono essere presenti, almeno uno obbligatorio
				if ( empty( $codice_fiscale ) && empty( $partita_iva ) ) {
					$errors->add( 'billing_fiscal_data', __( 'Inserisci almeno il Codice Fiscale o la Partita IVA.', 'wc-it-fiscal-fields' ) );
				}
				break;
		}

		// Validazione formato Codice Fiscale (basic - 16 caratteri alfanumerici)
		if ( ! empty( $codice_fiscale ) && ! preg_match( '/^[A-Z0-9]{16}$/i', $codice_fiscale ) ) {
			$errors->add( 'billing_codice_fiscale', __( 'Il Codice Fiscale deve essere di 16 caratteri alfanumerici.', 'wc-it-fiscal-fields' ) );
		}

		// Validazione formato Partita IVA (11 cifre numeriche)
		if ( ! empty( $partita_iva ) && ! preg_match( '/^[0-9]{11}$/', $partita_iva ) ) {
			$errors->add( 'billing_partita_iva', __( 'La Partita IVA deve essere di 11 cifre numeriche.', 'wc-it-fiscal-fields' ) );
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

		$user_type      = $order->get_meta( '_billing_user_type' );
		$codice_fiscale = $order->get_meta( '_billing_codice_fiscale' );
		$partita_iva    = $order->get_meta( '_billing_partita_iva' );

		// Se non ci sono dati fiscali, non mostrare nulla
		if ( empty( $user_type ) && empty( $codice_fiscale ) && empty( $partita_iva ) ) {
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
		$user_type      = $order->get_meta( '_billing_user_type' );
		$codice_fiscale = $order->get_meta( '_billing_codice_fiscale' );
		$partita_iva    = $order->get_meta( '_billing_partita_iva' );

		// Se non ci sono dati fiscali, non mostrare il box
		if ( empty( $user_type ) && empty( $codice_fiscale ) && empty( $partita_iva ) ) {
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
