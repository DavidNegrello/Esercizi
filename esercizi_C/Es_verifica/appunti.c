#include <fcntl.h>
#include <errno.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <sys/wait.h>
int main(int argc, char *argv[])
{
    /*  STRUCT CON PIPE
    typedef struct
    {
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
        close(fd[1]);
        read(fd[0], , );
        close(fd[0]);
    }
    else
    { // padre
        close(fd[0]);
        write(fd[1], , );
        close(fd[1]);
    } */

    /*  CONSUMATORE E FILE
    int fd;
    int n;
    FILE *destinazione;
    unsigned char buffer[BUFF_SIZE];
    destinazione = fopen("destinazione.txt", "w");
    if (destinazione == NULL)
    {
        printf("Errore apertura file \n");
        exit(-1);
    }
    fd = open("pipe", O_RDONLY);
    while ((n = read(fd, buffer, sizeof(buffer))) > 0)
    {
        fwrite(buffer, 1, sizeof(buffer), destinazione);
    }
    close(fd);
    fclose(destinazione);
    printf("Fine programma \n");
    */

    /*  PRODUTTORE E FILE
    FILE *origine;
    unsigned char buffer[BUFF_SIZE];
    int fd;
    if (argc != 2)
    {
        printf("numero argomenti sbagliati \n");
        exit(-1);
    }
    origine = fopen(argv[1], "r");
    if (origine == NULL)
    {
        printf("Il file inserito non esiste \n");
        exit(-1);
    }
    fd = open("pipe", O_WRONLY);
    if (fd < 0)
    {
        printf("Errore apertura fifo \n");
        exit(-1);
    }
    while (!feof(origine))
    {
        fread(buffer, 1, BUFF_SIZE, origine);
        write(fd, buffer, sizeof(buffer));
    }
    close(fd);
    fclose(origine);*/
    return 0;
}