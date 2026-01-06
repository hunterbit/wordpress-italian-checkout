/**
 * Script per gestire la visibilità condizionale dei campi fiscali nel checkout
 *
 * Logica:
 * - Persona Fisica: mostra Codice Fiscale, nasconde Partita IVA
 * - Azienda: mostra Partita IVA, nasconde Codice Fiscale
 * - Associazione/Ente: mostra entrambi i campi
 *
 * @package WC_IT_Fiscal_Fields
 */

(function($) {
	'use strict';

	/**
	 * Gestisce la visibilità dei campi fiscali
	 */
	function toggleFiscalFields() {
		var userType = $('input[name="billing_user_type"]:checked').val();
		var cfField = $('#billing_codice_fiscale_field');
		var pivaField = $('#billing_partita_iva_field');
		var cfInput = $('#billing_codice_fiscale');
		var pivaInput = $('#billing_partita_iva');

		// Reset degli stati
		cfField.hide();
		pivaField.hide();
		cfInput.prop('required', false);
		pivaInput.prop('required', false);

		// Applica la logica in base alla tipologia utente
		switch(userType) {
			case 'persona_fisica':
				// Mostra solo Codice Fiscale
				cfField.show();
				cfInput.prop('required', true);
				// Svuota Partita IVA se precedentemente compilata
				pivaInput.val('');
				break;

			case 'azienda':
				// Mostra solo Partita IVA
				pivaField.show();
				pivaInput.prop('required', true);
				// Svuota Codice Fiscale se precedentemente compilato
				cfInput.val('');
				break;

			case 'associazione_ente':
				// Mostra entrambi i campi
				cfField.show();
				pivaField.show();
				// Almeno uno dei due deve essere compilato (validazione lato server)
				break;

			default:
				// Se nessuna selezione, nascondi entrambi
				cfField.hide();
				pivaField.hide();
				break;
		}
	}

	/**
	 * Inizializza gli eventi quando il DOM è pronto
	 */
	$(document).ready(function() {
		// Esegui al caricamento della pagina
		toggleFiscalFields();

		// Esegui quando cambia la selezione della tipologia utente
		$(document.body).on('change', 'input[name="billing_user_type"]', function() {
			toggleFiscalFields();
		});

		// Esegui anche quando WooCommerce aggiorna i frammenti del checkout
		$(document.body).on('updated_checkout', function() {
			toggleFiscalFields();
		});

		// Esegui quando viene attivato l'evento di update dei campi billing
		$(document.body).on('change', 'input.input-text, select', function() {
			// Piccolo delay per permettere a WooCommerce di processare i cambiamenti
			setTimeout(toggleFiscalFields, 100);
		});
	});

})(jQuery);
