//CLIENT
#include <stdio.h>
#include <stdlib.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <string.h>
#include <errno.h>
#include <ctype.h>
#include <unistd.h>

#define DIM 512
#define SERVERPORT 1313

int main()
{
    struct sockaddr_in servizio;
    int socketID;
    char str1[DIM], str2[DIM], lettura[DIM];
    int lunghezza;


    servizio.sin_family = AF_INET;
    servizio.sin_addr.s_addr = htonl(INADDR_ANY);
    servizio.sin_port = htons(SERVERPORT);
    socketID = socket(AF_INET,SOCK_STREAM,0);

    connect(socketID, (struct sockaddr*)&servizio, sizeof(servizio));

    printf("Inserisci la prima stringa: ");
    scanf("%s", str1);
    printf("\nInserisci la seconda stringa: ");
    scanf("%s", str2);

    write(socketID, str1, sizeof(str1));
    write(socketID, str2, sizeof(str2));
    
    read(socketID, lettura, sizeof(lettura));
    
    printf("\nStringa ricevuta dal server: %s", lettura);

    close(socketID);
    return 0;
}