# E-commerce PHP con Database

Questo progetto è una migrazione dell'e-commerce originale da JavaScript con localStorage a PHP con database MySQL.

## Requisiti

- PHP 7.4 o superiore
- MySQL 5.7 o superiore
- Estensione PDO PHP
- Estensione JSON PHP
- Server web (Apache, Nginx, ecc.)

## Installazione

1. Clona o scarica il repository nella directory del tuo server web
2. Assicurati che la directory `config` sia scrivibile dal server web
3. Accedi a `http://tuo-server/percorso-al-progetto/install.php`
4. Segui la procedura guidata di installazione:
   - Verifica dei requisiti
   - Configurazione del database
   - Creazione delle tabelle
   - Inserimento dei dati di esempio (opzionale)

## Struttura del Database

Il database è composto dalle seguenti tabelle principali:

- `utenti`: Gestisce gli account utente
- `sessioni`: Gestisce le sessioni utente
- `prodotti`: Contiene tutti i prodotti disponibili
- `immagini_prodotto`: Contiene le immagini aggiuntive dei prodotti
- `specifiche_prodotto`: Contiene le specifiche tecniche dei prodotti
- `varianti_prodotto`: Contiene le varianti disponibili per i prodotti
- `carrello`: Gestisce i carrelli degli utenti
- `carrello_prodotti`: Contiene i prodotti nei carrelli
- `ordini`: Gestisce gli ordini effettuati
- `ordini_prodotti`: Contiene i prodotti negli ordini
- `coupon`: Gestisce i codici sconto

## Funzionalità

- Visualizzazione del catalogo prodotti
- Filtri di ricerca per categoria, marca, prezzo
- Dettaglio prodotto con varianti
- Carrello persistente (anche senza login)
- Checkout con applicazione coupon
- Gestione degli ordini

## Migrazione da localStorage a Database

Il progetto è stato migrato da un'implementazione basata su localStorage a una soluzione con database MySQL. I principali cambiamenti includono:

1. Persistenza dei dati anche dopo la chiusura del browser
2. Gestione delle sessioni utente
3. Possibilità di accedere al carrello da dispositivi diversi (con login)
4. Migliore gestione delle varianti di prodotto
5. Tracciamento degli ordini

## API PHP

Il backend è composto da diverse API PHP:

- `api/prodotti.php`: Gestisce le operazioni sui prodotti
- `api/carrello.php`: Gestisce le operazioni sul carrello
- `api/checkout.php`: Gestisce il processo di checkout
- `api/session.php`: Gestisce le sessioni utente

## Note per lo Sviluppo

- I file JavaScript sono stati aggiornati per utilizzare le API PHP invece del localStorage
- La struttura HTML rimane sostanzialmente invariata
- È stata aggiunta la gestione delle sessioni per mantenere il carrello anche senza login
- Quando un utente effettua il login, il carrello della sessione viene associato all'utente

## Coupon di Esempio

Per testare la funzionalità dei coupon, puoi utilizzare i seguenti codici:

- `Sconto10`: 10% di sconto
- `Sconto20`: 20% di sconto
- `Sconto30`: 30% di sconto