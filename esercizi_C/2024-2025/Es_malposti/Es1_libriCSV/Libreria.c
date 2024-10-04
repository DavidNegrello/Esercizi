#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <errno.h>
#include <string.h>
#include <fcntl.h>
#include <time.h>

#define DIM 100
#define Lista 39
#define buffer_dim 1024

FILE *sorgente;
char buffer[buffer_dim];
//=================STRUCT===============
typedef struct
{
    char categoria[DIM];
    char titolo[DIM];
    char autore[DIM];
    int anno;
    float prezzo;
} Libreria;

//=================FUNZIONI================

int SalvaLibri(Libreria listaLibri[], int contatore)
{
    while (fgets(buffer, sizeof(buffer), sorgente) != NULL)
    {
        sscanf(buffer, "%99[^,],%99[^,],%f,%d", listaLibri[contatore].titolo, listaLibri[contatore].autore, &listaLibri[contatore].prezzo, &listaLibri[contatore].anno);
        // Imposta un valore predefinito per la categoria
        strcpy(listaLibri[contatore].categoria, "default");
        contatore++;
    }
    return contatore;
}
void Menu(){
    
}

//=================MAIN================
int main(int argc, char *argv[])
{
    Libreria listaLibri[Lista];
    int contatore = 0;
    //==========FILE==========
    sorgente = fopen("libreria_libri.csv", "r");
    if (sorgente == NULL)
    {
        printf("Errore apertura file");
        exit(1);
    }
    contatore = SalvaLibri(listaLibri, contatore);

    //==========Menu==========
    Menu();
    // Stampa i dati letti
    for (int i = 0; i < contatore; i++)
    {
        printf("%s, %s, %.2f, %d, %s\n",
               listaLibri[i].titolo,
               listaLibri[i].autore,
               listaLibri[i].prezzo,
               listaLibri[i].anno,
               listaLibri[i].categoria);
    }
    fclose(sorgente);
    return 0;
}
//==========FINE FILE==========
