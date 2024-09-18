#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>

#define Dim 64
#define Slice_Dim 16
typedef struct
{
    unsigned char bufferDentroStruct[Slice_Dim];
    int n;
} bufferStruct;

bufferStruct buffer[Slice_Dim];
int wrtieIndex; // per tenere traccia di dove è arrivato a scrivere
int readIndex;  // per tenere traccia di dove è arrivato a leggere
int sliceLetti; // è una sezione critica perchè viene messa in comune a entrambi i thread
pthread_mutex_t mutex;
pthread_cond_t notFull;
void *WriteFile(void *arg)
{
    FILE *fileorigine = (FILE *)arg;
    pthread_mutex_lock(&mutex);
    if (sliceLetti > 0)
    {
        int n = fwrite(buffer[readIndex].bufferDentroStruct, 1, buffer[readIndex].n, fileorigine); // non leggerò più il DIM ma quello che c'è di disponibile
        readIndex = (readIndex++) % Slice_Dim;                                                     // ovvero quando arriva a 16 riparte da 0 e non continua
        sliceLetti--;                                                                              // se è 16 significa che è pieno, 0 vuoto
        pthread_cond_wait(&notFull, &mutex);
    }
    pthread_mutex_unlock(&mutex);
}

void *ReadFile(void *arg)
{ // legge da file e scrive su buffer
    FILE *fileorigine = (FILE *)arg;
    pthread_mutex_lock(&mutex);
    if (sliceLetti >= Slice_Dim)
    {
        pthread_cond_wait(&notFull, &mutex);
    }

    int n = fread(buffer[wrtieIndex].bufferDentroStruct, 1, Dim, fileorigine); // variabile locale n
    if (n > 0)
    {
        buffer[wrtieIndex].n = n;                // va prima a scrivere e dopo sposta altrimenti va a spostare il puntatore (writeIndex)
        wrtieIndex = (wrtieIndex++) % Slice_Dim; // ovvero quando arriva a 16 riparte da 0 e non continua
        sliceLetti++;                            // altrimenti non si sa quanto ha letto
    }
    pthread_mutex_unlock(&mutex);
}
int main(int argc, char *argv[])
{
    FILE *fileRead, *fileWrite;
    pthread_t writeT, readT;
    pthread_create(&writeT, NULL, WriteFile, fileWrite);
    pthread_create(&readT, NULL, ReadFile, fileRead);
    pthread_join(writeT, NULL);
    pthread_join(readT, NULL);
    return 0;
}