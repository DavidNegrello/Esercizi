#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>

#define DIM 64
#define BLOCKS 16

typedef struct 
{
    unsigned char buffer[DIM];
    int byteLetti;
}Bufferone;

Bufferone struttura[BLOCKS];



int main(int argc, char *argv[])
{
    pthread_t tLeggi,tScrivi;
    FILE *origine,*destinazione;
    if (argc!=3)
    {
        printf("Errore argomenti \n");
        exit(-1);
    }
    origine=fopen(argv[1],"r");
    if (origine==NULL)
    {
        printf("Errore argomenti \n");
        exit(-1);
    }
    destinazione=fopen(argv[1],"r");
    if (origine==NULL)
    {
        printf("Errore argomenti \n");
        exit(-1);
    }
    return 0;
}