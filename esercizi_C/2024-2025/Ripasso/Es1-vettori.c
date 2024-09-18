#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <string.h>
#include <fcntl.h>

#define SIZE 1024
/*
Scrivere un programma in C che effettui, attraverso un menù, le seguenti operazione sugli array:
1) Visualizzi a video gli elementi dell’array inseriti o generati casualmente;
2) Visualizzi a video l’array invertito cioè sostituendo il primo elemento con l ultimo , il secondo con il
penultimo e cosi via;
3) Visualizzi a video la somma e media degli elementi del vettore;
4) Visualizzi a video tutti i numeri pari;
5) Visualizzi a video tutti i numeri dispari;
6) Ricerchi un numero (letto in input) nell’array;
7) Elimini un elemento (letto in input) dell’array (se esistente);
8) Alterni (o scambi ) a due a due le posizioni del vettore: esempio
vettore: 1,2,3,4 vettore alternato: 2,1,4,3 (attenzione se lungo dispari);
9) Ordini il vettore (ordinamento a scelta).
*/
void Menu()
{
    printf("Menu\n");
    printf("[1]Genera vettore e visualizza\n");
    printf("[2]Vettore invertito\n");
    printf("[3]Somma e media\n");
    printf("[4]Numeri pari\n");
    printf("[5]Numeri dispari\n");
    printf("[6]Ricerca\n");
    printf("[7]Elimina\n");
    printf("[8]Alterna a due posizioni\n");
    printf("[9]Ordina crescente\n");
    printf("[10]Esci\n");
}

int Generazione(int dimensioneVettore)
{
    int *vettore[dimensioneVettore];
    for (int i = 0; i <= dimensioneVettore; i++)
    {
        vettore[i] = rand() % 20;
    }
    for (int i = 0; i <=dimensioneVettore; i++)
    {
        printf("%d\n",vettore[i]);
    }
    return vettore;
}

int main(int argc, char *argv[])
{
    int scelta;
    int dimensioneVettore=0;
    do
    {
        Menu(); //visualizza il menu
        scanf("%d\n", &scelta);
        
        switch (scelta)
        {
        case 1:
            printf("Inserisci la grandezza del vettore\n");
            scanf("%d\n", &dimensioneVettore);
            int *vettore=Generazione(dimensioneVettore);;
            
            break;
            case 2:
            break;
        }


    } while (scelta);

    return 0;
}