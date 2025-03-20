# E-commerce PC Componenti - Versione PHP con Database

Questo progetto è una versione migliorata dell'e-commerce di PC Componenti, ora con backend PHP e database MySQL.

## Requisiti

- PHP 7.4 o superiore
- MySQL 5.7 o superiore
- Server web (Apache, Nginx, ecc.)

## Installazione

1. Clona o scarica il repository nella directory del tuo server web.

2. Crea un database MySQL per il progetto.

3. Configura le credenziali del database nel file `config/database.php`:

```php
$host = 'localhost';      // Modifica se necessario
$db_name = 'ecommerce_db'; // Modifica con il nome del tuo database
$username = 'root';       // Modifica con il tuo username MySQL
$password = '';           // Modifica con la tua password MySQL
```

4. Esegui lo script di setup del database visitando:

```
http://localhost/percorso-al-progetto/config/setup_database.php
```

5. Esegui lo script per inserire i dati di esempio visitando:

```
http://localhost/percorso-al-progetto/config/insert_sample_data.php
```

6. Accedi al sito:

```
http://localhost/percorso-al-progetto/index.html
```

## Struttura del Progetto

- `api/`: Contiene gli endpoint API PHP
- `config/`: File di configurazione e script di setup
- `includes/`: Classi PHP riutilizzabili
- `js/`: File JavaScript
- `stili/`: File CSS
- `pagine/`: Pagine HTML
- `immagini/`: Immagini e risorse

## Funzionalità

- Catalogo prodotti con filtri
- Dettaglio prodotto con varianti
- Carrello di acquisto
- Checkout con applicazione coupon
- Sistema di autenticazione utenti
- Dashboard utente
- Gestione ordini
- Questionario per profilo utente

## Migrazione da JSON a Database

Questo progetto è stato migrato da un'implementazione basata su file JSON statici a un sistema dinamico con database MySQL. I principali cambiamenti includono:

1. Creazione di un database relazionale per memorizzare prodotti, utenti, ordini, ecc.
2. Implementazione di API PHP per fornire i dati al frontend
3. Sistema di autenticazione e gestione sessioni
4. Persistenza del carrello tra sessioni e dopo il login

## Note per lo Sviluppo

- Per aggiungere nuovi prodotti, utilizzare il pannello di amministrazione o inserirli direttamente nel database.
- I coupon di esempio sono: "Sconto10", "Sconto20", "Sconto30".
- Per testare il sistema di autenticazione, registrare un nuovo account tramite la pagina di registrazione.