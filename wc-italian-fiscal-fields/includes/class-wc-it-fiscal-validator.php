<?php
/**
 * Classe per la validazione avanzata dei campi fiscali
 *
 * Implementa algoritmi di validazione per:
 * - Codice Fiscale (con controllo carattere di controllo)
 * - Partita IVA (con algoritmo modulo 11)
 * - Ragione Sociale (validazione basic)
 *
 * @package WC_IT_Fiscal_Fields
 * @author  Rocco Fusella
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe WC_IT_Fiscal_Validator
 */
class WC_IT_Fiscal_Validator {

	/**
	 * Istanza della classe Options
	 *
	 * @var WC_IT_Fiscal_Options
	 */
	private $options;

	/**
	 * Tabella conversione caratteri dispari per CF
	 *
	 * @var array
	 */
	private $cf_odd_chars = array(
		'0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
		'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
		'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
		'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23,
	);

	/**
	 * Tabella conversione caratteri pari per CF
	 *
	 * @var array
	 */
	private $cf_even_chars = array(
		'0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
		'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9,
		'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
		'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25,
	);

	/**
	 * Costruttore
	 *
	 * @param WC_IT_Fiscal_Options $options Istanza opzioni.
	 */
	public function __construct( WC_IT_Fiscal_Options $options ) {
		$this->options = $options;
	}

	/**
	 * Valida il Codice Fiscale
	 *
	 * @param string $value     Valore da validare.
	 * @param string $user_type Tipologia utente (opzionale, per logging).
	 * @return bool
	 */
	public function validate_codice_fiscale( $value, $user_type = '' ) {
		// Trim e uppercase
		$value = strtoupper( trim( $value ) );

		// Validazione formato base: 16 caratteri alfanumerici
		if ( ! preg_match( '/^[A-Z0-9]{16}$/i', $value ) ) {
			return false;
		}

		// Se validazione algoritmica è abilitata, esegui controllo checksum
		if ( $this->options->is_advanced_validation_enabled() ) {
			return $this->validate_cf_algorithm( $value );
		}

		return true;
	}

	/**
	 * Valida la Partita IVA
	 *
	 * @param string $value Valore da validare.
	 * @return bool
	 */
	public function validate_partita_iva( $value ) {
		// Trim
		$value = trim( $value );

		// Validazione formato base: 11 cifre numeriche
		if ( ! preg_match( '/^[0-9]{11}$/', $value ) ) {
			return false;
		}

		// Se validazione algoritmica è abilitata, esegui controllo modulo 11
		if ( $this->options->is_advanced_validation_enabled() ) {
			return $this->validate_piva_algorithm( $value );
		}

		return true;
	}

	/**
	 * Valida la Ragione Sociale
	 *
	 * @param string $value Valore da validare.
	 * @return bool
	 */
	public function validate_ragione_sociale( $value ) {
		// Trim
		$value = trim( $value );

		// Deve essere non vuoto
		if ( empty( $value ) ) {
			return false;
		}

		// Lunghezza minima 2 caratteri
		if ( strlen( $value ) < 2 ) {
			return false;
		}

		// Lunghezza massima 100 caratteri
		if ( strlen( $value ) > 100 ) {
			return false;
		}

		return true;
	}

	/**
	 * Algoritmo di validazione Codice Fiscale (carattere di controllo)
	 *
	 * Implementa l'algoritmo ufficiale dell'Agenzia delle Entrate per il
	 * calcolo del carattere di controllo (16° carattere).
	 *
	 * @param string $cf Codice fiscale da validare (16 caratteri).
	 * @return bool
	 */
	private function validate_cf_algorithm( $cf ) {
		$cf  = strtoupper( $cf );
		$sum = 0;

		// Somma caratteri dispari (posizioni 1, 3, 5, ..., 15)
		for ( $i = 0; $i < 15; $i += 2 ) {
			$char = $cf[ $i ];
			if ( isset( $this->cf_odd_chars[ $char ] ) ) {
				$sum += $this->cf_odd_chars[ $char ];
			} else {
				return false; // Carattere non valido
			}
		}

		// Somma caratteri pari (posizioni 2, 4, 6, ..., 14)
		for ( $i = 1; $i < 15; $i += 2 ) {
			$char = $cf[ $i ];
			if ( isset( $this->cf_even_chars[ $char ] ) ) {
				$sum += $this->cf_even_chars[ $char ];
			} else {
				return false; // Carattere non valido
			}
		}

		// Calcola carattere di controllo
		$remainder      = $sum % 26;
		$check_char     = chr( 65 + $remainder ); // A=65 in ASCII
		$expected_char  = $cf[15];

		return $check_char === $expected_char;
	}

	/**
	 * Algoritmo di validazione Partita IVA (modulo 11)
	 *
	 * Implementa l'algoritmo standard italiano per il controllo della
	 * Partita IVA tramite calcolo modulo 11.
	 *
	 * @param string $piva Partita IVA da validare (11 cifre).
	 * @return bool
	 */
	private function validate_piva_algorithm( $piva ) {
		$sum = 0;

		// Somma cifre dispari (posizioni 1, 3, 5, 7, 9)
		for ( $i = 0; $i < 11; $i += 2 ) {
			$sum += intval( $piva[ $i ] );
		}

		// Somma cifre pari moltiplicate per 2 (posizioni 2, 4, 6, 8, 10)
		for ( $i = 1; $i < 10; $i += 2 ) {
			$doubled = intval( $piva[ $i ] ) * 2;
			// Se il risultato è > 9, sottrai 9
			if ( $doubled > 9 ) {
				$doubled -= 9;
			}
			$sum += $doubled;
		}

		// L'ultima cifra deve rendere la somma multipla di 10
		$check_digit   = intval( $piva[10] );
		$expected_digit = ( 10 - ( $sum % 10 ) ) % 10;

		return $check_digit === $expected_digit;
	}
}
