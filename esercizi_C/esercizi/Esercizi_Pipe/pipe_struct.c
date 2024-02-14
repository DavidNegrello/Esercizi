//L'esercizio non funziona correttamente
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/wait.h>
#define dimensione_t 2
typedef struct
{
    char titolo[100];
    char autore[100];
    int prezzo;
} Libro;

int main(int argc, char *argv[])
{
    Libro book;
    int pipe[2];
    int p;
    p = fork();
    if (p < 0) // controllo creazione
    {
        printf("Errore nella creazione\n");
        exit(-1);
    }
    if (p > 0) // padre
    {
        close(pipe[0]);
        for (int i = 0; i < dimensione_t; i++)
        {
            printf("Inserisci il titolo del libro\n");
            scanf("%s", book.titolo);
            printf("Inserisci l'autore del libro\n");
            scanf("%s", book.autore);
            printf("Inserisci il prezzo del libro\n");
            scanf("%d", &book.prezzo);
            if (write(pipe[1], &book, sizeof(book)) == -1)
            {
                printf("Errore scrittura nella pipe\n");
                exit(-1);
            }
        }

        close(pipe[1]);
    }
    else // figlio
    {
        close(pipe[1]);
        for (int i = 0; i < dimensione_t; i++)
        {
            if (read(pipe[0],  &book, sizeof(book)) == -1)
            {
                printf("Errore scrittura nella pipe\n");
                exit(-1);
            }
            printf("Il titolo del libro %d ° è: %s", i, book.titolo);
            printf("L'autore del libro %d ° è: %s", i, book.autore);
            printf("Il prezzo del libro %d ° è: %d", i, book.prezzo);
        }
        close(pipe[0]);
    }
    return 0;
}