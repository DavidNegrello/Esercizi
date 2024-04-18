#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <string.h>
#include <fcntl.h>
#include <pthread.h>
#define SIZE 100
#define META SIZE/2
int vettore[SIZE] = {0, 1, 2, 3, 4, 5, 8, 6, 7, 9};

void *Ricerca(void *par)
{
    int *indice = (int *)par;

    if (indice == 0)
    {
        for (int i = 0; i < META; i++)
        {
            if (vettore[i] == 8)
            {
                printf("Posizione prima: %d\n", i);
            }
        }
    }else
    {
       for (int i = META; i < SIZE; i++)
        {
            if (vettore[i] == 8)
            {
                printf("Posizione dopo: %d\n", i);
            }
        } 
    }

    pthread_exit(0);
}

int main(int argc, char *argv[])
{
    for (int i = 0; i < SIZE; i++)
    {
        vettore[i] = rand() % 10;
    }
    vettore[META] = 8;

    pthread_t prima, dopo;
    int indice = 0, indiceBello = META;

    printf("Polesella\n");
    pthread_create(&prima, NULL, &Ricerca, &indice);
    pthread_create(&dopo, NULL, &Ricerca, &indiceBello);

    pthread_join(prima, NULL);
    pthread_join(dopo, NULL);

    return 0;
}