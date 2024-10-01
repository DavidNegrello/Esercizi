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
void VerificaStringa(char str1[])
{
    for (int i = 0; i < strlen(str1); i++)
    {
        if (str1[i] >= 48 || str1[i] <= 57)
        {
            printf("Presenza di un numero\n");
            break;
        }
    }
}
void ConteggioLettera(char str1[], char lettera)
{
    int contatore = 0;
    for (int i = 0; i < strlen(str1); i++)
    {
        if (str1[i] == lettera)
        {
            contatore++;
        }
    }
    printf("La lettera è presente %d volte", contatore);
}
void ConteggioLettera(char str1[], char strPari[], char strDispari[])
{
    for (int i = 0; i < strlen(str1); i++)
    {
        if (strlen(str1) % 2 == 0)
        {
            strPari[i] = str1[i];
        }
        else
        {
            strDispari[i] = str1[i];
        }
    }
}
void Doppie(char str1[]){
    for (int i = 0; i < strlen(str1); i++){
        for (int j = i + 1; j < strlen(str1); j++){
            if (str1[i] == str1[j]){
                printf("La lettera %c è presente due volte\n", str1[i]);
            }
        }
    }
}
void LungaLunga(char str2[], char str1[])
{
    if (strlen(str1) > strlen(str2))
    {
        printf("La prima stringa è più lunga");
    }
    else if (strlen(str1) < strlen(str2))
    {
        printf("La seconda stringa è più lunga");
    }
    else
    {
        printf("Le due stringhe hanno la stessa lunghezza");
    }
}

int main(int argc, char *argv[])
{

    char str1[DIM];
    char str2[DIM];
    char lettera;
    printf("Inserisci una stringa\n");
    scanf("%s", str1);
    VerificaStringa(str1);
    printf("Che lettera vuoi contare\n");
    scanf(" %c", &lettera); // lo spazio serve per evitare lo \n
    ConteggioLettera(str1, lettera);
    // 2 stringhe, pari e dispari
    char strPari[DIM];
    char strDispari[DIM];
    PariDispari(str1, strPari, strDispari);
    //nuova stringa
    printf("Inserisci la nuova stringa\n");
    scanf("%s", str2);
    LungaLunga(str2, str1);
    return 0;
}