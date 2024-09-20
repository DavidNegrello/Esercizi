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

#define DIM 30
void Menu()
{
    printf("\nMenu:\n");
    printf("[1]visualizza\n");
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

void Generazione(int array[], int dimensioneVettore)
{
    srand(time(NULL));
    for (int i = 0; i < dimensioneVettore; i++)
    {
        array[i] = rand() % 20;
    }
}
void Visualizza(int array[], int dimensione)
{
    for (int i = 0; i < dimensione; i++)
    {
        printf("%d - ", array[i]);
    }
}
void VisualizzaInvertito(int array[], int dimensione)
{
    int arrayInv[dimensione];
    for (int i = 0; i < dimensione; i++)
    {
        arrayInv[i] = array[dimensione - i - 1];
    }

    for (int i = 0; i < dimensione; i++)
    {
        printf("%d ", arrayInv[i]);
    }
    printf("\n");
}
void SommaMedia(int array[], int dimensione, int somma, float media)
{
    for (int i = 0; i < dimensione; i++)
    {
        somma += array[i];          // somma
        media = somma / dimensione; // media utilizzando la somma calcolata in precedenza e la dimensione dell'array per sapere quanti numeri ci sono nell'array
    }
    printf("Somma di tutti i numeri: %d \n", somma);
    printf("Media di tutti i numeri: %.2f \n", media);
}
void NumeriPari(int array[], int dimensione)
{
    printf("Numeri pari: ");
    for (int i = 0; i < dimensione; i++)
    {
        if (array[i] % 2 == 0)
        {
            printf("%d - ", array[i]);
        }
    }
}
void NumeriDispari(int array[], int dimensione)
{
    printf("Numeri dispari: ");
    for (int i = 0; i < dimensione; i++)
    {
        if (array[i] % 2 != 0)
        {
            printf("%d - ", array[i]);
        }
    }
}
void Ricerca(int array[], int dimensione, int numeroScelto)
{
    printf("Numero da ricercare: ");
    scanf("%d",&numeroScelto);
    for (int i = 0; i < dimensione; i++)
    {
        if (numeroScelto == array[i])
        {
            printf("numero %d in posizione: %d", array[i], i);
        }
    }
}
void Elimina(int array[], int dimensione, int numeroScelto)
{
    Ricerca(array, dimensione, numeroScelto);
    if (numeroScelto != -1)
    {
        for (int i = numeroScelto; i < dimensione - 1; i++)
        {
            array[i] = array[i + 1];
        }
        dimensione--;
    }
    Visualizza(array,dimensione);
}
void Alterna(int array[], int dimensione){
    for (int i = 0; i < dimensione - 1; i += 2) {
        int temp = array[i];
        array[i] = array[i + 1];
        array[i + 1] = temp;
    }
    Visualizza(array,dimensione);
}
void Ordina(int array[], int dimensione){
    for (int i = 0; i < dimensione - 1; i++) {
        for (int j = i + 1; j < dimensione; j++) {
            if (array[i] > array[j]) {
                int swap = array[i];
                array[i] = array[j];
                array[j] = swap;
            }
        }
    }
    Visualizza(array,dimensione);
}

int main(int argc, char *argv[])
{
    int scelta, bool = 0;
    int dimensioneVettore, numeroScelto;
    int somma = 0;
    float media = 0;
    printf("Inserisci la grandezza del vettore\n");
    scanf("%d", &dimensioneVettore);
    int vettore[dimensioneVettore];
    Generazione(vettore, dimensioneVettore);
    do
    {
        Menu(); // visualizza il menu
        printf("Scegli cosa fare\n");
        scanf("%d", &scelta);
        switch (scelta)
        {
        case 1:
            Visualizza(vettore, dimensioneVettore);
            bool = 1; // per poter eliminare un numero
            break;
        case 2:
            VisualizzaInvertito(vettore, dimensioneVettore);
            break;
        case 3:
            SommaMedia(vettore, dimensioneVettore, somma, media);
            break;
        case 4:
            NumeriPari(vettore, dimensioneVettore);
            break;
        case 5:
            NumeriDispari(vettore, dimensioneVettore);
            break;
        case 6:
            printf("Numero da ricercare: ");
            scanf("%d", &numeroScelto);
            Ricerca(vettore, dimensioneVettore, numeroScelto);
            break;
        case 7:
            if (bool == 1)
            {
                printf("Numero da eliminare: ");
                scanf("%d", &numeroScelto);
                Elimina(vettore, dimensioneVettore,numeroScelto);
            }
            else
            {
                printf("Prima visualizza i numeri all'interno dell'array");
            }
            break;
        case 8:
                Alterna(vettore, dimensioneVettore);
            break;
        case 9:
                Ordina(vettore, dimensioneVettore);
            break;
        case 10:
                return 0;
            default:
                printf("Scelta non valida\n");
        }

    } while (scelta != 10);

    return 0;
}