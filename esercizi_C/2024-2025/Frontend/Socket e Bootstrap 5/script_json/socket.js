window.addEventListener("load", function() {
    // Caricamento dinamico per la sezione "INTRODUZIONE"
    document.getElementById("intro-container").innerHTML = `
        <br>
        <div class="row">
            <div class="col-md-4">
                <img src="../immagini/stream-socket-connessione.png" alt="Esempio socket" class="img-fluid rounded">
            </div>
            <div class="col-md-8">
                <p class="lead justified-text">
                    Un socket è un oggetto software che permette l'invio e la ricezione di dati, tra host remoti (tramite una rete) o tra <a href="../pagine/glossario.html#term1" class="glossary-link">processi locali (Inter-Process Communication)</a>.
                    Più precisamente, il concetto di socket si basa sul modello Input/Output su file di Unix, quindi sulle operazioni di open, read, write e close; l'utilizzo, infatti, avviene secondo le stesse modalità, aggiungendo i parametri utili alla comunicazione, quali indirizzi, <a href="https://it.wikipedia.org/wiki/Porte_TCP_e_UDP_standard" >numeri di porta</a> e protocolli.
                    Socket locali e remoti in comunicazione formano una coppia (pair), composta da indirizzo e porta di client e server; tra di loro c'è una connessione logica.
                    Solitamente i sistemi operativi forniscono delle API per permettere alle applicazioni di controllare e utilizzare i socket di rete.
                    Possiamo vedere i socket come degli intermediari tra il livello applicazione e di trasporto nello stack <a href="../pagine/glossario.html#term2" class="glossary-link">TCP/IP</a>. Infatti la funzione dei socket è quella di indirizzamento dei processi.
                </p>
            </div>
        </div>
    `;

    // Caricamento dinamico per la sezione "Famiglie di Socket"
    document.getElementById("famiglie-title").innerHTML = "Famiglie di Socket";
    document.getElementById("famiglie-text").innerHTML = `
        <p class="lead mt-3 mb-2">
            I tipi di protocolli utilizzati dal socket ne definiscono la famiglia (o dominio). Possiamo distinguere, ad esempio, due importanti famiglie:
        </p>
        <ul class="list-unstyled ms-3">
            <a href="../pagine/glossario.html#term3" class="glossary-link"><li><strong>AF_INET</strong></a>: comunicazione tra host remoti, tramite Internet;</li>
            <a href="../pagine/glossario.html#term4" class="glossary-link"><li><strong>AF_UNIX</strong></a>: comunicazione tra processi locali, su macchine Unix. Questa famiglia è anche chiamata *Unix Domain Socket*.</li>
        </ul>
    `;

    // Caricamento dinamico per la sezione "Tipi di Socket"
    document.getElementById("tipi-title").innerHTML = "Tipi di socket";
    
    // Stream socket
    document.getElementById("stream-container").innerHTML = `
        <div class="col-md-4">
            <img src="../immagini/stream.jpg" alt="Stream Socket" class="img-fluid rounded">
        </div>
        <div class="col-md-8">
            <p class="lead">
                <strong>Stream socket</strong>: orientati alla connessione (connection-oriented), basati su protocolli affidabili come TCP o SCTP.
                Un stream socket è utilizzato per le comunicazioni basate su TCP (Transmission Control Protocol), che è un protocollo di trasporto orientato alla connessione.
                Questo tipo di socket stabilisce una connessione tra due endpoint (host) prima che i dati possano essere inviati.
                I dati vengono trasmessi in modo sequenziale e affidabile, garantendo che arrivino senza errori, nell'ordine corretto e senza duplicati.
                Tipico esempio di utilizzo: il protocollo HTTP (per navigare sul web) o <a href="https://it.wikipedia.org/wiki/File_Transfer_Protocol" >FTP (per il trasferimento di file)</a>.
                La caratteristica principale è la connessione che deve essere stabilita prima della trasmissione.
            </p>
        </div>
    `;

    // Datagram socket
    document.getElementById("datagram-container").innerHTML = `
        <div class="col-md-4 order-md-2">
            <img src="../immagini/datagram-sockets.jpg" alt="Datagram Socket" class="img-fluid rounded">
        </div>
        <div class="col-md-8">
            <p class="lead">
                <strong>Datagram socket</strong>: non orientati alla connessione (connectionless), basati sul protocollo veloce ma inaffidabile UDP.
                Un datagram socket è usato per le comunicazioni basate su <a href="../pagine/glossario.html#term5" class="glossary-link">UDP (User Datagram Protocol)</a>, che è un protocollo di trasporto senza connessione.
                Con i datagram, non c'è bisogno di stabilire una connessione prima di inviare i dati. I dati vengono inviati sotto forma di pacchetti chiamati datagrammi.
                UDP non garantisce la consegna dei pacchetti, quindi i dati possono arrivare in ordine diverso o addirittura non arrivare affatto.
                È più veloce rispetto a TCP, ma non adatto per applicazioni che richiedono affidabilità, come il trasferimento di file o la navigazione web.
                Tipico esempio di utilizzo: giochi online, streaming video/audio, o <a href="https://it.wikipedia.org/wiki/Domain_Name_System">DNS (Domain Name System)</a>.
            </p>
        </div>
    `;

    // Raw socket
    document.getElementById("raw-container").innerHTML = `
        <div class="col-md-4">
            <img src="../immagini/raw.jpg" alt="Raw Socket" class="img-fluid rounded">
        </div>
        <div class="col-md-8">
            <p class="lead">
                <strong>Raw socket (raw IP)</strong>: il livello di trasporto viene bypassato, e l'header è accessibile al livello applicativo.
                Un raw socket fornisce accesso diretto al livello di rete (livello IP) del modello OSI, permettendo di inviare e ricevere pacchetti senza l'uso di protocolli di trasporto come TCP o UDP.
                Con i raw socket, è possibile manipolare direttamente l'intestazione del pacchetto, il che è utile per applicazioni come l'analisi dei pacchetti <a href="https://it.wikipedia.org/wiki/Sniffing">(packet sniffing)</a> o l'invio di pacchetti personalizzati.
                Viene generalmente utilizzato da software di basso livello come strumenti di diagnostica o firewall, ma può richiedere privilegi amministrativi per essere utilizzato.
                Poiché i raw socket permettono di inviare pacchetti arbitrari, sono più flessibili ma anche più complessi e pericolosi se mal utilizzati.
            </p>
        </div>
    `;
});
