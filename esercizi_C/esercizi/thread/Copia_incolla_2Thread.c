#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>

#define False 0
#define True 1
#define Buffer_Dim 1024
#define Buffer_Size 64
// Struttura per passare i parametri ai thread
typedef struct {
    FILE *input;
    FILE *output;
    unsigned char buffer[Buffer_Dim];
    int n;
} ThreadParams;

ThreadParams parametri[Buffer_Size];
int fineFile= False;
int readIndice=0;
int writeIndice=0;
int N_block=0;

// Funzione per il thread di lettura
void *Leggi(void *arg) {
    ThreadParams *params = (ThreadParams *) arg;
    char buffer[1024];
    size_t bytesRead;
    while (!feof(params->input))
    {
        //to do
    }
    
    while ((bytesRead = fread(buffer, 1, sizeof(buffer), params->input)) > 0) {
        fwrite(buffer, 1, bytesRead, params->output);
    }

    return NULL;
}

int main(int argc, char *argv[]) {
    if (argc != 3) {
        printf("Usage: %s input_file output_file\n", argv[0]);
        return 1;
    }

    FILE *input = fopen(argv[1], "r");
    if (!input) {
        perror("Error opening input file");
        return 1;
    }

    FILE *output = fopen(argv[2], "w");
    if (!output) {
        perror("Error opening output file");
        fclose(input);
        return 1;
    }

    // Creazione dei thread
    pthread_t readThreadId;
    ThreadParams params = {input, output};
    pthread_create(&readThreadId, NULL, readThread, &params);

    // Attesa della fine del thread di lettura
    pthread_join(readThreadId, NULL);

    // Chiusura dei file
    fclose(input);
    fclose(output);

    return 0;
}