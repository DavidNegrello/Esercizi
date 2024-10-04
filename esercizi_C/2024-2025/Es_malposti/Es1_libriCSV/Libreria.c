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
FILE *destinazione;
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
void Menu()
{
    printf("==========MENU==========\n");
    printf("1. Visualizza la lista dei libri\n");
    printf("2. Modifica lista con inserimento categorie\n");
    printf("3. Visualizza per categoria\n");
    printf("4. Esci\n");
}
// Funzione per salvare i dati aggiornati in un file
void ScriviSuFile(Libreria listaLibri[], int contatore)
{

    for (int i = 0; i < contatore; i++)
    {
        // Scrivi ogni libro con le categorie aggiornate
        fprintf(destinazione, "%s,%s,%.2f,%d,%s\n",
                listaLibri[i].titolo,
                listaLibri[i].autore,
                listaLibri[i].prezzo,
                listaLibri[i].anno,
                listaLibri[i].categoria);
    }
}
int ControlloScelta(int scelta, int contatore, Libreria listaLibri[])
{
    // char Categorie[buffer_dim]; // per salvare i dati delle modifiche
    char categoriaModifica[DIM];
    char sceltaCategoria[DIM];
    switch (scelta)
    {
    case 1:
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
        break;
    case 2: // canbia categoria
        for (int i = 0; i < contatore; i++)
        {
            printf("Cambia la categoria del libro %s \n", listaLibri[i].titolo);
            scanf(" %s", categoriaModifica);
            strcpy(listaLibri[i].categoria,categoriaModifica);

            // strcat(categoriaModifica, "\n");         // Aggiungi una nuova riga alla fine
            // strcpy(Categorie[i], categoriaModifica); // Copia la stringa con il newline nell'array
            
            // Dopo la modifica, salva su file
            ScriviSuFile(listaLibri, contatore);
        }
        break;
    case 3:
        printf("Scegli una categoria:\n");
        scanf(" %s", sceltaCategoria);
        for (int i = 0; i < contatore; i++)
        {
            if (strcmp(sceltaCategoria, listaLibri[i].categoria) == 0)
            {
                printf("%d %s\n", i + 1, listaLibri[i].categoria);
            }
        }
        break;
    }
    return scelta;
}

//=================MAIN================
int main(int argc, char *argv[])
{
    Libreria listaLibri[Lista];
    int contatore = 0;
    int scelta;
    //==========FILE==========
    sorgente = fopen("libreria_libri.csv", "r");
    if (sorgente == NULL)
    {
        printf("Errore apertura file");
        exit(1);
    }
    destinazione = fopen("libreria_libri_modificata.csv", "w");
    if (destinazione == NULL)
    {
        printf("Errore apertura file per scrittura.\n");
        exit(1);
    }
    contatore = SalvaLibri(listaLibri, contatore);

    //==========Menu==========
    do
    {

        Menu();
        printf("Inserisci un numero come scelta\n");
        scanf(" %d", &scelta);
        scelta = ControlloScelta(scelta, contatore, listaLibri);
    } while (scelta != 4);

    fclose(sorgente);
    fclose(destinazione);
    return 0;
}
//==========FINE FILE==========
