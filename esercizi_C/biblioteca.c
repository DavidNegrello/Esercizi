#include <stdio.h>
#include <stdlib.h>
struct biblioteca
{
    char titolo[30];
    char autore[30];
    float prezzo;
};
void Inserimento(struct biblioteca *libreria)
{
        printf("Inserisci il titolo del libro: ");
        scanf("%s", libreria->titolo);
        printf("Inserisci l'autore del libro: ");
        scanf("%s", libreria->autore);
        printf("Inserisci il prezzo del libro: ");
        scanf("%f", &libreria->prezzo);
}
void stampa(struct biblioteca *libreria,int indice)
{
    printf("Elenco dei libri: ");
    for (int i = 0; i < indice; i++)
    {
        printf("Libro %d:\n", i + 1);
        printf("Titolo: %s\n", libreria[i].titolo);
        printf("Autore: %s\n", libreria[i].autore);
        printf("Prezzo: %.2f\n", libreria[i].prezzo);
    }
    
}
void applicaSconto(struct biblioteca *libreria, int indice, float sconto) {
    for (int i = 0; i < indice; i++) {
        libreria[i].prezzo -= (libreria[i].prezzo =sconto / 100);
    }
}
int main(int argc, char *argv[])
{
    const int indice = 3;
    float sconto;
    struct biblioteca *libreria[indice];
    for (int i = 0; i < indice; i++)
    {
         libreria[i] = malloc(sizeof(struct biblioteca));
         printf("Libro numero %d\n",i+1);
         Inserimento(&libreria[i]); //passo l'indirizzo di memoria con l'indice
    }
    stampa(libreria,indice);

    
    printf("Inserisci l'importo dello sconto (in percentuale): ");
    scanf("%f", &sconto);
    applicaSconto(libreria, indice, sconto);

    // Stampa la biblioteca con lo sconto applicato
    printf("\nBiblioteca con sconto applicato:\n");
    stampa(libreria, indice);
    return 0;
}