#include <pthread.h>
#include <stdio.h>
#include <stdlib.h>

#define BUFF_DIM 4096 // dimensione del buffer all'intero del buffer ring
#define FETTE 80      // numero di fette
typedef struct {      // buffer ad anello
  unsigned char buffer[BUFF_DIM];
  int n; // byte letti dal file, presenti nel buffer
} Anello;

Anello buffer[FETTE]; // ring buffer

// contatori riferiti al buffer
int write_index = 0, read_index = 0, cont_fette = 0;
int end = 0; // fine

pthread_mutex_t mutex; // semaforo

pthread_cond_t not_full, not_empty; // condizioni

void *leggi(void *arg) {
  FILE *origine =
      (FILE *)arg; // file di origine castato dopo averlo aperto nel main
  int n;
  while (!feof(origine)) {

    pthread_mutex_lock(&mutex); // sezione critica

    if (cont_fette < FETTE) {

      n = fread(buffer[write_index].buffer, 1, BUFF_DIM,
                origine);        // lettura dal file di origine
      buffer[write_index].n = n; // byte letti dal file

      cont_fette++; // fette del ringbuffer scritte incrementate
      write_index = (write_index + 1) % FETTE; // riassegnazione del write index

      pthread_cond_signal(
          &not_full); // mando sengale per dire che ringbuffer non è pieno
    } else {
      // aspetta coi semafori

      pthread_cond_wait(&not_empty,
                        &mutex); // aspetta che il ringbuffer non sia vuoto
    }
    pthread_mutex_unlock(&mutex); // sblocco sezione critica
  }
  end = 1;         // fine di lettura del file
  fclose(origine); // chiusa lettura del file

  pthread_exit(NULL);
}
void *scrivi(void *arg) {
  FILE *destinazione = (FILE *)arg;

  while (1) {

    pthread_mutex_lock(&mutex);   // blocco della sezione critica
    if (end && cont_fette == 0) { // per vedere se ha finito di leggere
      break;
    }

    if (cont_fette >
        0) { // per vedere se sono state scritte delle fette nel ring buffer
      fwrite(buffer[read_index].buffer, 1, buffer[read_index].n,
             destinazione); // scrittura sul file di destinazione

      cont_fette--; // diminuzione delle fette da scrivere nel file di
                    // destinazione

      read_index = (read_index + 1) % FETTE; // riassegnazione del read index

      pthread_cond_signal(
          &not_empty); // mando segnale per dire che ringbuffer non è vuoto
    } else {
      // aspetta coi semafori
      pthread_cond_wait(&not_full,
                        &mutex); // aspetta che ringbuffer non sia pieno
    }
    pthread_mutex_unlock(&mutex); // sblocco sezione critica
  }

  fclose(destinazione); // chiusura file destinazione

  pthread_exit(NULL);
}

int main(int argc, char *argv[]) {
  if (argc != 3) { // controllo che numeri argomenti sia corretto
    printf("Errore argomenti \n");
    exit(-1);
  }

  pthread_t Tleggi, Tscrivi;
  FILE *origine, *destinazione;

  origine = fopen(argv[1], "r"); // apertura file origine
  if (origine == NULL) {
    printf("Errore file di origine \n");
    exit(-1);
  }

  destinazione = fopen(argv[2], "w"); // apertura file destinazione
  if (destinazione == NULL) {
    printf("Errore file di destinazione \n");
    exit(-1);
  }

  pthread_create(&Tleggi, NULL, &leggi, (void *)origine);
  pthread_create(&Tscrivi, NULL, &scrivi, (void *)destinazione);

  pthread_join(Tleggi, NULL);
  pthread_join(Tscrivi, NULL);

  // distruzione delle cose
  pthread_mutex_destroy(&mutex);
  pthread_cond_destroy(&not_empty);
  pthread_cond_destroy(&not_full);

  return 0;
}