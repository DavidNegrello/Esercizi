#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
int global1;
int global2;
int main(int argc, char *argv[])
{
    int a;
    while (1)
    {
        printf("%d\n",getpid());    //identificatore del processo
        printf("%p\n",&a);  //indirizzo di memoria
        sleep(1);
    }
    
    //printf("Cocco\n");
    //printf("Lezione sul layout di memoria\n");
    return 0;
}