#include <fcntl.h>
#include <errno.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>

typedef struct
{
    char nome[30];
    char cognome[30];
    int eta;
} Studente;
int main(int argc, char *argv[])
{
    int pd[2];
    int p;            // per la fork
    Studente alunno;  // per la struct
    if (pipe(pd) < 0) // controllo creazione pipe
    {
        printf("Errore nella creazione della pipe \n");
        exit(-1);
    }
    p = fork();
    if (p < 0) // controllo creazione fork
    {
        printf("Errore nella creazione della fork \n");
        exit(-1);
    }

    if (p == 0) // figlio
    {
        close(pd[1]);
        if (read(pd[0], &alunno, sizeof(alunno)) == -1)
        {
            printf("Errore nella lettura dalla pipe\n");
            return 1;
        }
        printf("Nome: %s\n", alunno.nome);
        printf("Congome: %s\n", alunno.cognome);
        printf("Età: %d\n", alunno.eta);
        
        close(pd[0]);
    }
    else // padre
    {
        close(pd[0]);
        printf("Inserire il nome\n");
        scanf("%s", alunno.nome);
        printf("Inserire il cognome\n");
        scanf("%s", alunno.cognome);
        printf("Inserire l'età\n");
        scanf("%d", &alunno.eta);
        if (write(pd[1], &alunno, sizeof(alunno)) == -1)
        {
            printf("Errore nella scrittura nella pipe\n");
            return 1;
        }
        execl("./P2.exe", "P2.exe", alunno.nome,alunno.cognome,alunno.eta, NULL);   //non viene preso alunno.eta
        close(pd[1]);
    }

    return 0;
}