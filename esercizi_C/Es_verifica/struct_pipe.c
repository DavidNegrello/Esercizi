#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/wait.h>
#define NUMERO 2
int main(int argc, char *argv[])
{
    typedef struct
    {
        char nome[20];
        char cognome[20];
        float media;
    } Studente;
    int p;
    int fd[2];
    if (pipe(fd) < 0)
    {
        printf("Errore pipe \n");
        exit(-1);
    }
    p = fork();
    if (p < 0)
    {
        printf("ERRORE creazione fork \n");
        exit(-1);
    }
    if (p == 0) // filgio
    {
        Studente array[NUMERO];
        close(fd[1]);
        read(fd[0], array, sizeof(array));
        for (int i = 0; i < NUMERO; i++)
        {
            printf("nome %s  \n", array[i].nome);
            printf("cognome %s \n", array[i].cognome);
            printf("media %.2f \n", array[i].media);
        }
        close(fd[0]);
    }
    else
    { // padre
        Studente array[NUMERO];
        close(fd[0]);
        for (int i = 0; i < NUMERO; i++)
        {
            printf("Inserire nome \n");
            scanf("%s", array[i].nome);
            printf("Inserire cognome \n");
            scanf("%s", array[i].cognome);
            printf("Inserire media \n");
            scanf("%f", &array[i].media);
        }
        write(fd[1], array, sizeof(array));
        close(fd[1]);
    }
    return 0;
}