#include <stdio.h>
#include <pthread.h>
#include <stdbool.h>

#define DIM 64 
#define N 16 

/*n linguaggio c creare 2 thread, il primo genera una sequenza di N numeri casuali, con N fornito come argomento della riga di comando, 
e li inserisce singolarmente in un ring-buffer, il secondo preleva i numeri dal ring-buffer condiviso e li visualizza.
la codifica dovrà prevedere la necessaria sincronizzazione fra thread consumatore e thread produttore.*/
typedef struct
{
    unsigned char buffer[DIM]; 
    int n;                    
} buffer_vettore;


buffer_vettore ring_buffer[N];

int read_index = 0, write_index = 0, n_block = 0;

bool end = false;

pthread_mutex_t critical, mutex;

pthread_cond_t not_full, not_empty;
FILE *origine, *destinazione;


void *genera(void *par)
{
    for (int i = 1; i < 250; i++)
    {
        ring_buffer->buffer = rand() % scelta+1; // Estremo massimo escluso
    }
    int n;
    while (!feof(origine))
    {

        pthread_mutex_lock(&critical);

        
        if (n_block > N)
            pthread_cond_wait(&not_full, &critical);

       
        n = fread(ring_buffer[write_index].buffer, 1, DIM, origine);
        if (n > 0) // Se sono stati letti dei byte
        {

            ring_buffer[write_index].n = n;
       
            write_index = (write_index + 1) % N;
            pthread_mutex_lock(&mutex);
 
            n_block++;
    
            pthread_mutex_unlock(&mutex);
      
            pthread_cond_signal(&not_empty);
        }

        pthread_mutex_unlock(&critical);
    }
    
    end = true;

    pthread_cond_signal(&not_empty);
    pthread_exit(NULL);
}

void *scrivi(void *par)
{
    while (1)
    {
        
        if (end && n_block == 0)
            break;

        pthread_mutex_lock(&critical);

       
        if (n_block > 0)
        {
           
            fwrite(ring_buffer[read_index].buffer, 1, ring_buffer[read_index].n, destinazione);
           
            read_index = (read_index + 1) % N;
            
            pthread_mutex_lock(&mutex);
            
            n_block--;
            
            pthread_mutex_unlock(&mutex);
           
            pthread_cond_signal(&not_full);
        }
        else
        {
           
            pthread_cond_wait(&not_empty, &critical);
        }
        
        pthread_mutex_unlock(&critical);
    }
    pthread_exit(NULL);
}

int main(int argc, char *argv[])
{
    pthread_t scrittura_thread, lettura_thread;

    if (argc != 3)
    {
        printf("Uso: %s file-origine file-destinazione\r\n", argv[0]);
        return 0;
    }
    origine = fopen(argv[1], "rb");
    if (origine == NULL)
    {
        printf("Errore apertura file %s\r\n", argv[1]);
        return 0;
    }
    destinazione = fopen(argv[2], "wb");
    if (destinazione == NULL)
    {
        printf("Errore apertura file %s\r\n", argv[2]);
        fclose(origine);
        return -1;
    }

    int scelta;
    printf("Inserisci il Max \n");
    scanf("%d", &scelta);
    scelta=(int*)argv;

    // Inizializza i mutex e le variabili di condizione
    pthread_mutex_init(&critical, NULL);
    pthread_mutex_init(&mutex, NULL);
    pthread_cond_init(&not_full, NULL);
    pthread_cond_init(&not_empty, NULL);

    // Avvia i thread di lettura e scrittura
    pthread_create(&lettura_thread, NULL, &genera, NULL);
    pthread_create(&scrittura_thread, NULL, &scrivi, NULL);

    // Attende che i thread terminino
    pthread_join(lettura_thread, NULL);
    pthread_join(scrittura_thread, NULL);

    // Distrugge i mutex e le variabili di condizione
    pthread_mutex_destroy(&critical);
    pthread_mutex_destroy(&mutex);
    pthread_cond_destroy(&not_full);
    pthread_cond_destroy(&not_empty);

    fclose(origine);
    fclose(destinazione);

    return 0;    
}