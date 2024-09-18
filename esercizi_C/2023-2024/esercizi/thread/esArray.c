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
int vettore[SIZE] = {0, 1, 2, 3, 4, 5, 8, 6, 7, 9};

void *RicercaPrima(void *par)
{
    for (int i = 0; i < 5; i++)
    {
        if (vettore[i] == 8)
        {
            printf("Posizione prima: %d\n", i);
        }
    }

    return (void *)0;
}
void *RicercaDopo(void *par)
{
    for (int i = 5; i <= 9; i++)
    {
        if (vettore[i] == 8)
        {
            printf("Posizione dopo: %d\n", i);
        }
    }

    return (void *)0;
}
int main(int argc, char *argv[])
{
    
    pthread_t prima, dopo;

    

    printf("Polesella\n");
    pthread_create(&prima, NULL, &RicercaPrima, NULL);
    pthread_create(&dopo, NULL, &RicercaDopo, NULL);

    pthread_join(prima, NULL);
    pthread_join(dopo, NULL);

    return 0;
}