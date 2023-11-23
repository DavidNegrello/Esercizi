#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
int p;
int main(int argc, char *argv[])
{
    p = getpid();
    printf("Sono il processo padre; il mio pid: %d",p);
    for (int i = 0; i < 2; i++)
    {
        if (p != 0)
        {
            p=fork();   //ripete il ciclo 2 volte e genera 2 figli
            if (p == 0)
            {
                printf("");
            }
        }
    }

    return 0;
}