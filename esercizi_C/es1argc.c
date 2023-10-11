#include <stdio.h>
#include <stdlib.h>

int main(int argc, char *argv[])
{
    // Inizializza la variabile somma a 0
    int somma = 0;
    // Crea un vettore per contenere cinque numeri interi
    int n[5];
    // dimensione dell'array
    int max = 5;
    // Verifica se sono stati forniti esattamente tre argomenti (nome del programma, limite1, limite2)
    if (argc != 3)
    {
        printf("Utilizzo: %s <limite1> <limite2>\n", argv[0]);
        return 1; // Esce con codice di errore
    }

    // Converte gli argomenti da stringhe a numeri interi
    int limite1 = atoi(argv[1]);
    int limite2 = atoi(argv[2]);

    // Verifica se i limiti inseriti sono validi
    if (limite1 >= 30 && limite1 <= 50 && limite2 >= 30 && limite2 <= 50)
    {
        printf("Limite1: %d, Limite2: %d\n", limite1, limite2);
        // Chiedi all'utente di inserire cinque numeri interi e li salva nel vettore n
        for (int i = 0; i < max; i++)
        {
            printf("Inserisci il %d° numero intero: ", i + 1);
            scanf("%d", &n[i]);
        }
        // Visualizza il contenuto del vettore n
        printf("Contenuto del vettore n: ");
        for (int i = 0; i < max; i++)
        {
            printf("%d ", n[i]);
        }
        printf("\n");
        // Trova numeri compresi tra limite1 e limite2 e visualizza indice e posizione
        for (int i = 0; i < max; i++)
        {
            if (n[i] >= limite1 && n[i] <= limite2)
            {
                printf("Il numero %d è compreso tra limite1 e limite2. Indice: %d, Posizione: %d\n", n[i], i, i + 1);
                somma += n[i];
            }
        }
        // Visualizza la somma dei numeri compresi tra limite1 e limite2
        printf("La somma dei numeri compresi tra limite1 e limite2 è: %d\n", somma);
    }
    else
    {
        printf("I limiti inseriti non sono validi. Assicurati che siano compresi tra 30 e 50.\n");
    }

    return 0;
}