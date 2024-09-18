#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>


#define Indice 20
typedef struct{
    char nome[50];
    int y;
}Valori;

void *StampaNome(void *arg) {
    Valori *nomeFunzine= (Valori *) arg;
    nomeFunzine->y++;
    printf("Nome: %s, ripetuto: %d\n", nomeFunzine->nome,nomeFunzine->y); 
    
    return NULL;
}
//il programma deve scrivere 20 volte un none inserito dall'utente con 20 thread diversi
int main() {
    Valori valori;

    pthread_t threads[Indice];
    
    //Inserisce il nome
    printf("Inserisci il nome\n");
    scanf("%s", valori.nome);

    // crea quattro thread per cercare il numero nell'array
    for (int i = 0; i < Indice; i++) {
        pthread_create(&threads[i], NULL, &StampaNome, valori.nome);
    }

    // aspetta che i thread terminino
    for (int i = 0; i < Indice; i++) {
        pthread_join(threads[i], NULL);
    }

    return 0;
}