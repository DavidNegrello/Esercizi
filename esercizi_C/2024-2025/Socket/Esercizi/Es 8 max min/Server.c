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
#define BUFFER_SIZE 1024
#define DIM_messaggi 2000
/*
Scrivere il codice in C, di un applicazione Socket CLIENT-SERVER in cui il server riceve in input un vettore
di numeri interi, dopo aver effettuato gli eventuali ed opportuni controlli (se necessari), rispedisce al Client il
massimo ed il minimo .
*/

void ControlloVettore(int vettore[], int lunghezza, int *max, int *min) {


    // Inizializza max e min con il primo elemento del vettore
    *max = vettore[0];
    *min = vettore[0];

    for (int i = 1; i < lunghezza; i++) {
        if (vettore[i] > *max) {
            *max = vettore[i];
        }
        if (vettore[i] < *min) {
            *min = vettore[i];
        }
    }
}
int main()
{
    int sock, client_sock, c;
    struct sockaddr_in server, client;
    int vettore[BUFFER_SIZE];
    int max = 0, min = 0;
    // Creazione del socket
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1)
    {
        printf("Impossibile creare il socket");
        return 1;
    }

    // Configurazione del server
    server.sin_addr.s_addr = htonl(INADDR_ANY);
    server.sin_family = AF_INET;
    server.sin_port = htons(SERVERPORT);

    if (bind(sock, (struct sockaddr *)&server, sizeof(server)) < 0)
    {
        perror("Bind fallito");
        return 1;
    }
    else
    {
        printf("Bind completato\n");
    }

    listen(sock, 3);

    // Accetta la connessione
    printf("In attesa di connessioni...\n");
    c = sizeof(struct sockaddr_in);
    client_sock = accept(sock, (struct sockaddr *)&client, (socklen_t *)&c);
    if (client_sock < 0)
    {
        printf("Connessione fallita\n");
        return 1;
    }
    else
    {
        printf("Connessione accettata\n");
    }

    // Ricezione del messaggio del client
    int lunghezza_vettore = read(client_sock, vettore, sizeof(vettore));
    if (lunghezza_vettore > 0)
    {
        lunghezza_vettore /= sizeof(int); // Calcola il numero di interi ricevuti
        printf("Messaggio ricevuto: ");
        for (int i = 0; i < lunghezza_vettore; i++)
        {
            printf("%d ", vettore[i]);
        }
        printf("\n");

        // Funzione per trovare il max e min
        ControlloVettore(vettore, lunghezza_vettore, &max, &min);

        // Invia la risposta al client
        send(client_sock, &max, sizeof(max), 0);
        send(client_sock, &min, sizeof(min), 0);

        close(client_sock);
        close(sock);

        return 0;
    }
}