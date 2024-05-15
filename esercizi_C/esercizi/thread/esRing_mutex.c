#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>

#define DIM 64
#define BLOCKS 16

typedef struct
{
    unsigned char buffer[DIM];
    int n;
} Block;

Block buffer[BLOCKS];
int readIndex = 0;
int writeIndex = 0;
int nBlock = 0;
int end = 0;
pthread_cond_t noPieno, noVuoto;
pthread_mutex_t critical, mutex;

void *Lettura(void *args)
{
    int n;
    FILE *file = (FILE *)args;

    while (!feof(file))
    {
        pthread_mutex_lock(&critical);
        if (nBlock >= n)
        {
            pthread_cond_wait(&noPieno, &critical);
        }
        n = fread(buffer[writeIndex].buffer, 1, DIM, file);
        if (n > 0)
        {
            buffer[writeIndex].n = n;
            writeIndex = (writeIndex + 1) % BLOCKS;
            pthread_mutex_lock(&mutex);
            nBlock++;
            pthread_cond_signal(&noVuoto);
            pthread_mutex_unlock(&mutex);
        }
        pthread_mutex_unlock(&critical);
    }
    end = 1;
    pthread_cond_signal(&noVuoto);
    pthread_exit(NULL);
}

void *Scrittura(void *args)
{
    FILE *file = (FILE *)args;

    while (1)
    {
        if (end && nBlock == 0)
        {
            break;
        }
        pthread_mutex_lock(&critical);
        if (nBlock > 0)
        {
            readIndex = fwrite(buffer[readIndex].buffer, 1, buffer[readIndex].n, file);
            readIndex = (readIndex + 1) % BLOCKS;
            pthread_mutex_lock(&mutex);
            nBlock--;
            pthread_cond_signal(&noPieno);
        }else{
            pthread_cond_wait(&noVuoto, &critical);
        }
        pthread_mutex_unlock(&critical);
    }

   

    pthread_exit(NULL);
}

int main(int argc, char *argv[])
{
    FILE *origine, *destinazione;
    pthread_t lettura, scrittura;

    // Controllo parametri
    if (argc != 3)
    {
        printf("Uso: %s <file di origine> <file di destinazione>\n", argv[0]);
        return -1;
    }

    // Apertura file
    origine = fopen(argv[1], "r");
    if (origine == NULL)
    {
        printf("Impossibile aprire il file di origine\n");
        return -1;
    }
    destinazione = fopen(argv[2], "w");
    if (destinazione == NULL)
    {
        printf("Impossibile aprire il file di destinazione\n");
        fclose(origine);
        return -1;
    }

    // Creazione thread e sezioni critiche
    pthread_mutex_init(&critical, NULL);
    pthread_cond_init(&noPieno, NULL);
    pthread_cond_init(&noVuoto, NULL);

    pthread_create(&lettura, NULL, &Lettura, origine);
    pthread_create(&scrittura, NULL, &Scrittura, destinazione);
    pthread_join(lettura, NULL);
    pthread_join(scrittura, NULL);

    pthread_mutex_destroy(&critical);
    pthread_cond_destroy(&noPieno);
    pthread_cond_destroy(&noVuoto);

    // Chiusura file
    fclose(origine);
    fclose(destinazione);
    return 0;
}