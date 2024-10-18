#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <arpa/inet.h>

#define SERVERPORT 1313
#define DIM 512

int main()
{
    int socketTrasporto;
    struct sockaddr_in indirizzo_remoto;
    char stringa[DIM], risposta[DIM];

    // Creazione del socket
    socketTrasporto = socket(AF_INET, SOCK_STREAM, 0);

    indirizzo_remoto.sin_family = AF_INET;
    indirizzo_remoto.sin_port = htons(SERVERPORT);

    // Connessione al server
    connect(socketTrasporto, (struct sockaddr *)&indirizzo_remoto, sizeof(indirizzo_remoto));

    // Inserisci la stringa da inviare al server
    printf("Inserisci una stringa: ");
    fgets(stringa, DIM, stdin);

    // Invia la stringa al server
    write(socketTrasporto, stringa, strlen(stringa));

    // Legge la risposta dal server
    read(socketTrasporto, risposta, sizeof(risposta));

    // Stampa la stringa ricevuta dal server
    printf("Risposta ricevuta dal server: %s\n", risposta);


    close(socketTrasporto);

    return 0;
}