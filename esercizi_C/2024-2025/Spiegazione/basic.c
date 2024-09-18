#include <stdio.h>
#include <stdlib.h>
int main()
{
	
	FILE *fileOrigine;	//"FILE" e' un tipo di variabile data dalla libreria, "*fileOrigine" e' un puntatore a un oggetto di tipo FILE

	fileOrigine=fopen("nomeFile.txt", "r"); 	//apre il file in uno .txt (in modalita' lettura), se esiste, oppure lo crea

	if(fileOrigine==NULL)  	//controlla se il file riesce ad aprirlo
	{
		printf("Impossibile aprire il file");
		exit (1);	//il programma si ferma
	}
	else 
	{
		 printf("File aperto");
    }

    fclose(fileOrigine);           //chiude il file
	
    if(fclose(fileOrigine)==0) 	//ritorna un bool e se e' 0 significa true oppure false con 1
		printf("\nChiusura del file avvenuta con successo");
	else printf("\nChiusura del file fallita");

	//risultato:
	//il programma controlla l'apertura del file e la sua chiusura
	return 0;
}
