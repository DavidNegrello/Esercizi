#include <fcntl.h>
#include <errno.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>


int main(int argc, char *argv[])
{
    printf("Dati ricevuti da P1:\n");
    printf("Nome: %s\n", argv[1]);
    printf("Cognome: %s\n", argv[2]);
    printf("Età: %s\n",argv[3]);    //non funziona e restituisce null
    return 0;
}