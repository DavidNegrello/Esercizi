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
#define Lista 29
#define buffer_dim  1024 
//=================STRUCT===============
typedef struct
{
    char categoria[DIM];
    char titolo[DIM];
    char autore[DIM];
    int anno;
    float prezzo
} Libreria;

//=================FUNZIONI================



int main(int argc, char *argv[])
{
    Libreria listaLibri[29];
    FILE *sorgente;
    char carattere;
    unsigned char buffer[buffer_dim];


    //==========FILE==========
    sorgente = fopen("Lista dei libri", "r"); 
    if (sorgente == NULL)
    {
        printf("Errore apertura file");
        exit(1);
    }
    while (!feof(sorgente)) 
    {
        carattere = fread(buffer,1,buffer_dim,sorgente); 
    }

    fclose(sorgente);
    return 0;
}