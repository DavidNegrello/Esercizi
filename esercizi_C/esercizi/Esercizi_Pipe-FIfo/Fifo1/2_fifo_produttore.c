#include <fcntl.h>
#include <errno.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <time.h> // necessario per la funzione time() del random

#define maxRandom 100
#define maxVettore 5
int main(int argc, char *argv[])
{
    int numeroCasuale;
    int vettore[maxVettore];
    int fifo;
    srand(time(NULL));              // per il random
    if (mkfifo("Sium", 0777) == -1) // nome e tutti i permessi
    {
        if (errno != EEXIST)
        {
            printf("Errore creazione fifo\n");
            return 1;
        }
    }
    fifo = open("Sium", O_WRONLY);
    printf("La fifo è stata aperta correttamente\n");
    for (int i = 0; i < maxVettore; i++)
    {
        numeroCasuale = rand() % maxRandom;
        vettore[i] = numeroCasuale;
        if (write(fifo, &vettore[i], sizeof(vettore[i])) == -1)
        {
            printf("Errore scrittura nella fifo\n");
            return 2;
        }
    }

    printf("Scittura eseguita nella fifo\n");
    close(fifo);

    return 0;
}