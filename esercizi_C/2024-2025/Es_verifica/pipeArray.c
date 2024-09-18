#include <fcntl.h> 
#include <errno.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <sys/wait.h>
#define size_array 5
//fare un array da 5 numeri inseriti dal figlio e il padre li somma tutti
int main(int argc, char *argv[])
{
    int pd[2];  //per la pipe
    int p;  //per la fork
    int numeri[size_array];
    if (pipe(pd) == -1) {
        printf("Errore creazione pipe");
        return 1;
    }

    p=fork();
    if (p==-1)  
    {
        printf("Errore creazione fork");
        return 1;
    }
    if (p>0)   //padre 
    {
        
        close(pd[1]);
        
        if (read(pd[0],&numeri,sizeof(numeri))==-1)
        {
            printf("Errore lettura nella pipe\n");
            exit(-1);
        }
        printf("Lettura numeri\n");
        for (int i = 0; i < size_array; i++)
        {
            printf("Numero %d: %d\n",i,numeri[i]);
        }
        close(pd[0]);
    }
    else    //figlio
    {
        close(pd[0]);
        
        printf("Inserisci 5 numeri\n");
        for (int i = 0; i < size_array; i++)
        {
            printf("Numero %d:\n",i);
            scanf("%d",&numeri[i]);
        }
        if (write(pd[1],&numeri,sizeof(numeri))==-1)
        {
            printf("Errore scrittura nella pipe\n");
            exit(-1);
        }
        
        
        close(pd[1]);
        
        
    }
    
    return 0;
}