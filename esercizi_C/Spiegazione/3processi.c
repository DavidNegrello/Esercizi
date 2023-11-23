#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
int p;
int main(int argc, char *argv[])
{
    p = fork();
    if (p!=0)
    {
        //padre
        printf("Sono il processo padre; il mio pid: %d\n",getpid());
        p=fork();
        if (p==0)
        {
            //figlio "quo"
            printf("Sono il processo figlio quo; il mio pid: %d\n",getpid());
        }
        else{
            p=fork();
            if (p==0)
            {
                //figlio "qua"
                printf("Sono il processo figlio qua; il mio pid: %d\n",getpid());
            }
            
        }
    }
    else{
        //figlio "qui"
        printf("Sono il processo figlio qui; il mio pid: %d\n",getpid());
    }
    return 0;
}