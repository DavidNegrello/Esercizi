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

#define DIM 512
#define SERVERPORT 1313

int main()
{
    struct sockaddr_in servizio, indirizzo_remoto;
    int socketTrasporto, client;
    socklen_t fromlen = sizeof(indirizzo_remoto);
    char stringa[DIM];
    char risposta[DIM];
    int vocali = 0, consonanti = 0;

    // Configurazione dei dati del socket
    servizio.sin_family = AF_INET;
    servizio.sin_addr.s_addr = htonl(INADDR_ANY);
    servizio.sin_port = htons(SERVERPORT);

    // Creazione del socket
    socketTrasporto = socket(AF_INET, SOCK_STREAM, 0);

    // Associazione del socket all'indirizzo del server
    bind(socketTrasporto, (struct sockaddr *)&servizio, sizeof(servizio));

    // Mettiamo il server in ascolto per le richieste dei client
    listen(socketTrasporto, 10);
    printf("Server in ascolto sulla porta %d...\n", SERVERPORT);

    while (1)
    {
        client = accept(socketTrasporto, (struct sockaddr *)&indirizzo_remoto, &fromlen);

        // Legge la stringa inviata dal client
        read(client, stringa, sizeof(stringa));
        printf("Stringa ricevuta: %s\n", stringa);

        // Conta vocali e consonanti convertendo
        for (int i = 0; stringa[i] != '\0'; i++)
        {
            char carattere = tolower(stringa[i]); // Converte il carattere per facilitare la lettura

            //  lettere dalla (a-z)
            if (carattere >= 'a' && carattere <= 'z')
            {
                // Verifica se è una vocale
                if (carattere == 'a' || carattere == 'e' || carattere == 'i' || carattere == 'o' || carattere == 'u')
                {
                    vocali++;
                }
                else
                {
                    consonanti++;
                }
            }
        }
        snprintf(risposta, sizeof(risposta), "Vocali: %d, Consonanti: %d", vocali, consonanti);
        printf("Risposta inviata al client: %s\n", risposta);

        //  risposta per il client
        write(client, risposta, strlen(risposta));

        close(client);
    }
    close(socketTrasporto);

    return 0;
}