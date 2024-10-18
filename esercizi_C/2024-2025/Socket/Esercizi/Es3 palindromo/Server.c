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
#define BUFFER_SIZE 1024
/*
Scrivere il codice in C, di un applicazione Socket CLIENT-SERVER in cui il server riceve in input 1 stringa
e, dopo aver effettuato gli eventuali ed opportuni controlli (se necessari), rispedisce al Client il messaggio se
è palindroma oppure non lo è.
*/
int main()
{

    struct sockaddr_in servizio, indirizzo_remoto;
    int socketTrasporto, client;
    socklen_t fromlen = sizeof(indirizzo_remoto);
    char stringa[DIM];
    char buffer[BUFFER_SIZE];

    // Configurazione dei dati del socket
    servizio.sin_family = AF_INET;
    servizio.sin_addr.s_addr = htonl(INADDR_ANY);
    servizio.sin_port = htons(SERVERPORT);

    // Creazione del socket
    socketTrasporto = socket(AF_INET, SOCK_STREAM, 0);

    // Associazione del socket all'indirizzo del server
    bind(socketTrasporto, (struct sockaddr *)&servizio, sizeof(servizio));

    //  server in ascolto
    listen(socketTrasporto, 10);

    printf("Server in ascolto sulla porta %d...\n", SERVERPORT);

    while (true)
    {
        fflush(stdout);

        read(client, buffer, BUFFER_SIZE);
        printf("Received message from client: %s\n", buffer);
        
        close(client);
    }

    return 0;
}