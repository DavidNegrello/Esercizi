#include <fcntl.h> //per usare le fifo (per aprirla)
#include <errno.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
int main(int argc, char *argv[])
{
    int fd;
    // int x=90;   //non bisogna dichiararla qua ma dopo l'apertura in scrittura della fifo

    if (mkfifo("Polesella", 0777) == -1) // nome e tutti i permessi
    {                                    // può esserci l'errore anche se esite già
        if (errno != EEXIST)             // per controllare se esiste già
        {
            printf("Errore creazione fifo\n");
            return 1; // se qualcosa è sbagliato
        }
    }
    // apro la fifo in scrittura
    fd = open("Polesella", O_WRONLY); // fd=1 poichè ho aperto la fifo in scrittura
    printf("La fifo è stata aperta correttamente\n");
    int x = 90;
    if (write(fd, &x, sizeof(x)) == -1) // indirizzo di memoria di x
    {
        printf("Errore scrittura nella fifo\n");
        return 2; // è già presente il return 1
    }
    printf("Scittura eseguita nella fifo\n");
    close(fd);
    return 0; // se tutto è corretto
}