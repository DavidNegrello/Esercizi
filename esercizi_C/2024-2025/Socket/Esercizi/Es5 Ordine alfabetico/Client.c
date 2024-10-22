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
#define DIM_messaggi 2000
/*
Scrivere il codice in C, di un applicazione Socket CLIENT-SERVER in cui il server riceve in input 1 stringa
e, dopo aver effettuato gli eventuali ed opportuni controlli (se necessari), rispedisce al Client il messaggio se
è palindroma oppure non lo è.
*/



int main() {
    int sock;
    struct sockaddr_in server;
    char messaggio[DIM_messaggi], risposta_server[DIM_messaggi];
    
    // Creazione del socket
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1) {
        printf("Impossibile creare il socket");
        return 1;
    }
    
    server.sin_addr.s_addr=htonl(INADDR_ANY);
    server.sin_family = AF_INET;
    server.sin_port = htons(SERVERPORT);
    
    // Connessione al server
    if (connect(sock, (struct sockaddr *)&server, sizeof(server)) < 0) {
        printf("Connessione fallita");
        return 1;
    }
    
    printf("Connesso al server\n");
    
    // Invio della stringa al server
    printf("Inserisci una stringa: ");
    fgets(messaggio, DIM_messaggi, stdin);
    messaggio[strcspn(messaggio, "\n")] = '\0'; // Rimozione newline
    
    if (send(sock, messaggio, strlen(messaggio), 0) < 0) {
        printf("Invio fallito\n");
        return 1;
    }
    
    if (read(sock, risposta_server, DIM_messaggi)> 0) {
        printf("Risposta dal server: %s\n", risposta_server);
    } else {
        printf("Ricezione fallita\n");
    }
    
    close(sock);
    
    return 0;
}
