#include <stdio.h>
#include <stdlib.h>
int main()
{
	FILE *fileOrigine;	//"FILE" e' un tipo di variabile data dalla libreria, "*fileOrigine" e' un puntatore a un oggetto di tipo FILE
	char c;		//dichiarazione variabile char(carattere)
	fileOrigine=fopen("nomi.txt", "r"); 	//apre il file in uno .txt (in modalita' lettura), se esiste, oppure lo crea

	if(fileOrigine==NULL) 	//controlla se il file riesce ad aprirlo
	{
		printf("Impossibile aprire il file");
		exit (1);	//il programma si ferma
	}
	else 
	{
		c=fgetc(fileOrigine); //prende il primo carattere che trova e lo salva nella variabile char
		while (c!=EOF)	//finche' non finiscono i caratteri, significa "end of file"
		{
			putchar(c);    //stampa il carattere
			c=fgetc(fileOrigine);	//ripete quello detto nella riga 16	e continua a salvare e stampare
	    }
	
	if(fclose(fileOrigine)==0) //ritorna un bool e se e' 0 significa true oppure false con 1
		printf("\n\nChiusura del file avvenuta con successo");
	else printf("\n\nChiusura del file fallita");
    }
	//risultato:
	//controlla l'apertura del file, salva il primo carattere lo stampa, dopo lo copia e continua a stamparli fino alla fine della frase/file, infine controlla la chiusura
    return 0;
}
