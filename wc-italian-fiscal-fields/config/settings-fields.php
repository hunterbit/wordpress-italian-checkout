<?php
/**
 * Definizione campi per la pagina di impostazioni WooCommerce
 *
 * Questo file contiene la configurazione di tutti i campi admin
 * per la pagina "WooCommerce → Impostazioni → Campi Fiscali".
 *
 * @package WC_IT_Fiscal_Fields
 * @author  Rocco Fusella
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	// =============================================================================
	// SEZIONE 1: IMPOSTAZIONI GENERALI
	// =============================================================================

	array(
		'id'    => 'wc_it_fiscal_general',
		'title' => __( 'Impostazioni Generali', 'wc-it-fiscal-fields' ),
		'desc'  => __( 'Abilita o disabilita i singoli campi nel checkout.', 'wc-it-fiscal-fields' ),
		'type'  => 'title',
	),

	array(
		'title'   => __( 'Abilita Tipologia Utente', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_enable_user_type',
		'default' => 'yes',
		'type'    => 'checkbox',
		'desc'    => __( 'Mostra il campo "Tipologia Utente" nel checkout (campo fondamentale per la logica condizionale).', 'wc-it-fiscal-fields' ),
	),

	array(
		'title'   => __( 'Abilita Ragione Sociale', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_enable_ragione_sociale',
		'default' => 'yes',
		'type'    => 'checkbox',
		'desc'    => __( 'Mostra il campo "Ragione Sociale" per aziende e associazioni/enti.', 'wc-it-fiscal-fields' ),
	),

	array(
		'title'   => __( 'Abilita Codice Fiscale', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_enable_cf',
		'default' => 'yes',
		'type'    => 'checkbox',
		'desc'    => __( 'Mostra il campo "Codice Fiscale" nel checkout.', 'wc-it-fiscal-fields' ),
	),

	array(
		'title'   => __( 'Abilita Partita IVA', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_enable_piva',
		'default' => 'yes',
		'type'    => 'checkbox',
		'desc'    => __( 'Mostra il campo "Partita IVA" nel checkout.', 'wc-it-fiscal-fields' ),
	),

	array(
		'type' => 'sectionend',
		'id'   => 'wc_it_fiscal_general',
	),

	// =============================================================================
	// SEZIONE 2: CAMPI OBBLIGATORI
	// =============================================================================

	array(
		'id'    => 'wc_it_fiscal_requirements',
		'title' => __( 'Campi Obbligatori', 'wc-it-fiscal-fields' ),
		'desc'  => __( 'Configura quali campi sono obbligatori per ogni tipologia di utente.', 'wc-it-fiscal-fields' ),
		'type'  => 'title',
	),

	// Ragione Sociale
	array(
		'title'   => __( 'Ragione Sociale - Azienda', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_ragione_sociale_required_azienda',
		'default' => 'yes',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Obbligatorio', 'wc-it-fiscal-fields' ),
			'no'  => __( 'Facoltativo', 'wc-it-fiscal-fields' ),
		),
		'desc'    => __( 'La Ragione Sociale è obbligatoria per le aziende?', 'wc-it-fiscal-fields' ),
	),

	array(
		'title'   => __( 'Ragione Sociale - Associazione/Ente', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_ragione_sociale_required_associazione',
		'default' => 'yes',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Obbligatorio', 'wc-it-fiscal-fields' ),
			'no'  => __( 'Facoltativo', 'wc-it-fiscal-fields' ),
		),
		'desc'    => __( 'La Ragione Sociale è obbligatoria per associazioni ed enti?', 'wc-it-fiscal-fields' ),
	),

	// Codice Fiscale
	array(
		'title'   => __( 'Codice Fiscale - Persona Fisica', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_cf_required_persona_fisica',
		'default' => 'yes',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Obbligatorio', 'wc-it-fiscal-fields' ),
			'no'  => __( 'Facoltativo', 'wc-it-fiscal-fields' ),
		),
		'desc'    => __( 'Il Codice Fiscale è obbligatorio per le persone fisiche?', 'wc-it-fiscal-fields' ),
	),

	array(
		'title'   => __( 'Codice Fiscale - Associazione/Ente', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_cf_required_associazione',
		'default' => 'no',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Obbligatorio', 'wc-it-fiscal-fields' ),
			'no'  => __( 'Facoltativo', 'wc-it-fiscal-fields' ),
		),
		'desc'    => __( 'Il Codice Fiscale è obbligatorio per associazioni ed enti? (Se "no", è comunque richiesto almeno uno tra CF e P.IVA)', 'wc-it-fiscal-fields' ),
	),

	// Partita IVA
	array(
		'title'   => __( 'Partita IVA - Azienda', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_piva_required_azienda',
		'default' => 'yes',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Obbligatorio', 'wc-it-fiscal-fields' ),
			'no'  => __( 'Facoltativo', 'wc-it-fiscal-fields' ),
		),
		'desc'    => __( 'La Partita IVA è obbligatoria per le aziende?', 'wc-it-fiscal-fields' ),
	),

	array(
		'title'   => __( 'Partita IVA - Associazione/Ente', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_piva_required_associazione',
		'default' => 'no',
		'type'    => 'select',
		'options' => array(
			'yes' => __( 'Obbligatorio', 'wc-it-fiscal-fields' ),
			'no'  => __( 'Facoltativo', 'wc-it-fiscal-fields' ),
		),
		'desc'    => __( 'La Partita IVA è obbligatoria per associazioni ed enti? (Se "no", è comunque richiesto almeno uno tra CF e P.IVA)', 'wc-it-fiscal-fields' ),
	),

	array(
		'type' => 'sectionend',
		'id'   => 'wc_it_fiscal_requirements',
	),

	// =============================================================================
	// SEZIONE 3: ETICHETTE PERSONALIZZATE
	// =============================================================================

	array(
		'id'    => 'wc_it_fiscal_labels',
		'title' => __( 'Etichette Personalizzate', 'wc-it-fiscal-fields' ),
		'desc'  => __( 'Personalizza le etichette dei campi visualizzate nel checkout.', 'wc-it-fiscal-fields' ),
		'type'  => 'title',
	),

	array(
		'title'    => __( 'Etichetta Tipologia Utente', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_user_type_label',
		'type'     => 'text',
		'default'  => __( 'Tipologia utente', 'wc-it-fiscal-fields' ),
		'desc'     => __( 'Etichetta del campo "Tipologia Utente".', 'wc-it-fiscal-fields' ),
		'css'      => 'min-width:300px;',
	),

	array(
		'title'    => __( 'Etichetta Ragione Sociale', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_ragione_sociale_label',
		'type'     => 'text',
		'default'  => __( 'Ragione Sociale', 'wc-it-fiscal-fields' ),
		'desc'     => __( 'Etichetta del campo "Ragione Sociale".', 'wc-it-fiscal-fields' ),
		'css'      => 'min-width:300px;',
	),

	array(
		'title'    => __( 'Etichetta Codice Fiscale', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_cf_label',
		'type'     => 'text',
		'default'  => __( 'Codice Fiscale', 'wc-it-fiscal-fields' ),
		'desc'     => __( 'Etichetta del campo "Codice Fiscale".', 'wc-it-fiscal-fields' ),
		'css'      => 'min-width:300px;',
	),

	array(
		'title'    => __( 'Etichetta Partita IVA', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_piva_label',
		'type'     => 'text',
		'default'  => __( 'Partita IVA', 'wc-it-fiscal-fields' ),
		'desc'     => __( 'Etichetta del campo "Partita IVA".', 'wc-it-fiscal-fields' ),
		'css'      => 'min-width:300px;',
	),

	array(
		'type' => 'sectionend',
		'id'   => 'wc_it_fiscal_labels',
	),

	// =============================================================================
	// SEZIONE 4: PLACEHOLDER
	// =============================================================================

	array(
		'id'    => 'wc_it_fiscal_placeholders',
		'title' => __( 'Placeholder Campi', 'wc-it-fiscal-fields' ),
		'desc'  => __( 'Personalizza i placeholder (testo di esempio) visualizzati nei campi.', 'wc-it-fiscal-fields' ),
		'type'  => 'title',
	),

	array(
		'title'    => __( 'Placeholder Ragione Sociale', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_ragione_sociale_placeholder',
		'type'     => 'text',
		'default'  => __( 'Es: Nome Azienda Srl', 'wc-it-fiscal-fields' ),
		'desc'     => __( 'Testo di esempio per il campo Ragione Sociale.', 'wc-it-fiscal-fields' ),
		'css'      => 'min-width:300px;',
	),

	array(
		'title'    => __( 'Placeholder Codice Fiscale', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_cf_placeholder',
		'type'     => 'text',
		'default'  => __( 'Es: RSSMRA80A01H501U', 'wc-it-fiscal-fields' ),
		'desc'     => __( 'Testo di esempio per il campo Codice Fiscale.', 'wc-it-fiscal-fields' ),
		'css'      => 'min-width:300px;',
	),

	array(
		'title'    => __( 'Placeholder Partita IVA', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_piva_placeholder',
		'type'     => 'text',
		'default'  => __( 'Es: 12345678901', 'wc-it-fiscal-fields' ),
		'desc'     => __( 'Testo di esempio per il campo Partita IVA.', 'wc-it-fiscal-fields' ),
		'css'      => 'min-width:300px;',
	),

	array(
		'type' => 'sectionend',
		'id'   => 'wc_it_fiscal_placeholders',
	),

	// =============================================================================
	// SEZIONE 5: POSIZIONAMENTO CAMPI
	// =============================================================================

	array(
		'id'    => 'wc_it_fiscal_positioning',
		'title' => __( 'Posizionamento Campi', 'wc-it-fiscal-fields' ),
		'desc'  => __( 'Configura la priorità (ordine) dei campi nel checkout. Valori più bassi = posizionamento più in alto.', 'wc-it-fiscal-fields' ),
		'type'  => 'title',
	),

	array(
		'title'    => __( 'Priority Tipologia Utente', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_user_type_priority',
		'type'     => 'number',
		'default'  => '45',
		'desc'     => __( 'Default: 45 (dopo telefono ~40, prima azienda ~50).', 'wc-it-fiscal-fields' ),
		'custom_attributes' => array(
			'min'  => '1',
			'step' => '1',
		),
	),

	array(
		'title'    => __( 'Priority Ragione Sociale', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_ragione_sociale_priority',
		'type'     => 'number',
		'default'  => '46',
		'desc'     => __( 'Default: 46 (dopo tipologia utente, prima codice fiscale).', 'wc-it-fiscal-fields' ),
		'custom_attributes' => array(
			'min'  => '1',
			'step' => '1',
		),
	),

	array(
		'title'    => __( 'Priority Codice Fiscale', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_cf_priority',
		'type'     => 'number',
		'default'  => '47',
		'desc'     => __( 'Default: 47 (dopo ragione sociale, prima partita IVA).', 'wc-it-fiscal-fields' ),
		'custom_attributes' => array(
			'min'  => '1',
			'step' => '1',
		),
	),

	array(
		'title'    => __( 'Priority Partita IVA', 'wc-it-fiscal-fields' ),
		'id'       => 'wc_it_fiscal_piva_priority',
		'type'     => 'number',
		'default'  => '49',
		'desc'     => __( 'Default: 49 (prima del campo azienda ~50).', 'wc-it-fiscal-fields' ),
		'custom_attributes' => array(
			'min'  => '1',
			'step' => '1',
		),
	),

	array(
		'type' => 'sectionend',
		'id'   => 'wc_it_fiscal_positioning',
	),

	// =============================================================================
	// SEZIONE 6: VALIDAZIONE AVANZATA
	// =============================================================================

	array(
		'id'    => 'wc_it_fiscal_validation',
		'title' => __( 'Validazione Avanzata', 'wc-it-fiscal-fields' ),
		'desc'  => __( 'Abilita algoritmi di validazione avanzata per Codice Fiscale e Partita IVA.', 'wc-it-fiscal-fields' ),
		'type'  => 'title',
	),

	array(
		'title'   => __( 'Validazione Algoritmica', 'wc-it-fiscal-fields' ),
		'id'      => 'wc_it_fiscal_advanced_validation',
		'default' => 'no',
		'type'    => 'checkbox',
		'desc'    => __( 'Abilita validazione algoritmica avanzata: controllo carattere di controllo per Codice Fiscale e modulo 11 per Partita IVA. Se disabilitato, viene effettuato solo il controllo del formato (16 caratteri alfanumerici per CF, 11 cifre per P.IVA).', 'wc-it-fiscal-fields' ),
	),

	array(
		'type' => 'sectionend',
		'id'   => 'wc_it_fiscal_validation',
	),
);
