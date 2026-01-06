# WooCommerce Italian Fiscal Fields

Plugin WordPress per aggiungere campi fiscali italiani (Codice Fiscale e Partita IVA) al checkout di WooCommerce con logica condizionale basata sulla tipologia di utente.

## Descrizione

Questo plugin aggiunge tre nuovi campi nella sezione di fatturazione del checkout WooCommerce:

1. **Tipologia Utente** (obbligatorio) - Radio button con 3 opzioni:
   - Persona Fisica
   - Azienda
   - Associazione/Ente

2. **Codice Fiscale** - Campo testuale che appare condizionalmente

3. **Partita IVA** - Campo testuale che appare condizionalmente

## Logica di Visibilità

- **Persona Fisica**: Mostra solo Codice Fiscale (obbligatorio)
- **Azienda**: Mostra solo Partita IVA (obbligatorio)
- **Associazione/Ente**: Mostra entrambi i campi (almeno uno obbligatorio)

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

### Posizionamento Campi

I campi sono posizionati tramite il sistema di `priority` di WooCommerce:

- Nome (priority 10)
- Cognome (priority 20)
- Email (priority 30)
- Telefono (priority 40)
- **Tipologia Utente (priority 45)** ⬅️ NUOVO
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
- `_billing_codice_fiscale`
- `_billing_partita_iva`

## Localizzazione

Il plugin è predisposto per le traduzioni con text-domain: `wc-it-fiscal-fields`

File .pot template disponibile in: `/languages/wc-it-fiscal-fields.pot`

## Personalizzazione

### Modificare Priority dei Campi

Nel file `includes/class-wc-it-fiscal-fields.php`, metodo `add_checkout_fields()`, modifica i valori di `priority`:

```php
'priority' => 45, // Cambia questo valore
```

### Modificare Logica di Obbligatorietà

Nel file `includes/class-wc-it-fiscal-fields.php`, metodo `validate_checkout_fields()`, modifica le regole di validazione.

### Modificare Etichette

Tutte le stringhe utilizzano funzioni di localizzazione WordPress (`__()`, `_e()`), quindi possono essere tradotte o modificate tramite file .po/.mo.

## Struttura File

```
wc-italian-fiscal-fields/
├── wc-italian-fiscal-fields.php          # File principale
├── includes/
│   └── class-wc-it-fiscal-fields.php     # Classe principale
├── assets/
│   ├── js/
│   │   └── checkout-fields.js            # JavaScript per show/hide
│   └── css/
│       └── checkout-fields.css           # Stili CSS
├── languages/
│   └── wc-it-fiscal-fields.pot           # Template traduzioni
└── README.md                              # Questo file
```

## Sviluppo Futuro

Possibili miglioramenti:

- ✅ Validazione formale algoritmica di CF e P.IVA
- ✅ Compatibilità con WooCommerce Checkout Blocks
- ✅ Pannello di configurazione admin per personalizzare obbligatorietà
- ✅ Esportazione dati fiscali in CSV/PDF
- ✅ Integrazione con servizi di verifica CF/P.IVA online

## Supporto

Per bug, richieste di funzionalità o contributi, apri una issue su GitHub.

## Licenza

GPL v2 or later

## Autore

Sviluppato seguendo le specifiche del documento CLAUDE.md

## Changelog

### 1.0.0
- Release iniziale
- Campi fiscali base: Tipologia Utente, Codice Fiscale, Partita IVA
- Logica condizionale show/hide
- Validazione frontend e backend
- Visualizzazione in frontend e backend admin
