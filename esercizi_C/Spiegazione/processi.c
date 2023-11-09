// dimostriamo che: p Padre=p Figlio
// p Figlio=0
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
int p; // variabile che permette di gestire il padre e il figlio
int main(int argc, char *argv[])
{
    // facciamo una fork per far clonare se stesso
    // la fork restituisce un valore intero
    p = fork(); // Padre genera processo Figlio, p è diverso tra padre e figlio
    if (p != 0) // siamo nel Padre
    {
        printf("\nSono il padre, valore= %d, pid= %d", p, getpid()); // restituisce 6496 (è un numero) pid numero casuale
    }
    else // siamo nel figlio
    {
        printf("\nSono il figlio, valore= %d, pid= %d, mio padre ha il pid=%d \n", p, getpid(), getppid()); // restituisce 0 (è un numero) sempre ==valore padre, la pid del figlio; per sapere il pid del padre
    }
    return 0;
}