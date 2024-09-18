#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>

#define N 1000
#define MIN 1
#define MAX 500

int array[N];
int num, pos;
int found;


void *search(void *arg) {
    int start, end, i;
    start = (int)arg * (N / 4);
    end = (int)(arg + 1) * (N / 4);
    if (arg == 3) {
        end = N;
    }
    for (i = start; i < end; i++) {
        if (array[i] == num) {
            pos = i;
            found = 1;
        }
    }
    return NULL;
}

int main() {
    int i;
    pthread_t threads[4];

    // crea l'array di numeri
    for (i = 0; i < N; i++) {
        array[i] = MIN + rand() % (MAX - MIN + 1);
    }

    // chiede all'utente di inserire un numero da cercare nell'array
    printf("Inserisci un numero da cercare nell'array: ");
    scanf("%d", &num);

    // crea quattro thread per cercare il numero nell'array
    for (i = 0; i < 4; i++) {
        pthread_create(&threads[i], NULL, search, (void *)i);
    }

    // aspetta che i thread terminino
    for (i = 0; i < 4; i++) {
        pthread_join(threads[i], NULL);
    }

    // stampa la posizione del numero nell'array
    if (found) {
        printf("Numero trovato alla posizione %d\n", pos);
    } else {
        printf("Numero non trovato\n");
    }

  

    return 0;
}