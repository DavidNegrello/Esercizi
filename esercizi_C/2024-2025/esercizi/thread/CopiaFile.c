#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>
#include <string.h>

#define BUFFER_SIZE 1024

pthread_t T_scrittura;

int n;
char buffer[BUFFER_SIZE];

void *scrittura(void *fileDestinazione)
{
    FILE *dest_file = fopen((char *)fileDestinazione, "a");
    if (dest_file == NULL)
    {
        perror("Errore nell'apertura del file di destinazione");
        exit(-1);
    }

    fwrite(buffer, 1, n, dest_file); // Scrivi nel file di destinazione

    fclose(dest_file);
    pthread_exit(NULL);
}

void *lettura(void *arg)
{
    char **nomiFile = (char **)arg;
    char *fileOrigine = nomiFile[0];
    char *fileDestinazione = nomiFile[1];

    FILE *src_file = fopen(fileOrigine, "r");
    if (src_file == NULL)
    {
        perror("Errore nell'apertura del file di origine");
        exit(-1);
    }

    while ((n = fread(buffer, 1, BUFFER_SIZE, src_file)) > 0)
    {
        pthread_create(&T_scrittura, NULL, scrittura, fileDestinazione);
        pthread_join(T_scrittura, NULL); // Aspetta che il thread di scrittura finisca
    }

    fclose(src_file);
    pthread_exit(NULL);
}

int main(int argc, char *argv[])
{
    if (argc != 3)
    {
        printf("Numero di argomenti errato.\n");
        exit(-1);
    }

    pthread_t T_lettura;

    char *nomiFile[] = {argv[1], argv[2]}; // Array dei nomi dei file

    //cancello contenuto file aprendolo e chiudendolo
    FILE *dest_file = fopen(argv[2], "w");
    if (dest_file == NULL)
    {
        perror("Errore nell'apertura del file di destinazione");
        exit(-1);
    }
    fclose(dest_file);

    pthread_create(&T_lettura, NULL, lettura, nomiFile);

    pthread_join(T_lettura, NULL); // Aspetta che il thread di lettura finisca

    printf("Operazione completata.\n");

    return 0;
}