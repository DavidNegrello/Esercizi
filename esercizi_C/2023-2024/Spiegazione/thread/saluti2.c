#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <string.h>
#include <fcntl.h>
#include <pthread.h>

void* StampaPersone(void* par) 
{
    printf("%s\n",(char*)par);
    return (void*)0;
}

int main(int argc, char *argv[])
{
    pthread_t threadNino, threadMatteo, threadLuciano;
    char* msg1="Nino Pavan";
    char* msg2="Matteo Bensfulli";
    char* msg3="Luciano Castello";
    printf("Polesella\n");
    pthread_create(&threadNino, NULL, &StampaPersone, msg1); 
    pthread_create(&threadMatteo, NULL, &StampaPersone, msg2);
    pthread_create(&threadLuciano, NULL, &StampaPersone, msg3);
    pthread_join(threadNino,NULL);
    pthread_join(threadMatteo,NULL);
    pthread_join(threadLuciano,NULL);

    

    return 0;
}