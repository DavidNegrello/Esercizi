#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/wait.h>

int main()
{
    int p;
    int fd[2];

    if (pipe(fd) == -1)
    {
        printf("Errore creazione pipe");
        exit(-1);
    }

    p = fork();

    if (p < 0)
    {
        close(fd[0]);
        close(fd[1]);
        printf("Errore creazione fork");
        exit(-1);
    }

    if (p == 0)
    {                 // figlio
        close(fd[0]); // chiudo uscita (lettura)
        int array[5];
        printf("Sono il figlio. Il mio PID è: %d \n", getpid());
        for (int i = 0; i < 5; i++)
        {
            printf("Inserisci il %d numero \n", i + 1);
            scanf("%d", &array[i]);
        }
        if (write(fd[1], array, sizeof(array)) == -1)
        {
            perror("write");
            exit(-1);
        }
        close(fd[1]); // chiudo la pipe
    }
    else
    { // padre
        int moltiplicatore;
        int array[5];
        close(fd[1]); // chiudo entrata (scrittura)
        printf("Sono il padre. Il mio PID è: %d \n", getpid());
        wait(NULL); // attendo il termine del figlio
        printf("Inserisci un moltiplicatore: ");
        scanf("%d", &moltiplicatore);
        if (read(fd[0], array, sizeof(array)) == -1)
        {
            perror("read");
            exit(-1);
        }
        for (int i = 0; i < 5; i++)
        {
            printf("%d \n", array[i] * moltiplicatore);
        }
        close(fd[0]); // chiudo la pipe
    }
    return 0;
}
