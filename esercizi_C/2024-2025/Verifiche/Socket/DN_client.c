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
int main()
{
    
    int sock;
    struct sockaddr_in server;
    int vettore[]={1,2,3,4,1}, ripetizione;
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
    if (connect(sock, (struct sockaddr *)&server, sizeof(server))==-1)
    {
        printf("Connessione fallita");
        return 1;
    }

    printf("Connesso al server\n");


    // Invio vettore con send
    if (send(sock, vettore, sizeof(vettore), 0) < 0)
    {
        printf("Invio fallito\n");
        return 1;
    }
    else
    {
        printf("invio riuscito\n");
    }

    // read della risposta dal server
    read(sock, &ripetizione, sizeof(ripetizione));
    printf("Nel vettore sono presenti %d ripetizioni del primo numero \n",ripetizione);
    close(sock);
    return 0;
}