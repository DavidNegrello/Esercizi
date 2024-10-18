#include <stdio.h>
#include <stdlib.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <string.h>
#include <unistd.h>

#define DIM 512
#define SERVERPORT 1313
#define BUFFER_SIZE 1024

int ControlloStringa(char stringa[], char carattere) {
    int ripetizioniChar = 0;
    for (int i = 0; i < strlen(stringa); i++) {
        if (stringa[i] == carattere) {
            ripetizioniChar++;
        }
    }
    return ripetizioniChar;
}

int main() {
    struct sockaddr_in servizio, indirizzo_remoto;
    int socketTrasporto, client;
    socklen_t fromlen = sizeof(indirizzo_remoto);
    char stringa[DIM];
    char carattere;
    char buffer[BUFFER_SIZE];
    int caratteriContati;

    // Configurazione dei dati del socket
    servizio.sin_family = AF_INET;
    servizio.sin_addr.s_addr = htonl(INADDR_ANY);
    servizio.sin_port = htons(SERVERPORT);

    // Creazione del socket
    socketTrasporto = socket(AF_INET, SOCK_STREAM, 0);
    if (socketTrasporto < 0) {
        perror("Errore nella creazione del socket");
        exit(1);
    }

    // Associazione del socket all'indirizzo del server
    bind(socketTrasporto, (struct sockaddr *)&servizio, sizeof(servizio));


    // Server in ascolto
    listen(socketTrasporto, 10);
    printf("Server in ascolto sulla porta %d...\n", SERVERPORT);
    fflush(stdout);

    // Accettazione della connessione
    client = accept(socketTrasporto, (struct sockaddr *)&indirizzo_remoto, &fromlen);
    if (client < 0) {
        perror("Errore nell'accettazione della connessione");
        close(socketTrasporto);
        exit(1);
    }

    // Lettura della stringa e del carattere
    read(client, stringa, sizeof(stringa));
    read(client, &carattere, sizeof(carattere)); // Usare &carattere

    printf("Stringa dal client: %s\n", stringa);
    caratteriContati = ControlloStringa(stringa, carattere);

    // Scrittura del risultato nel client
    sprintf(buffer, "%d", caratteriContati); // Convertire l'intero in stringa
    write(client, buffer, strlen(buffer)); // Inviare la risposta

    // Chiusura delle connessioni
    close(client);
    close(socketTrasporto);
    return 0;
}