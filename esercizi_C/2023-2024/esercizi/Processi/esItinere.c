#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#define MAX 9999
// il numero inserito dall'utente deve essere fatti da comando (./ripasso numero)ù
int p;
int main(int argc, char *argv[])
{
    int vett[MAX];

    FILE *scrittura;
    int n;
    p = fork();
    if (p == 0)
    {
        // filgio A
    }
    else
    {
        p = fork();
        if (p == 0)
        {
            // figlio B
        }
        else
        {
            // padre
            scrittura = fopen(argv[1], "w");
            if (scrittura == NULL)
            {
                printf("Errore apertura file\n");
                exit(1);
            }
            for (int i = 0; i <= MAX; i++) // per generare i numeri casuali
            {
                vett[i] = ("%d", rand() % 501);
                fputs(vett, scrittura); 
                fputc('\n', scrittura); // aggiunge il carattere
            }
           
            
        }
    }
    fclose(scrittura);
    return 0;
}