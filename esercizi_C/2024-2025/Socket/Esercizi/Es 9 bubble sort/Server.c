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

#define DIM 15
#define SERVERPORT 1316
#define BUFFER_SIZE 1024
#define DIM_messaggi 2000
/*
Scrivere il codice in C, di un applicazione Socket CLIENT-SERVER in cui il server riceve in input un
vettore di numeri interi, dopo aver effettuato gli eventuali ed opportuni controlli (se necessari), rispedisce al
Client il vettore ordinato in modo crescente .
*/

void ControlloVettore(int vettore[], int lunghezza, int ordinato[]) {
    int supporto;
    for (int i = 0; i < lunghezza - 1; i++) {
        for (int j = 0; j < lunghezza - i - 1; j++) {
            if (vettore[j] > vettore[j + 1]) {
                 supporto = vettore[j];
                vettore[j] = vettore[j + 1];
                vettore[j + 1] = supporto;
            }
        }
    }
    

}
int main()
{
    int sock, client_sock, c;
    struct sockaddr_in server, client;
    int vettore[DIM], ordinato[DIM];
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
        ControlloVettore(vettore, lunghezza_vettore, ordinato);

        // Invia la risposta al client
        send(client_sock, &max, sizeof(max), 0);

        close(client_sock);
        close(sock);

        return 0;
    }
}