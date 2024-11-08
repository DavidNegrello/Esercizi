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
#define DIM_messaggi 2000
/*


*/

void ControlloPalindromo(char *stringa, char *risultato)
{
    int lunghezza = strlen(stringa);
    char stringaInversa[lunghezza + 1];

    // Primo ciclo for: creare la stringa invertita
    int i;
    for (i = 0; i < lunghezza; i++)
    {
        stringaInversa[i] = stringa[lunghezza - i - 1]; // Copia l'ultimo carattere presente nella stringa
    }
    stringaInversa[lunghezza] = '\0'; //terminatore

    // Secondo ciclo for: confrontare la stringa originale con quella invertita
    for (i = 0; i < lunghezza; i++)
    {
        if (stringa[i] != stringaInversa[i])
        {
            strcpy(risultato, "La stringa non è palindroma"); // Aggiorna il risultato
        }
    }

    strcpy(risultato, "La stringa è palindroma"); // Se è un palindromo, aggiorna il risultato
}

int main()
{
    int sock, client_sock, c;
    struct sockaddr_in server, client;
    char messaggio_client[DIM_messaggi],risposta[100];;

    // Creazione del socket
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1)
    {
        printf("Impossibile creare il socket");
        return 1;
    }

    // Configurazione del server
    server.sin_addr.s_addr=htonl(INADDR_ANY);
    server.sin_family = AF_INET;
    server.sin_port = htons(SERVERPORT);

 
    if (bind(sock, (struct sockaddr *)&server, sizeof(server)) < 0)
    {
        printf("Bind fallito\n");
        return 1;
    }
    else{
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
    else{
        printf("Connessione accettata\n");
    }
    

    // Ricezione del messaggio del client 

    if (read(client_sock, messaggio_client, sizeof(messaggio_client) > 0))
    {
        printf("Messaggio ricevuto: %s\n", messaggio_client);

        // Verifica se la stringa è palindroma e aggiorna il risultato
        
        ControlloPalindromo(messaggio_client, risposta); // Passa il risultato come riferimento

        // Invia la risposta al client
        send(client_sock, risposta, strlen(risposta), 0);
    }

    close(client_sock);
    close(sock);

    return 0;
}
