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

int main()
{

    struct sockaddr_in servizio, indirizzo_remoto;
    int socketTrasporto, client;
    socklen_t fromlen = sizeof(indirizzo_remoto);
    char stringa[DIM];

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
        // Apertura  del socket per la comunicazione con il client
        client = accept(socketTrasporto, (struct sockaddr *)&indirizzo_remoto, &fromlen);

        // Legge la stringa inviata dal client
        read(client, stringa, sizeof(stringa));
        perror("Errore nella read");
        close(client);

        printf("Stringa ricevuta: %s\n", stringa);

        // Converte
        for (int i = 0; stringa[i]; i++)
        {
            stringa[i] = toupper(stringa[i]);
        }

        printf("Stringa convertita in maiuscolo: %s\n", stringa);

        // Reinvio della stringa maiuscola
        write(client, stringa, strlen(stringa));

        close(client);
    }

    return 0;
}
