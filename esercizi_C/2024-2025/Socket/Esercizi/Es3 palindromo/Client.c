#include <stdio.h>
#include <stdlib.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <netdb.h>
#include <string.h>
#include <fcntl.h>
#include <signal.h>
#include <errno.h>
#include <ctype.h>
#include <unistd.h>
#include <stdbool.h> //per il boolean

#define DIM 512
#define SERVERPORT 1313

/*
Scrivere il codice in C, di un applicazione Socket CLIENT-SERVER in cui il server riceve in input 1 stringa
e, dopo aver effettuato gli eventuali ed opportuni controlli (se necessari), rispedisce al Client il messaggio se
è palindroma oppure non lo è.
*/

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

    // Inserimento della stringa da inviare al server
    printf("Inserisci una stringa: ");
    //fgets(stringa, DIM, stdin);
    scanf("%s",stringa);

    // Invia la stringa al server
    write(socketTrasporto, stringa, strlen(stringa));

    // Legge la risposta dal server
    read(socketTrasporto, risposta, sizeof(risposta));

    // Stampa la stringa ricevuta dal server
    printf("Risposta ricevuta dal server: %s\n", risposta);

    close(socketTrasporto);
    return 0;
}