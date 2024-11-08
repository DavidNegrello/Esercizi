#include <stdio.h>
#include <stdlib.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <netdb.h>
#include <string.h>
#include <ctype.h>
#include <unistd.h>

/*
Scrivere il codice di un'applicazione socket client-server in linguaggio C.
L'applicazione deve consentire al client di inviare al server un vettore di numeri interi maggiori di zero.
Il server dovrà analizzare il vettore ricevuto dal client e restituire al client:
il vettore con le componenti alternate a due a due (esempio: Vettore inserito: 1,2,3,4 -  vettore alternato: 2,1,4,3 )
e la frequenza della prima componente all’interno del vettore stesso (esempio: Vettore inserito: 1,2,1,4  -
la prima componente si ripete 2 volte all’interno del vettore).
*/

#define SERVERPORT 1515

int ControlloVettore(int vettore[], int lunghezza)
{
    int primaCella = vettore[0];
    int ripetizioni = 0;
    for (int i = 0; i < lunghezza; i++)
    {
        if (primaCella == vettore[i])
        {
            ripetizioni++;
        }
    }

    return ripetizioni;
}
int main()
{
    struct sockaddr_in server, client;
    int sock, client_sock, car;
    int vettore[5], lunghezza;
    int ripetizioni;
    sock = socket(AF_INET, SOCK_STREAM, 0); // creazione della socket
    if (sock == -1)
    {
        printf("Impossibile creare il socket");
        return 1;
    }
    // impostazione server
    server.sin_addr.s_addr = htonl(INADDR_ANY);
    server.sin_family = AF_INET;
    server.sin_port = htons(SERVERPORT);

    bind(sock, (struct sockaddr *)&server, sizeof(server));

    // Accetta la connessione
    printf("In attesa di connessioni...\n");

    listen(sock, 3);
    printf("Server in ascolto...\n");

    car = sizeof(struct sockaddr_in);
    client_sock = accept(sock, (struct sockaddr *)&client, (socklen_t *)&car);
    lunghezza = read(client_sock, vettore, sizeof(vettore));
    if (lunghezza < 0)
    {
        printf("Errore nella lettura\n");
        exit(1);
    }

    for (int i = 0; i < lunghezza; i++)
    {
        printf("%d", vettore[i]);
    }

    ripetizioni = ControlloVettore(vettore, lunghezza);

    if (send(sock, &ripetizioni, sizeof(ripetizioni), 0) < 0)
    {
        printf("Invio fallito\n");
        return 1;
    }

    close(client_sock);
    close(sock);
    return 0;
}
