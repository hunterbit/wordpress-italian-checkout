# CLAUDE.md

## Contesto progetto

- **Tipo progetto**: Plugin WordPress custom per WooCommerce checkout.
- **Obiettivo**: Aggiungere campi fiscali aggiuntivi specifici per e‑commerce italiani nella pagina di checkout, con logica condizionale in base alla tipologia di utente.
- **Target**: Siti WooCommerce classico (non solo block checkout), PHP 7.4+ / 8.x, WordPress 6.x.

## Feature da implementare

### 1. Campi aggiuntivi checkout

Aggiungere nella sezione di fatturazione (billing) quattro campi:

1. Tipologia di utente (campo richiesto)
   - Valori: `persona_fisica`, `azienda`, `associazione_ente`.
   - Tipo di input: **select** (dropdown).
   - Default: **persona_fisica**.
   - Il valore va salvato come meta dell'ordine.

2. Ragione Sociale **[NUOVO]**
   - Campo testuale.
   - Visibile solo per `azienda` e `associazione_ente`.
   - Nascosto per `persona_fisica`.
   - Obbligatorio per azienda e associazione (configurabile).
   - Salvare in order meta.

3. Codice Fiscale
   - Campo testuale.
   - Obbligatorio per persona fisica.
   - Visibile per persona fisica e associazione/ente, nascosto per azienda.
   - Salvare in order meta.

4. Partita IVA
   - Campo testuale.
   - Rilevante per azienda e associazione/ente, nascosto per persona fisica.
   - Salvare in order meta.

### 2. Logica di visibilità / editabilità

Comportamento richiesto in checkout:

- Se tipologia utente = **persona_fisica**:
  - Ragione Sociale: nascosta.
  - Codice Fiscale: visibile e obbligatorio.
  - Partita IVA: nascosta.

- Se tipologia utente = **azienda**:
  - Ragione Sociale: visibile e obbligatoria.
  - Codice Fiscale: nascosto.
  - Partita IVA: visibile e obbligatoria.

- Se tipologia utente = **associazione/ente**:
  - Ragione Sociale: visibile e obbligatoria.
  - Codice Fiscale: visibile.
  - Partita IVA: visibile.
  - Almeno uno tra CF e P.IVA obbligatorio.  

Requisiti tecnici:

- Implementare la logica sia lato frontend (JS) per UX immediata, sia lato PHP in validazione (non consentire combinazioni incoerenti se qualcuno manipola il DOM).
- In caso di valore mancante per un campo obbligatorio, aggiungere errori al checkout usando le API WooCommerce standard.

### 3. Posizionamento e ordine dei campi

Usare il sistema di **priority** dei campi checkout WooCommerce per controllare l’ordine.

Linee guida:

- Posizionare i nuovi campi nella sezione `billing` usando il filtro `woocommerce_checkout_fields`.
- Ordine consigliato (dalle priorità più basse alle più alte):
  1. Nome (billing_first_name) – priority ~10
  2. Cognome (billing_last_name) – ~20
  3. Email (billing_email) – ~30
  4. Telefono (billing_phone) – ~40
  5. Tipologia utente (NUOVO) – **priority 45** - SELECT con default persona_fisica
  6. Ragione Sociale (NUOVO) – **priority 46** - Condizionale (solo azienda/associazione)
  7. Codice fiscale (NUOVO) – **priority 47** - Condizionale
  8. Partita IVA (NUOVO) – **priority 49** - Condizionale
  9. Azienda (billing_company) – ~50
  10. Indirizzo, CAP, città, ecc. seguono i default.

Indicazioni per Claude:

- Recupera la lista delle priorità di default dai reference WooCommerce e assegna ai nuovi campi una priority intermedia tale da posizionarli subito dopo i dati di contatto (email/telefono) e prima del campo azienda, in modo da rendere chiara la natura dell’utente prima di altri dati di fatturazione.
- Non creare logiche di riordinamento manuale complesse: sfrutta solo il parametro `priority` sui nuovi campi.

### 4. Salvataggio e visualizzazione

- Salvare i valori dei campi in order meta usando gli hook standard in fase di creazione ordine (`woocommerce_checkout_create_order` o equivalente).
- Mostrare i valori:
  - nella pagina “Order received” / “Grazie per il tuo ordine”;  
  - nella pagina “Il mio account” (dettaglio ordine);  
  - nel backend admin nella schermata di dettaglio ordine, in una sezione chiara (es. box “Dati fiscali”).

### 5. Struttura del plugin

Obiettivo: mantenere il plugin semplice ma estensibile.

Richiedi a Claude:

- Creare un plugin standalone con:
  - File principale PHP (header standard WordPress, attivabile da wp-admin).
  - Namespace o prefisso univoco per funzioni, classi, handle JS/CSS (ad es. `wc_it_fiscal_`).
  - Caricamento condizionale di JS/CSS solo nella pagina di checkout.
- Codice ordinato in:
  - una classe principale che registra hook e filtri;
  - una parte dedicata alla definizione dei campi checkout;
  - una parte per validazione e salvataggio;
  - una parte per output nel backend.

### 6. Localizzazione / i18n

- Preparare le stringhe per la traduzione usando le funzioni di localizzazione WordPress (`__()`, `_e()`, ecc.).
- Text-domain del plugin: definisci un text-domain coerente con il nome del plugin (es. `wc-it-fiscal-fields`).

### 7. Stile e qualità del codice

- Seguire gli standard di coding di WordPress e WooCommerce (nomi funzioni snake_case, suffisso o prefisso anti‑collisione).
- Commentare in modo essenziale le parti chiave (soprattutto dove c’è logica JS/PHP combinata).
- Non introdurre dipendenze esterne non necessarie.

## Come voglio che Claude lavori

Quando ti chiedo di implementare o modificare questo plugin:

1. **Leggi questo file prima di proporre soluzioni.**
2. **IMPORTANTE - Autore**: Usa sempre **Rocco Fusella** come autore nei file del plugin (header WordPress, README, documentazione, ecc.).
3. Prima fase: proponi struttura file/classi e signature dei metodi principali.
4. Seconda fase: genera il codice PHP/JS completo in blocchi coerenti e pronti per l'uso, includendo:
   - hook WordPress/WooCommerce usati;
   - definizione dei campi con `priority`;
   - logica JS per mostra/nascondi campi;
   - validazione e salvataggio.
5. Mantieni il codice il più semplice possibile, evitando over‑engineering.
6. Quando possibile, commenta le parti dove un manutentore potrebbe voler cambiare:
   - priorità dei campi;
   - logica di obbligatorietà;
   - etichette e testi.

## Stato Implementazione

### ✅ Versione 2.0.0 - Completata

**Data completamento**: 2026-01-06

**Status**: Implementazione completata in 3 fasi (Infrastructure, Integration, Frontend Assets)

**Nuove funzionalità v2.0.0**:
- ✅ Modificato campo "Tipologia Utente" da radio buttons a **SELECT** con default persona_fisica
- ✅ **NUOVO CAMPO**: Ragione Sociale (priority 46, visibile solo per azienda/associazione)
- ✅ Pagina configurazione admin (WooCommerce → Impostazioni → Campi Fiscali)
- ✅ Validazione algoritmica avanzata CF/P.IVA (configurabile on/off)
- ✅ Sistema opzioni per personalizzazione campi (22 opzioni totali)
- ✅ Architettura refactorizzata con 3 nuove classi

**Campi checkout totali v2.0.0**: 4 campi
1. Tipologia Utente (select, priority 45)
2. Ragione Sociale (text, priority 46) - NUOVO
3. Codice Fiscale (text, priority 47)
4. Partita IVA (text, priority 49)

**Dettagli tecnici v2.0.0**:
- Nuovi file: `class-wc-it-fiscal-options.php`, `class-wc-it-fiscal-validator.php`, `class-wc-it-fiscal-admin-settings.php`, `config/settings-fields.php`
- 22 opzioni configurabili in wp_options
- Full backward compatibility con v1.0.0

**Commit implementazione**:
1. Phase 1: Infrastructure - Nuove classi create (commit: 2dfa04e)
2. Phase 2: Integration - Integrazione nuove classi (commit: 546630b)
3. Phase 3: Frontend Assets - Aggiornamento JS e CSS (commit: 45c12e7)
4. Docs: Aggiornamento README.md per v2.0.0 (commit: f4244c9)

---

### ✅ Versione 1.0.0 - Completata

**Data completamento**: 2026-01-06

Il plugin è stato completamente implementato seguendo tutte le specifiche descritte in questo documento.

#### Struttura File Implementata

```
wc-italian-fiscal-fields/
├── wc-italian-fiscal-fields.php          # File principale con header WordPress
├── includes/
│   └── class-wc-it-fiscal-fields.php     # Classe principale con tutti i metodi
├── assets/
│   ├── js/
│   │   └── checkout-fields.js            # Logica JavaScript show/hide
│   └── css/
│       └── checkout-fields.css           # Stili CSS per i campi
├── languages/
│   └── wc-it-fiscal-fields.pot           # Template traduzioni
└── README.md                              # Documentazione utente
```

#### Funzionalità Implementate

✅ **Campi Checkout**:
- Campo "Tipologia Utente" (radio buttons) - Priority 45
- Campo "Codice Fiscale" (text) - Priority 47
- Campo "Partita IVA" (text) - Priority 49

✅ **Logica Condizionale**:
- JavaScript: Show/hide real-time in base alla selezione
- PHP: Validazione server-side per prevenire manipolazioni

✅ **Validazione**:
- Tipologia utente obbligatoria
- Persona Fisica: CF obbligatorio (16 caratteri alfanumerici)
- Azienda: P.IVA obbligatoria (11 cifre numeriche)
- Associazione/Ente: Almeno uno dei due campi obbligatorio

✅ **Salvataggio**:
- Order meta: `_billing_user_type`, `_billing_codice_fiscale`, `_billing_partita_iva`

✅ **Visualizzazione**:
- Frontend: Pagina "Grazie per l'ordine" e "Il mio account"
- Backend: Box "Dati Fiscali" nella pagina dettaglio ordine admin

✅ **Localizzazione**:
- Text-domain: `wc-it-fiscal-fields`
- File .pot template creato

✅ **Coding Standards**:
- Prefisso `wc_it_fiscal_` per tutte le funzioni
- Classe singleton `WC_IT_Fiscal_Fields`
- Caricamento condizionale JS/CSS solo in checkout
- Commenti esplicativi per manutentori

#### Hook WordPress/WooCommerce Utilizzati

| Hook | Tipo | Scopo |
|------|------|-------|
| `plugins_loaded` | action | Inizializzazione plugin |
| `init` | action | Caricamento text-domain |
| `woocommerce_checkout_fields` | filter | Aggiunta campi checkout |
| `wp_enqueue_scripts` | action | Caricamento JS/CSS |
| `woocommerce_after_checkout_validation` | action | Validazione campi |
| `woocommerce_checkout_create_order` | action | Salvataggio order meta |
| `woocommerce_thankyou` | action | Display frontend (thank you) |
| `woocommerce_view_order` | action | Display frontend (my account) |
| `woocommerce_admin_order_data_after_billing_address` | action | Display backend admin |

#### Metodi Classe Principale

| Metodo | Descrizione |
|--------|-------------|
| `get_instance()` | Singleton pattern |
| `add_checkout_fields()` | Definisce i 3 campi con priority |
| `enqueue_scripts()` | Carica JS/CSS in checkout |
| `validate_checkout_fields()` | Validazione server-side |
| `save_order_meta()` | Salva dati in order meta |
| `display_order_data_thankyou()` | Mostra dati in thank you page |
| `display_order_data_my_account()` | Mostra dati in my account |
| `display_admin_order_meta()` | Mostra box admin |
| `get_user_type_label()` | Helper per etichette tradotte |

#### Come Personalizzare

**Modificare Priority dei Campi**:
- File: `includes/class-wc-it-fiscal-fields.php`
- Metodo: `add_checkout_fields()`
- Parametro: `'priority' => XX`

**Modificare Logica Obbligatorietà**:
- File: `includes/class-wc-it-fiscal-fields.php`
- Metodo: `validate_checkout_fields()`
- Modificare le condizioni nello switch case

**Modificare Etichette**:
- Tutte le stringhe usano funzioni `__()` e `_e()`
- Creare file .po/.mo in `/languages/` per tradurre

## Note future

- In una fase successiva si potrà aggiungere:
  - Validazione formale algoritmica di CF/P.IVA (checksum).
  - Compatibilità specifica con Checkout Blocks WooCommerce (se necessario).
  - Opzioni di configurazione nel backend per rendere i campi opzionali/obbligatori.
  - Integrazione con API Agenzia delle Entrate per verifica online CF/P.IVA.
