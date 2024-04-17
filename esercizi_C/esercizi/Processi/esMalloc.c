#include <stdio.h>
#include <string.h>
#include <stdlib.h>

struct studente // Definizione e creazinoe della struct
{
    char nome[10];
    char cognome[10];
    float media;
};


int main(int argc, char *argv[])
{
    const int indice=3;
    struct studente *alunni[indice]; // creazione array di 3 puntatori
    //allocazione delal memoria e valori
    for (int i = 0; i < indice; i++)
    {
        alunni[i] = malloc(sizeof(struct studente));
        printf("Inserisci il cognome dello studente %d: ", i + 1);
        scanf("%s", alunni[i]->cognome);

        printf("Inserisci il nome dello studente %d: ", i + 1);
        scanf("%s", alunni[i]->nome);

        printf("Inserisci la media dello studente %d: ", i + 1);
        scanf("%f", &(alunni[i]->media));
    }

    // Stampa il contenuto
    for (int i = 0; i < indice; i++)
    {
        printf("\nStudente %d:\n", i + 1);
        printf("Cognome: %s\n", alunni[i]->cognome);
        printf("Nome: %s\n", alunni[i]->nome);
        printf("Media: %.2f\n", alunni[i]->media);
    }

    // Stampa il contenuto dell'array (indirizzi di memoria)
    printf("\nIndirizzi di memoria degli studenti:\n");
    for (int i = 0; i < indice; i++)
    {
        printf("Studente %d: %p\n", i + 1, (void *)alunni[i]);
    }

    // Deallocazione della memoria allocata dinamicamente
    for (int i = 0; i < indice; i++)
    {
        free(alunni[i]);
    }

    return 0;
}