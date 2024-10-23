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
#define SERVERPORT 1316
#define DIM_messaggi 2000
#define Vet 10
/*
Scrivere il codice in C, di un applicazione Socket CLIENT-SERVER in cui il server riceve in input un vettore
di numeri interi, dopo aver effettuato gli eventuali ed opportuni controlli (se necessari), rispedisce al Client il
massimo ed il minimo .
*/

int main()
{
    int sock;
    struct sockaddr_in server;
    char messaggio[DIM_messaggi], risposta_server[DIM_messaggi];
    int vettore[Vet], max, min;
    // Creazione del socket
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1)
    {
        printf("Impossibile creare il socket");
        return 1;
    }

    server.sin_addr.s_addr = htonl(INADDR_ANY);
    server.sin_family = AF_INET;
    server.sin_port = htons(SERVERPORT);

    // Connessione al server
    if (connect(sock, (struct sockaddr *)&server, sizeof(server)) < 0)
    {
        printf("Connessione fallita");
        return 1;
    }

    printf("Connesso al server\n");

    // Creazione del vettore
    for (int i = 0; i < Vet; i++)
    {
        printf("Inserisci il numero %d°: ", i);
        scanf("%d", &vettore[i]);
    }

    // Invio vettore con send
    if (send(sock, vettore, sizeof(vettore), 0) < 0)
    {
        printf("Invio fallito\n");
        return 1;
    }

    // read per somma e media
    read(sock, &max, sizeof(max));
    read(sock, &min, sizeof(min));
    printf("Max: %d",max);
    printf("Min: %d",min);
    close(sock);

    return 0;
}