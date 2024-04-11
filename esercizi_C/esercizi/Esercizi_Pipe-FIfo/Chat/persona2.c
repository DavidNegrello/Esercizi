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
    fifo_r = open("polesella", O_RDONLY);
    fifo_w = open("polesella2", O_WRONLY);
    //printf("Persona 2, inserisci una frase: \n");
    //scanf("%s",frase_scritta);
    do
    {
        if (read(fifo_r, frase_letta, sizeof(frase_letta) == -1))
        {
            printf("Errore scrittura nella fifo\n");
            return 2;
        }
        printf("Persona 1: %s\n",frase_letta);

        printf("Persona 2, inserisci una frase: \n");
        scanf("%s",frase_scritta);
        
        if (write(fifo_w, frase_scritta, sizeof(frase_scritta)) == -1)
        {
            printf("Errore scrittura nella fifo\n");
            return 2;
        }
    } while (strcmp(frase_scritta,"halt"));
    
    


    printf("Scittura eseguita nella fifo\n");
    close(fifo_w);
    close(fifo_r);

    return 0;
}