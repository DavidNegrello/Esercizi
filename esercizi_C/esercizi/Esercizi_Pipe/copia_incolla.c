#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/wait.h>

#define BUFFER_SIZE 4096

int main(int argc, char *argv[]) {


    // Apertura del file di origine in lettura
    FILE *source_file = fopen(argv[1], "rb");
    if (source_file == NULL) {
        printf("Errore apertura file");
        return 1;
    }

    // Creazione della pipe
    int pipefd[2];
    if (pipe(pipefd) == -1) {
        printf("Errore creazione pipe");
        return 1;
    }

    // Creazione di un processo figlio
    pid_t pid = fork();
    if (pid < 0) {
        printf("Errore creazione processo");
        return 1;
    }

    if (pid == 0) { // Processo figlio
       
        close(pipefd[1]);

        // Apertura del file di destinazione in scrittura
        FILE *destination_file = fopen(argv[2], "wb");
        if (destination_file == NULL) {
            printf("Errore apertura file");
            return 1;
        }

        // Lettura dai dati dalla pipe e scrittura nel file di destinazione
        char buffer[BUFFER_SIZE];
        ssize_t bytes_read;
        while ((bytes_read = read(pipefd[0], buffer, BUFFER_SIZE)) > 0) {
            fwrite(buffer, 1, bytes_read, destination_file);
        }

        
        fclose(destination_file);
        close(pipefd[0]);

    } else { // Processo padre
        
        close(pipefd[0]);

        // Lettura dai dati 
        char buffer[BUFFER_SIZE];
        ssize_t bytes_read;
        while ((bytes_read = fread(buffer, 1, BUFFER_SIZE, source_file)) > 0) {
            if (write(pipefd[1], buffer, bytes_read) != bytes_read) {
                printf("Errore scrittura");
                return 1;
            }
        }

        
        fclose(source_file);
        close(pipefd[1]);

        // Attende la terminazione del processo figlio
        wait(NULL);

        return 0;
    }
}