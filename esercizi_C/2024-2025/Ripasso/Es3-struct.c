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
Scrivere un programma in C che dopo aver dichiarato una struttura denominata Auto con i seguenti
campi: ModelloAuto, marca, cilindrata, prezzo, anno_immatricolazione, determinare:
1) Il prezzo + alto e quello + basso visualizzando anche il modello dell'auto;
2) Ordinare la lista delle auto in base all'anno d'immatricolazione;
3) Visualizzare il nome delle auto a partire da un prezzo scelto dall'utente.
*/
#define DIM 100
#define lista 3
typedef struct
{
    char modelloAuto[DIM];
    char marca[DIM];
    int cilindrata;
    float prezzo;
    int annoImmatricolazione;
} Auto;

void TrovaPrezzo(Auto listaAuto[])
{
    int prezzoMax=listaAuto[0].prezzo;
    int prezzoMin=listaAuto[0].prezzo;

    char modelloMax[DIM];
    char modelloMin[DIM];

    strcpy(modelloMax, listaAuto[0].modelloAuto);
    strcpy(modelloMin, listaAuto[0].modelloAuto);

    for (int i = 1; i < lista; i++)
    {
        if (prezzoMax < listaAuto[i].prezzo)
        {
            prezzoMax = listaAuto[i].prezzo;
            strcpy(modelloMax, listaAuto[i].modelloAuto);
        }
        else if (prezzoMin > listaAuto[i].prezzo)
        {
            prezzoMin = listaAuto[i].prezzo;
            strcpy(modelloMin, listaAuto[i].modelloAuto);
        }
    }

    printf("La macchina %s ha il prezzo più alto: %d \n", modelloMax, prezzoMax);
    printf("La macchina %s ha il prezzo più basso: %d \n", modelloMin, prezzoMin);
    
}


void OrdinaLista(Auto listaAuto[],Auto listaOrdinata[]){
    int annoMax=listaAuto[0].annoImmatricolazione;
    int annoMin=listaAuto[0].annoImmatricolazione;

     for (int i = 1; i < lista; i++)
    {
        if (annoMax < listaAuto[i].annoImmatricolazione)
        {
            annoMax = listaAuto[i].annoImmatricolazione;
            listaOrdinata[i]=listaAuto[i];
        }
        else if (annoMin > listaAuto[i].annoImmatricolazione)
        {
            annoMin = listaAuto[i].annoImmatricolazione;
            listaOrdinata[i]=listaAuto[i];
        }
    }
    //stampa
    printf("Lista ordinata: \n");
    for (int i = 0; i <lista; i++)
    {
        printf("Macchina n° %d\n",i+1);
        printf("Modello: %s \n", listaOrdinata[i].modelloAuto);
        printf("Marca: %s \n", listaOrdinata[i].marca);
        printf("Anno: %d \n", listaOrdinata[i].annoImmatricolazione);
        printf("Prezzo: %f \n", listaOrdinata[i].prezzo);
        printf("Cilindrata: %d \n", listaOrdinata[i].cilindrata);
    }
    
}


int main(int argc, char *argv[])
{
    Auto listaAuto[lista] = {
        {"Ferrari", "LaFerrari", 5000, 100000, 2015},
        {"Audi", "A6", 2000, 60000, 2016},
        {"Fiat", "Panda", 1000, 20000, 2009}};
    Auto listaOrdinata[lista];
    /*
    for (int i = 0; i < lista; i++)
    {
        printf("Inserisci il modello dell'auto n° %d\n",i+1);
        scanf(" %s",listaAuto[i].modelloAuto);
        printf("Inserisci la marca\n");
        scanf(" %s",listaAuto[i].marca);
        printf("Inserisci la cilindrata\n");
        scanf(" %d",&listaAuto[i].cilindrata);
        printf("Inserisci il prezzo\n");
        scanf(" %f",&listaAuto[i].prezzo);
        printf("Inserisci l'anno d'immatricolazione\n");
        scanf(" %d",&listaAuto[i].annoImmatricolazione);
    }
    */
    TrovaPrezzo(listaAuto);
    OrdinaLista(listaAuto,listaOrdinata);
    return 0;
}