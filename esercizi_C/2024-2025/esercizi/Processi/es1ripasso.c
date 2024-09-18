#include <stdio.h>

int main(int argc, char *argv[])
{
   // Inizializza la variabile somma a 0
   int somma = 0;
   // Crea un vettore per contenere cinque numeri interi
   int n[5];
   // dimensione dell'array
   int max = 5;
   // Chiedi all'utente di inserire limite1 e limite2 compresi tra 30 e 50
   int limite1, limite2;
   printf("Inserisci limite1 (tra 30 e 50): ");
   scanf("%d", &limite1);
   printf("Inserisci limite2 (tra 30 e 50): ");
   scanf("%d", &limite2);

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