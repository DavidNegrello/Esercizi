#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <string.h>
#include <fcntl.h>
#include <pthread.h>

void* StampaNino(void* par) // primo thread che sto creando
{
    printf("Nino Pavan\n");
}
void* StampaMatteo(void* par)
{
    printf("Matteo Bensfulli\n");
}
void* StampaLuciano(void* par)
{
    printf("Luciano Castello\n");
}
int main(int argc, char *argv[])
{
    pthread_t threadNino, threadMatteo, threadLuciano;
    printf("Polesella\n");
    pthread_create(&threadNino, NULL, &StampaNino, NULL); // indirizzo della variabile pthread che si vuole usare, configurazioni per gestire le sue funzioni, quello che voglio fare in parallelo, qualcosa che si può passare alle funzioni
    pthread_create(&threadMatteo, NULL, &StampaMatteo, NULL);
    pthread_create(&threadLuciano, NULL, &StampaLuciano, NULL);
    pthread_join(threadNino,NULL);
    pthread_join(threadMatteo,NULL);
    pthread_join(threadLuciano,NULL);

    

    return 0;
}