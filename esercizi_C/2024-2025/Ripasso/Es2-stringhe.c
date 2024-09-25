#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <string.h>
#include <fcntl.h>
#include <time.h>
/*
Scrivere un programma in C che dopo aver inserito una stringa a piacere determini e o verifichi:
1) Che contenga solo lettere;
2) Il conteggio di una lettera (se presente) scelta dall’utente;
3) Crei 2 ulteriori stringhe che contengano 1 le lettere di posizione pari; la 2° le lettere di posizione
dispari;
4) Verifichi se contiene doppie.

Inserita una 2° stringa determinare:
1) Quale delle 2 è più lunga e più corta;
2) Quali sono le lettere contenute in ambedue le stringhe;
3) Quale delle 2 stringhe contiene più vocali;
4) Quale delle 2 stringhe contiene più consonanti;
*/
#define DIM 100
void VerificaStringa(char str1[]){
    for (int i = 0; i < strlen(str1); i++)
    {
        if (str1[i]>=48||str1[i]<=57)
        {
            printf("Presenza di un numero\n");
            break;
        }
    }
}
void ConteggioLettera(char str1[],char lettera){
    int contatore=0;
    for (int i = 0; i <strlen(str1); i++)
    {
        if (str1[i]==lettera)
        {
            contatore++;
        }
        
    }
    printf("La lettera è presente %d volte",contatore);
}
void ConteggioLettera(char str1[],char strPari[],char strDispari[]){
    for (int i = 0; i <strlen(str1); i++)
    {
        
    }
    
}

int main(int argc, char *argv[]) {

    char str1[DIM];
    char lettera;
    printf("Inserisci una stringa\n");
    scanf("%s",str1);
    VerificaStringa(str1);
    printf("Che lettera vuoi contare\n");
    scanf(" %c",&lettera);  //lo spazio serve per evitare lo \n
    ConteggioLettera(str1,lettera);
    //2 stringhe, pari e dispari
    char strPari[DIM];
    char strDispari[DIM];
    PariDispari(str1,strPari,strDispari);

    return 0;
}