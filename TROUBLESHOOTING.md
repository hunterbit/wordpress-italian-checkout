# Troubleshooting - WooCommerce Italian Fiscal Fields

## Problemi durante l'installazione

Se riscontri errori durante l'installazione del plugin, segui questi passaggi:

### 1. Verifica Requisiti

Prima di installare il plugin, assicurati che il tuo sistema soddisfi i requisiti:

- ✅ **WordPress**: 6.0 o superiore
- ✅ **WooCommerce**: 6.0 o superiore (DEVE essere installato e attivato PRIMA)
- ✅ **PHP**: 7.4 o superiore
- ✅ **PHP Extensions**: Nessuna estensione particolare richiesta

### 2. Abilita Debug WordPress

Per vedere l'errore esatto, abilita il debug di WordPress:

1. Apri il file `wp-config.php` nella root del tuo sito WordPress
2. Trova la riga:
   ```php
   define( 'WP_DEBUG', false );
   ```
3. Sostituiscila con:
   ```php
   define( 'WP_DEBUG', true );
   define( 'WP_DEBUG_LOG', true );
   define( 'WP_DEBUG_DISPLAY', true );
   @ini_set( 'display_errors', 1 );
   ```
4. Salva il file e riprova ad installare il plugin
5. Copia il messaggio di errore completo che appare

### 3. Controlla la versione PHP

Il plugin richiede PHP 7.4 o superiore:

1. Vai in **WordPress Admin → Strumenti → Salute del sito → Info**
2. Cerca la sezione "Server"
3. Verifica che la "Versione PHP" sia >= 7.4

Se hai PHP 7.3 o inferiore, contatta il tuo hosting per l'upgrade.

### 4. Verifica WooCommerce

Il plugin **richiede WooCommerce** per funzionare:

1. Vai in **WordPress Admin → Plugin**
2. Assicurati che **WooCommerce** sia installato e **Attivato**
3. Se WooCommerce non è installato:
   - Vai in **Plugin → Aggiungi nuovo**
   - Cerca "WooCommerce"
   - Installa e attiva WooCommerce
4. Solo dopo, installa questo plugin

### 5. Metodi di installazione

#### Metodo A: Upload ZIP (Consigliato)

1. Scarica il file `wc-italian-fiscal-fields-v2.0.1.zip`
2. Vai in **WordPress Admin → Plugin → Aggiungi nuovo**
3. Clicca **Carica Plugin**
4. Seleziona il file ZIP
5. Clicca **Installa ora**
6. Attiva il plugin

#### Metodo B: FTP/Copia manuale

1. Decomprimi il file ZIP sul tuo computer
2. Carica la cartella `wc-italian-fiscal-fields` in `/wp-content/plugins/` tramite FTP
3. La struttura finale deve essere:
   ```
   /wp-content/plugins/wc-italian-fiscal-fields/
   ├── wc-italian-fiscal-fields.php
   ├── includes/
   ├── config/
   ├── assets/
   └── ...
   ```
4. Vai in **WordPress Admin → Plugin**
5. Trova "WooCommerce Italian Fiscal Fields" e clicca **Attiva**

### 6. Errori comuni e soluzioni

#### Errore: "Il plugin non ha un header valido"

**Causa**: Il file ZIP ha una struttura errata o è corrotto

**Soluzione**:
- Usa il file `wc-italian-fiscal-fields-v2.0.1.zip` pulito fornito
- Assicurati di non aver modificato il file ZIP
- Scaricalo di nuovo se necessario

#### Errore: "Il plugin richiede WooCommerce"

**Causa**: WooCommerce non è installato o attivato

**Soluzione**:
1. Installa e attiva WooCommerce prima
2. Poi installa questo plugin

#### Errore: "Parse error" o "Syntax error"

**Causa**: Versione PHP troppo vecchia (< 7.4)

**Soluzione**:
- Verifica versione PHP in WordPress → Strumenti → Salute del sito
- Contatta il tuo hosting per upgrade a PHP 7.4+

#### Errore: "Fatal error: Class not found"

**Causa**: File mancanti o corrotti

**Soluzione**:
1. Disattiva e disinstalla il plugin
2. Cancella completamente la cartella `/wp-content/plugins/wc-italian-fiscal-fields/`
3. Reinstalla usando il file ZIP pulito

#### Errore: "Plugin could not be activated"

**Causa**: Conflitto con altro plugin o tema

**Soluzione**:
1. Disattiva temporaneamente tutti gli altri plugin
2. Attiva solo WooCommerce e questo plugin
3. Se funziona, riattiva gli altri plugin uno alla volta per trovare il conflitto

### 7. Log degli errori

Se il plugin si installa ma genera errori:

1. Vai in `/wp-content/debug.log` (se hai abilitato WP_DEBUG_LOG)
2. Cerca errori relativi a "WC_IT_Fiscal" o "wc-it-fiscal"
3. Copia l'errore completo

### 8. Test di integrità file

Per verificare che tutti i file siano presenti:

```
wc-italian-fiscal-fields/
├── wc-italian-fiscal-fields.php          ← File principale (DEVE esistere)
├── includes/
│   ├── class-wc-it-fiscal-fields.php     ← Classe principale
│   ├── class-wc-it-fiscal-options.php    ← Gestione opzioni
│   ├── class-wc-it-fiscal-validator.php  ← Validazione
│   └── class-wc-it-fiscal-admin-settings.php ← Admin
├── config/
│   └── settings-fields.php               ← Configurazione
├── assets/
│   ├── js/
│   │   └── checkout-fields.js
│   └── css/
│       └── checkout-fields.css
├── languages/
│   └── wc-it-fiscal-fields.pot
└── README.md
```

Se manca qualche file, reinstalla il plugin.

### 9. Supporto

Se hai seguito tutti i passaggi e il problema persiste:

1. **Abilita WP_DEBUG** e raccogli il messaggio di errore completo
2. **Verifica versione PHP**: WordPress → Strumenti → Salute del sito → Info
3. **Verifica WooCommerce**: Versione e stato di attivazione
4. **Apri una Issue** su GitHub con:
   - Messaggio di errore completo
   - Versione WordPress
   - Versione WooCommerce
   - Versione PHP
   - Metodo di installazione usato
   - Screenshot se possibile

## Checklist Pre-Installazione

Prima di installare, verifica:

- [ ] WordPress 6.0+
- [ ] WooCommerce 6.0+ installato e attivato
- [ ] PHP 7.4+
- [ ] File ZIP: `wc-italian-fiscal-fields-v2.0.1.zip` (17KB)
- [ ] Spazio su disco disponibile (minimo 1MB)
- [ ] Permessi di scrittura su `/wp-content/plugins/`

Se tutti i check sono OK, procedi con l'installazione.
