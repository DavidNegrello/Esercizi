#include <fcntl.h>
#include <errno.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>

#define maxVettore 5
int main(int argc, char *argv[])
{

    int vettore[maxVettore];
    int fifo;
    int somma=0;
    fifo = open("Sium", O_RDONLY);
    printf("La fifo è stata aperta correttamente\n");
    for (int i = 0; i < maxVettore; i++)
    {
        if (read(fifo, &vettore[i], sizeof(vettore[i])) == -1)
        {
            printf("Errore scrittura nella fifo\n");
            return 2;
        }
        somma=somma+vettore[i];
    }
    printf("Somma: %d\n",somma);
    printf("Lettura eseguita nella fifo\n");
    close(fifo);

    return 0;
}