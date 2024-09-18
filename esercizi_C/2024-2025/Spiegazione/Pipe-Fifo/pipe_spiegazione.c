#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/wait.h>
#define buffer_size 1024
//la pipe è un canale sincrono
int main(int argc, char *argv[])
{

    int p, n;
    // creazione
    int fd[2];                // descrittore
    char buffer[buffer_size]; // dichiarazioen
    if (pipe(fd) == -1)       //"tubo"/pipe creare prima del figlio
    {
        printf("Errore nella creazione della pipe\n");
        exit(-1);
    }

    // quando sei nel padre si chiude fd[0], nel figlio si chiude fd[1]
    p = fork();
    if (p < 0) // controllo creazione processo
    {
        printf("Errone nella creazione del processo");
    }

    if (p > 0) // padre fd[1] e chiudere fd[0](chiude uscita/lettura)
    {
        printf("Pid padre: %d, pid figlio: %d\n", getpid(), p);
        close(fd[0]);

        const char *messaggio = "Lattuga";
        if (write(fd[1], messaggio, strlen(messaggio)) == -1) // seguire l'ordine
        {
            printf("Errore nell'apertura della pipe\n");
            exit(-1);
        }
        else
        {
            close(fd[1]);
            wait(0);    //per aspettare la chiusura del figlio
        }
    }
    else // fliglio   fd[0] e chiudere fd[1] (chiude entrata/scrittura)
    {
        printf("Pid figlio: %d\n", getpid());
        close(fd[1]);
        n = read(fd[0], buffer, buffer_size);
        if (n == -1) // dire dimensione della read
        {
            printf("Errore lettura dalla pipe\n");
            exit(-1);
        }
        else
        {
            buffer[n] = '\0'; // carattere terminatore
            printf("Messaggio ricevuto: %s\n", buffer);

            close(fd[0]);
        }
    }
    return 0;
}