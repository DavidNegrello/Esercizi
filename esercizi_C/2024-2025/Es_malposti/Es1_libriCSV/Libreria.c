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
    printf("==========MENU==========\n");
    printf("1. Visualizza la lista dei libri\n");
    printf("2. Modifica lista con inserimento categorie\n");
    printf("3. Visualizza per categoria\n");
    printf("4. Esci\n");
}
void ControlloScelta(int scelta, int contatore,Libreria listaLibri[]){
    char categoriaModifica[DIM];
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
    case 2: //canbia categoria
        for (int i = 0; i < contatore; i++)
        {
            printf("Cambia la categoria del libro %s \n",listaLibri[i].titolo);
            strcpy(listaLibri[i].categoria, scanf(" %s",  categoriaModifica));
        }
        
        break;
    }
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
    contatore = SalvaLibri(listaLibri, contatore);

    
    do
    {
        //==========Menu==========
        Menu();
        printf("Inserisci un numero come scelta\n");
        scanf(" %d",&scelta);
       scelta=ControlloScelta(scelta,contatore,listaLibri);
    } while (scelta!=4);
    



    
    fclose(sorgente);
    return 0;
}
//==========FINE FILE==========
