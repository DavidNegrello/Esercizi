#include <stdio.h>
#include <stdlib.h>
#include <time.h>

#define MAX_ELEMENTI 10

// Funzione per generare un array casuale
void generaArray(int array[], int n) {
    srand(time(NULL));
    for (int i = 0; i < n; i++) {
        array[i] = rand() % 100;
    }
}

// Funzione per visualizzare l'array
void visualizzaArray(int array[], int n) {
    printf("Array: ");
    for (int i = 0; i < n; i++) {
        printf("%d ", array[i]);
    }
    printf("\n");
}

// Funzione per invertire l'array
void inverteArray(int array[], int n) {
    int temp;
    for (int i = 0; i < n / 2; i++) {
        temp = array[i];
        array[i] = array[n - i - 1];
        array[n - i - 1] = temp;
    }
}

// Funzione per calcolare la somma e la media degli elementi dell'array
void sommaMediaArray(int array[], int n) {
    int somma = 0;
    for (int i = 0; i < n; i++) {
        somma += array[i];
    }
    float media = (float)somma / n;
    printf("Somma: %d\nMedia: %.2f\n", somma, media);
}

// Funzione per visualizzare i numeri pari
void visualizzaPari(int array[], int n) {
    printf("Numeri pari: ");
    for (int i = 0; i < n; i++) {
        if (array[i] % 2 == 0) {
            printf("%d ", array[i]);
        }
    }
    printf("\n");
}

// Funzione per visualizzare i numeri dispari
void visualizzaDispari(int array[], int n) {
    printf("Numeri dispari: ");
    for (int i = 0; i < n; i++) {
        if (array[i] % 2 != 0) {
            printf("%d ", array[i]);
        }
    }
    printf("\n");
}

// Funzione per cercare un numero nell'array
int cercaNumero(int array[], int n, int num) {
    for (int i = 0; i < n; i++) {
        if (array[i] == num) {
            return i;
        }
    }
    return -1;
}

// Funzione per eliminare un elemento dall'array
void eliminaElemento(int array[], int n, int num) {
    int pos = cercaNumero(array, n, num);
    if (pos != -1) {
        for (int i = pos; i < n - 1; i++) {
            array[i] = array[i + 1];
        }
        n--;
    }
}

// Funzione per alternare gli elementi dell'array
void alternaElementi(int array[], int n) {
    for (int i = 0; i < n - 1; i += 2) {
        int temp = array[i];
        array[i] = array[i + 1];
        array[i + 1] = temp;
    }
}

// Funzione per ordinare l'array
void ordinaArray(int array[], int n) {
    for (int i = 0; i < n - 1; i++) {
        for (int j = i + 1; j < n; j++) {
            if (array[i] > array[j]) {
                int temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
        }
    }
}

int main() {
    int array[MAX_ELEMENTI];
    int n = MAX_ELEMENTI;
    int scelta;

    generaArray(array, n);

    while (1) {
        printf("Menù:\n");
        printf("1. Visualizza array\n");
        printf("2. Inverte array\n");
        printf("3. Somma e media\n");
        printf("4. Visualizza numeri pari\n");
        printf("5. Visualizza numeri dispari\n");
        printf("6. Cerca numero\n");
        printf("7. Elimina elemento\n");
        printf("8. Alterna elementi\n");
        printf("9. Ordina array\n");
        printf("10. Esci\n");

        printf("Inserisci scelta: ");
        scanf("%d", &scelta);

        switch (scelta) {
            case 1:
                visualizzaArray(array, n);
                break;
            case 2:
                inverteArray(array, n);
                visualizzaArray(array, n);
                break;
            case 3:
                sommaMediaArray(array, n);
                break;
            case 4:
                visualizzaPari(array, n);
                break;
            case 5:
                visualizzaDispari(array, n);
                break;
            case 6:
                int num;
                printf("Inserisci numero da cercare: ");
                scanf("%d", &num);
                int pos = cercaNumero(array, n, num);
                if (pos != -1) {
                    printf("Numero trovato in posizione %d\n", pos);
                } else {
                    printf("Numero non trovato\n");
                }
                break;
            case 7:
                int numElim;
                printf("Inserisci numero da eliminare: ");
                scanf("%d", &numElim);
                eliminaElemento(array, n, numElim);
                visualizzaArray(array, n);
                break;
            case 8:
                alternaElementi(array, n);
                visualizzaArray(array, n);
                break;
            case 9:
                ordinaArray(array, n);
                visualizzaArray(array, n);
                break;
            case 10:
                return 0;
            default:
                printf("Scelta non valida\n");
        }
    }

    return 0;
}