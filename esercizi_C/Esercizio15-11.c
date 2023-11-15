#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
int fuori; // variabile che permette di gestire il padre e il figlio
int main(int argc, char *argv[])
{
    int dentro;
    // facciamo una fork per far clonare se stesso
    // la fork restituisce un valore intero
    printf("ciao\n");
    fuori = fork(); // Padre genera processo Figlio, p è diverso tra padre e figlio
    if (p != 0) // siamo nel Padre
    {
        printf("\nSono il padre, valore= %d, pid= %d", fuori, getpid()); // restituisce 6496 (è un numero) pid numero casuale
    }
    else // siamo nel figlio
    {
        printf("\nSono il figlio, valore= %d, pid= %d, mio padre ha il pid=%d \n", fuori, getpid(), getppid()); // restituisce 0 (è un numero) sempre ==valore padre, la pid del figlio; per sapere il pid del padre
    }
    return 0;
} //Il PID del processo padre è uguale al PID del processo figlio. Tuttavia il PID del nonno, del processo figlio, è il PID del processo che ha avviato il programma.
//la variabile intera, anche se viene dichiarata all'interno del main, svolge lo stesso ruolo ma non è visibile globalmente
//il messaggio "ciao" viene stampato due volte, una volta dal processo padre e una volta dal processo figlio. 
//quando viene fatto il fork(), il processo figlio eredita lo stato del processo padre, compreso il buffer di output. Quindi, il messaggio "ciao" nel buffer del processo padre viene anche ereditato dal processo figlio