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

void Menu()
{
    printf("==========MENU==========\n");
    printf("1. Visualizza la lista dei libri\n");
    printf("2. Modifica lista con inserimento categorie\n");
    printf("3. Visualizza per categoria\n");
    printf("4. Esci\n");
}

// Funzione per ordinare la lista di libri in base alla categoria
void OrdinaPerCategoria(Libreria listaLibri[], int contatore)
{
    Libreria scambio;
    for (int i = 0; i < contatore - 1; i++)
    {
        for (int j = 0; j < contatore - i - 1; j++)
        {
            if (strcmp(listaLibri[j].categoria, listaLibri[j + 1].categoria) > 0)
            {
                // Scambio delle posizioni
                scambio = listaLibri[j];
                listaLibri[j] = listaLibri[j + 1];
                listaLibri[j + 1] = scambio;
            }
        }
    }
}

// Funzione per salvare i dati all'interno di un nuovo CSV con l'orninamento
void ScriviSuFile(Libreria listaLibri[], int contatore)
{

    // Ordina la lista di libri per categoria prima di salvare su file
    OrdinaPerCategoria(listaLibri, contatore);

    // Apri il file di destinazione in modalità scrittura, per sovrascrivere il contenuto ogni passaggio fino alla fine del processo
    FILE *destinazione = fopen("libreria_libri_modificata.csv", "w");
    if (destinazione == NULL)
    {
        printf("Errore apertura file per scrittura.\n");
        exit(1);
    }

    // Layout della tabella
    fprintf(destinazione, "%-30s %-30s %-10s %-5s %-15s\n", "Titolo", "Autore", "Prezzo", "Anno", "Categoria");
    fprintf(destinazione, "----------------------------------------------------------------------------------------\n");

    // libro con le categorie aggiornate
    for (int i = 0; i < contatore; i++)
    {
        // righe con il layout della tabella
        fprintf(destinazione, "%-30s %-30s %-10.2f %-5d %-15s\n",
                listaLibri[i].titolo,
                listaLibri[i].autore,
                listaLibri[i].prezzo,
                listaLibri[i].anno,
                listaLibri[i].categoria);
    }

    // Chiudi il file
    fclose(destinazione);
}

int ControlloScelta(int scelta, int contatore, Libreria listaLibri[])
{
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

    case 2: // Modifica categoria
        for (int i = 0; i < contatore; i++)
        {
            printf("Cambia la categoria del libro %s: ", listaLibri[i].titolo);
            scanf(" %s", categoriaModifica);
            strcpy(listaLibri[i].categoria, categoriaModifica);

            // Dopo ogni modifica, salva su file
            ScriviSuFile(listaLibri, contatore);
        }
        break;

    case 3: // Visualizza libri per categoria
        printf("Scegli una categoria:\n");
        scanf(" %s", sceltaCategoria);
        for (int i = 0; i < contatore; i++)
        {
            if (strcmp(sceltaCategoria, listaLibri[i].categoria) == 0)
            {
                printf("%d. %s, %s, %.2f, %d, %s\n",
                       i + 1,
                       listaLibri[i].titolo,
                       listaLibri[i].autore,
                       listaLibri[i].prezzo,
                       listaLibri[i].anno,
                       listaLibri[i].categoria);
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
        printf("Errore apertura file sorgente\n");
        exit(1);
    }

    contatore = SalvaLibri(listaLibri, contatore);

    //==========Menu==========
    do
    {
        Menu();
        printf("Inserisci un numero come scelta: ");
        scanf(" %d", &scelta);
        scelta = ControlloScelta(scelta, contatore, listaLibri);
    } while (scelta != 4);

    fclose(sorgente);

    return 0;
}
//==========FINE FILE==========
