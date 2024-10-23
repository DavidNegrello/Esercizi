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
Scrivere il codice in C, di un applicazione Socket CLIENT-SERVER in cui il server riceve in input un
vettore di numeri interi, dopo aver effettuato gli eventuali ed opportuni controlli (se necessari), rispedisce al
Client la somma e la media dei numeri pari e la somma e la media dei numeri dispari.
*/

void ControlloVettore(int vettore[], int lunghezza, int *somma, int *media)
{
    *somma = 0; // Inizializza la somma
    int numDispari=0;
    for (int i = 0; i < lunghezza; i++)
    {
        if (i%2>0)  //per trovare i dispari
        {
            *somma += vettore[i];
            numDispari++;
        }
    }
    *media = (int)*somma / numDispari; // Calcola la media
}

int main()
{
    int sock, client_sock, c;
    struct sockaddr_in server, client;
    int vettore[BUFFER_SIZE];
    int somma = 0, media = 0;
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

        // Funzione per fare la somma e la media del vettore
        ControlloVettore(vettore, lunghezza_vettore, &somma, &media);

        // Invia la risposta al client
        send(client_sock, &somma, sizeof(somma), 0);
        send(client_sock, &media, sizeof(media), 0);

        close(client_sock);
        close(sock);

        return 0;
    }
}
