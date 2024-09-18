#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <string.h>
#include <fcntl.h>

#define SIZE 1024
// #define program "./consumatore.exe"
int spawn(char program[], char *argument[])
{
    int p;
    p=fork();
    if (p<0)
    {
        return -1;
    }else if (p>0)  //padre
    {
        printf("Sono il padre e mio figlio ha il pid: %d\n",p);
        return 0;
    }
    if (p==0)
    {
        printf("Sono il figlio con pid: %d",getpid());
        execv(program,argument);
    }
   
    printf("Presenza di un errore nella exec");
    abort();
}

int main(int argc, char *argv[])
{
    int fifo;
    int n;
    char *arg[3];
    unsigned char buffer[SIZE];
    FILE *log;
    
    printf("Sono il produttore\n");
    if (arg != 3)
    {
        return 0;
    }
    arg[0] = (char *)malloc(strlen("./consumatore.exe") + 1);   
    strcpy(arg[0], "./consumatore.exe");
    arg[1] = (char *)malloc(strlen(argv[2]) + 1);
    strcpy(arg[1], argv[2]);
    arg[2] = NULL; // nell'exec l'ultimo deve essere NULL
    if (spawn("./consumatore.exe",arg)<0)
    {
        free(arg[0]);
        free(arg[1]);
        return 0;
    }
    if (mkfifo("polesella_city",0777)==-1)
    {
        if (errno!=EEXIST)
        {
            printf("Fifo già esistente");
            return 0;
        }
        
    }
    fifo=open("polesella_city",O_WRONLY);
    if (fifo<0)
    {
        printf("Errore creazione fifo\n");
        free(arg[0]);   //libera quello che c'è nell'oip 
        free(arg[1]);
        return 0;
    }
    if (log = fopen(argv[1], "wr") == NULL)
    {
        printf("Errore nell'apertura del file");
        fclose(log);
        exit(1);
    }
    return 0;
}