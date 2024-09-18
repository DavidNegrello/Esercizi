#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#define buffer_dim  1024 
int main(int argc, char *argv[])
{

    FILE *origine, *destinazione; // scrittura lettura
    int n;
    unsigned char buffer[buffer_dim]; // significa che il vettore non ha assegnazione
    if (argc != 3)
    {
        printf("Errore");
        return (1);
    }
    origine = fopen(argv[1], "r"); // sorgente file, visto che è in lettura lo legge con read
    if (origine == NULL)
    {
        printf("Errore apertura file");
        exit(1);
    }
    destinazione = fopen(argv[2], "w"); // sorgente file, visto che è in scrittura lo scrive con write
    if (destinazione == NULL)
    {
        printf("Errore apertura file");
        exit(1);
    }
    while (!feof(origine)) // finchè non c'è la fine del file "origine"
    {
        n = fread(buffer,1,buffer_dim,origine); // serve un array di caratteri, 1 (byte) trasferisce i caratteri, massimo contenuto "buffer_dim"
        if (n>0)
        {
            fwrite(buffer,1,n,destinazione);
        }
    }
    fclose(origine);
    fclose(destinazione);
    return 0;
}