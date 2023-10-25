#include <stdio.h>
#include <stdlib.h>
#define MAX_STR 20 	//costante con variabile e assegnazione che va a definire la lunghezza dell'array
#define qtaNomi 4 	//costante con variabile e assegnazione
int i;	//variabile globale
int main()
{
	FILE *fileDestinazione; //"FILE" e' un tipo di variabile data dalla libreria, "*fileOrigine" e' un puntatore a un oggetto di tipo FILE
	char vett[MAX_STR]; 	//creazione vettore di caratteri con la lunghezza definita nella costante
	
	fileDestinazione=fopen("nomi.txt", "w"); 	//apre il file in uno .txt (in modalita' scrittura), se esiste, oppure lo crea
	if(fileDestinazione==NULL)  //controlla se il file riesce ad aprirlo
	{
		printf("Impossibile aprire il file");
		exit (1); //il programma si ferma
	}
	else 
	{
		for(i=0; i<qtaNomi; i++)	//creazione ciclo for e continua fino alla dimensione detta dalla costante
		{
			printf("Inserisci il %d^ nome   >>   ", i+1);
			scanf("%s", vett);       
			
			fputs(vett, fileDestinazione);  //salva gli elementi contenuti nel vettore (stringa) dentro il file .txt
			
			fputc('\n', fileDestinazione);  //aggiunge il carattere

		}	 
    }
    fclose(fileDestinazione);	//chiude il file
    
    fileDestinazione=fopen("nomi.txt", "r");    //apre il file in uno .txt (in modalita' lettura), se esiste, oppure lo crea
	while( fgets(vett, MAX_STR, fileDestinazione) ) 	//stampa il vettore e come indice ha quello nella costante
		printf("%s", vett); 
    
    if(fclose(fileDestinazione)==0)  //ritorna un bool e se e' 0 significa true oppure false con 1
		printf("\n\nChiusura del file avvenuta con successo");
	else printf("\n\nChiusura del file fallita");
	
	return 0;
}
