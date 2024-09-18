#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>
#include <string.h>


typedef struct {
    char nome[50];
    char cognome[50];
    char classe[10];
    float media;
}Studente;


void *printStudentData(void *arg) {
     Studente *studente = (Studente *) arg;

    printf("Student data:\n");
    printf("Name: %s\n", studente->nome);
    printf("Surname: %s\n", studente->cognome);
    printf("Class: %s\n", studente->classe);
    printf("Grade: %.2f\n", studente->media);

    pthread_exit(0);
}


void *ScritturaFile(void *arg) {

    Studente *studente = (Studente *) arg;
    FILE *file;

    file = fopen("dati_studente.txt", "w");
    if (file == NULL) {
        printf("Errore apertura file\n");
        pthread_exit(0);
    }

    fprintf(file, "Student data:\n");
    fprintf(file, "Name: %s\n", studente->nome);
    fprintf(file, "Surname: %s\n", studente->cognome);
    fprintf(file, "Class: %s\n", studente->classe);
    fprintf(file, "Grade: %.2f\n", studente->media);

    fclose(file);
    pthread_exit(0);
}

int main() {
    Studente studente;
    pthread_t stampa, scrittura;

    printf("Inserimento dati dello studente:\n");
    printf("Nome: ");
    scanf("%s", studente.nome);
    printf("Cognome: ");
    scanf("%s", studente.cognome);
    printf("Classe: ");
    scanf("%s", studente.classe);
    printf("Media: ");
    scanf("%f", &studente.media);

    
    pthread_create(&stampa, NULL, &printStudentData, &studente);
    pthread_create(&scrittura, NULL, &ScritturaFile, &studente);

    
    pthread_join(stampa, NULL);
    pthread_join(scrittura, NULL);

    return 0;
}
