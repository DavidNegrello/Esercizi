#include <stdio.h>


struct Punto // Definizione e creazinoe della struct 
{
    int x;
    int y;
    int z;
};

// Funzione per stampare le coordinate di un punto
void stampa_punto(struct Punto punto)
{
    printf("Coordinate del punto: (%d, %d, %d)\n", punto.x, punto.y, punto.z);
}

// Funzione per creare un nuovo punto con coordinate incrementate di una unità
struct Punto punto_modificato(struct Punto punto)
{
    struct Punto nuovoPunto;
    nuovoPunto.x = punto.x + 1;
    nuovoPunto.y = punto.y + 1;
    nuovoPunto.z = punto.z + 1;
    return nuovoPunto;
}

int main(int argc, char *argv[])
{
    // Creazione di un punto
    struct Punto punto1;
    punto1.x = 1;
    punto1.y = 2;
    punto1.z = 3;

    // Visualizzazione delle coordinate del punto
    printf("Punto 1:\n");
    stampa_punto(punto1);

    // Creazione di un nuovo punto con coordinate incrementate
    struct Punto punto2 = punto_modificato(punto1);

    // Visualizzazione delle coordinate del nuovo punto
    printf("Punto 2 (coordinate incrementate):\n");
    stampa_punto(punto2);

    return 0;
}
