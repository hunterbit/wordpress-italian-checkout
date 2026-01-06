/**
 * Script per gestire la visibilità condizionale dei campi fiscali nel checkout
 *
 * Logica v2.0.0:
 * - Persona Fisica: mostra solo Codice Fiscale
 * - Azienda: mostra Ragione Sociale + Partita IVA
 * - Associazione/Ente: mostra Ragione Sociale + Codice Fiscale + Partita IVA
 *
 * @package WC_IT_Fiscal_Fields
 */

(function($) {
	'use strict';

	/**
	 * Gestisce la visibilità dei campi fiscali
	 */
	function toggleFiscalFields() {
		// Leggi tipologia utente da SELECT (non più da radio)
		var userType = $('select[name="billing_user_type"]').val();

		// Riferimenti ai campi
		var ragioneSocialeField = $('#billing_ragione_sociale_field');
		var ragioneSocialeInput = $('#billing_ragione_sociale');
		var cfField = $('#billing_codice_fiscale_field');
		var cfInput = $('#billing_codice_fiscale');
		var pivaField = $('#billing_partita_iva_field');
		var pivaInput = $('#billing_partita_iva');

		// Fallback se config non disponibile
		if (typeof WC_IT_Fiscal_Config === 'undefined') {
			console.warn('WC_IT_Fiscal_Config non disponibile, uso logica di default');
			// Logica fallback hardcoded
			ragioneSocialeField.hide();
			cfField.hide();
			pivaField.hide();
			return;
		}

		// Reset degli stati
		ragioneSocialeField.hide();
		cfField.hide();
		pivaField.hide();
		ragioneSocialeInput.prop('required', false);
		cfInput.prop('required', false);
		pivaInput.prop('required', false);

		// Applica la logica in base alla tipologia utente
		switch(userType) {
			case 'persona_fisica':
				// Solo Codice Fiscale
				if (WC_IT_Fiscal_Config.enable_cf) {
					cfField.show();
					cfInput.prop('required', WC_IT_Fiscal_Config.rules.cf_required_persona_fisica ? true : false);
				}
				// Svuota campi non usati
				ragioneSocialeInput.val('');
				pivaInput.val('');
				break;

			case 'azienda':
				// Ragione Sociale + Partita IVA
				if (WC_IT_Fiscal_Config.enable_ragione_sociale) {
					ragioneSocialeField.show();
					ragioneSocialeInput.prop('required', WC_IT_Fiscal_Config.rules.ragione_sociale_required_azienda ? true : false);
				}
				if (WC_IT_Fiscal_Config.enable_piva) {
					pivaField.show();
					pivaInput.prop('required', WC_IT_Fiscal_Config.rules.piva_required_azienda ? true : false);
				}
				// Svuota Codice Fiscale
				cfInput.val('');
				break;

			case 'associazione_ente':
				// Ragione Sociale + Codice Fiscale + Partita IVA
				if (WC_IT_Fiscal_Config.enable_ragione_sociale) {
					ragioneSocialeField.show();
					ragioneSocialeInput.prop('required', WC_IT_Fiscal_Config.rules.ragione_sociale_required_associazione ? true : false);
				}
				if (WC_IT_Fiscal_Config.enable_cf) {
					cfField.show();
					cfInput.prop('required', WC_IT_Fiscal_Config.rules.cf_required_associazione ? true : false);
				}
				if (WC_IT_Fiscal_Config.enable_piva) {
					pivaField.show();
					pivaInput.prop('required', WC_IT_Fiscal_Config.rules.piva_required_associazione ? true : false);
				}
				break;

			default:
				// Se nessuna selezione o valore non valido, nascondi tutti
				ragioneSocialeField.hide();
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

		// Esegui quando cambia la SELECT tipologia utente (non più radio)
		$(document.body).on('change', 'select[name="billing_user_type"]', function() {
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
