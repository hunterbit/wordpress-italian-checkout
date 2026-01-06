# WooCommerce Italian Fiscal Fields

Plugin WordPress per aggiungere campi fiscali italiani (Tipologia Utente, Ragione Sociale, Codice Fiscale e Partita IVA) al checkout di WooCommerce con logica condizionale e pannello di configurazione admin.

## Descrizione

Questo plugin aggiunge quattro nuovi campi nella sezione di fatturazione del checkout WooCommerce:

1. **Tipologia Utente** (obbligatorio) - Select dropdown con 3 opzioni:
   - Persona Fisica (default)
   - Azienda
   - Associazione/Ente

2. **Ragione Sociale** - Campo testuale che appare condizionalmente

3. **Codice Fiscale** - Campo testuale che appare condizionalmente

4. **Partita IVA** - Campo testuale che appare condizionalmente

## Logica di Visibilità

- **Persona Fisica**: Mostra solo Codice Fiscale (configurabile come obbligatorio)
- **Azienda**: Mostra Ragione Sociale + Partita IVA (configurabili come obbligatori)
- **Associazione/Ente**: Mostra tutti e tre i campi: Ragione Sociale, Codice Fiscale e Partita IVA (configurabili come obbligatori)

## Requisiti

- WordPress 6.0 o superiore
- WooCommerce 6.0 o superiore
- PHP 7.4 o superiore

## Installazione

1. Scarica il plugin o clona questa repository
2. Carica la cartella `wc-italian-fiscal-fields` nella directory `/wp-content/plugins/`
3. Attiva il plugin tramite il menu 'Plugin' in WordPress
4. Assicurati che WooCommerce sia installato e attivo

## Caratteristiche

### Validazione

- **Frontend**: Validazione JavaScript in tempo reale per UX ottimale
- **Backend**: Validazione PHP server-side per prevenire manipolazioni
- **Formato Codice Fiscale**: 16 caratteri alfanumerici
- **Formato Partita IVA**: 11 cifre numeriche
- **Validazione Algoritmica**: Opzionale (configurabile) - Verifica algoritmo carattere di controllo CF e modulo 11 P.IVA
- **Ragione Sociale**: Minimo 2 caratteri, massimo 100 caratteri

### Posizionamento Campi

I campi sono posizionati tramite il sistema di `priority` di WooCommerce:

- Nome (priority 10)
- Cognome (priority 20)
- Email (priority 30)
- Telefono (priority 40)
- **Tipologia Utente (priority 45)** ⬅️ NUOVO
- **Ragione Sociale (priority 46)** ⬅️ NUOVO v2.0
- **Codice Fiscale (priority 47)** ⬅️ NUOVO
- **Partita IVA (priority 49)** ⬅️ NUOVO
- Azienda (priority 50)
- Indirizzo, CAP, Città, ecc.

### Visualizzazione Dati

I dati fiscali vengono mostrati in:

1. **Pagina "Grazie per l'ordine"** (Order Received)
2. **Area "Il mio account"** (My Account - Order Details)
3. **Backend Admin** - Box dedicato "Dati Fiscali" nella pagina dettaglio ordine

### Salvataggio

I dati vengono salvati come meta dell'ordine:
- `_billing_user_type`
- `_billing_ragione_sociale`
- `_billing_codice_fiscale`
- `_billing_partita_iva`

## Localizzazione

Il plugin è predisposto per le traduzioni con text-domain: `wc-it-fiscal-fields`

File .pot template disponibile in: `/languages/wc-it-fiscal-fields.pot`

## Configurazione

### Pagina Admin

Vai su **WooCommerce → Impostazioni → Campi Fiscali** per configurare:

#### Abilitazione Campi
- Abilita/Disabilita Tipologia Utente
- Abilita/Disabilita Ragione Sociale
- Abilita/Disabilita Codice Fiscale
- Abilita/Disabilita Partita IVA

#### Regole di Obbligatorietà
- CF obbligatorio per Persona Fisica
- Ragione Sociale obbligatoria per Azienda
- P.IVA obbligatoria per Azienda
- Ragione Sociale obbligatoria per Associazione/Ente
- CF obbligatorio per Associazione/Ente
- P.IVA obbligatoria per Associazione/Ente

#### Etichette Personalizzate
- Etichetta Tipologia Utente
- Etichetta Ragione Sociale
- Etichetta Codice Fiscale
- Etichetta Partita IVA

#### Placeholder
- Placeholder Ragione Sociale
- Placeholder Codice Fiscale
- Placeholder Partita IVA

#### Priority Campi
- Priority Tipologia Utente (default: 45)
- Priority Ragione Sociale (default: 46)
- Priority Codice Fiscale (default: 47)
- Priority Partita IVA (default: 49)

#### Validazione Avanzata
- Abilita validazione algoritmica CF/P.IVA (carattere di controllo e modulo 11)

## Personalizzazione Avanzata

### Modificare Via Codice

Se preferisci modificare il codice direttamente invece di usare le opzioni admin:

#### Priority dei Campi
Nel file `includes/class-wc-it-fiscal-fields.php`, metodo `add_checkout_fields()`.

#### Logica di Validazione
Nel file `includes/class-wc-it-fiscal-validator.php`, metodi `validate_codice_fiscale()` e `validate_partita_iva()`.

#### Etichette
Tutte le stringhe utilizzano funzioni di localizzazione WordPress (`__()`, `_e()`), quindi possono essere tradotte o modificate tramite file .po/.mo.

## Struttura File

```
wc-italian-fiscal-fields/
├── wc-italian-fiscal-fields.php              # File principale
├── includes/
│   ├── class-wc-it-fiscal-fields.php         # Classe principale
│   ├── class-wc-it-fiscal-options.php        # Gestione opzioni
│   ├── class-wc-it-fiscal-validator.php      # Validazione avanzata
│   └── class-wc-it-fiscal-admin-settings.php # Pagina configurazione admin
├── config/
│   └── settings-fields.php                   # Definizione campi admin
├── assets/
│   ├── js/
│   │   └── checkout-fields.js                # JavaScript configuration-driven
│   └── css/
│       └── checkout-fields.css               # Stili CSS
├── languages/
│   └── wc-it-fiscal-fields.pot               # Template traduzioni
└── README.md                                  # Questo file
```

## Sviluppo Futuro

Possibili miglioramenti:

- ✅ Validazione formale algoritmica di CF e P.IVA *(implementato v2.0)*
- ✅ Pannello di configurazione admin *(implementato v2.0)*
- ⬜ Compatibilità con WooCommerce Checkout Blocks
- ⬜ Esportazione dati fiscali in CSV/PDF
- ⬜ Integrazione con servizi di verifica CF/P.IVA online (API Agenzia delle Entrate)

## Supporto

Per bug, richieste di funzionalità o contributi, apri una issue su GitHub.

## Licenza

GPL v2 or later

## Autore

**Rocco Fusella**

- Website: [https://roccofusella.it](https://roccofusella.it)
- GitHub: [https://github.com/roccofusella](https://github.com/roccofusella)

Sviluppato seguendo le specifiche del documento CLAUDE.md

## Changelog

### 2.0.0
- **NUOVO CAMPO**: Ragione Sociale (visibile per Azienda e Associazione/Ente)
- Tipologia Utente: cambiato da radio buttons a **SELECT dropdown** con default persona_fisica
- **Pagina configurazione admin**: WooCommerce → Impostazioni → Campi Fiscali (22 opzioni totali)
- **Validazione algoritmica avanzata**: Opzionale per CF (carattere di controllo) e P.IVA (modulo 11)
- **Sistema configuration-driven**: Architettura refactorizzata con 3 nuove classi (Options, Validator, AdminSettings)
- Enable/disable configurabile per ogni campo
- Obbligatorietà configurabile per tipologia utente
- Etichette, placeholder e priority personalizzabili via admin
- JavaScript dinamico con configurazione da backend
- Full backward compatibility con v1.0.0

### 1.0.0
- Release iniziale
- Campi fiscali base: Tipologia Utente, Codice Fiscale, Partita IVA
- Logica condizionale show/hide
- Validazione frontend e backend
- Visualizzazione in frontend e backend admin
