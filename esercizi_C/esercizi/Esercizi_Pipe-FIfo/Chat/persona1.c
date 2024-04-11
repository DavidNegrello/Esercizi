#include <fcntl.h>
#include <errno.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>


int main(int argc, char *argv[])
{
    int fifo_w;
    int fifo_r;
    char frase_scritta[1024];
    char frase_letta[1024];
    fifo_w = open("polesella", O_WRONLY);
    fifo_r = open("polesella2", O_RDONLY);
    
    printf("Persona 1, inserisci una frase: \n");
    scanf("%s",frase_scritta);
    do
    {
        if (write(fifo_w, frase_scritta, sizeof(frase_scritta)) == -1)
        {
            printf("Errore scrittura nella fifo\n");
            return 2;
        }
         if (read(fifo_r, frase_letta, sizeof(frase_letta) == -1))
        {
            printf("Errore scrittura nella fifo\n");
            return 2;
        }
        printf("Persona 2: %s\n",frase_letta);

        printf("Persona 1, inserisci una frase: \n");
        scanf("%s",frase_scritta);
    } while (strcmp(frase_scritta,"halt"));

    close(fifo_w);
    close(fifo_r);

    return 0;
}