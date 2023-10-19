#include <stdio.h>
#include <string.h>
#include <stdlib.h>
//portare dati da un array a un file esterno
//scrivere in un file
int main(int argc, char *argv[])
{
    char c;
    char vetNomi[20];
    //1) variabile puntatore a file 
    FILE *portaFile; //scrittura, lettura
    portaFile=fopen("nomi.txt","w"); //apre il file in uno .txt o lo crea se non esiste
    if(portaFile==NULL) //se il file non riesce ad aprirlo
    {
        printf("File non aperto");
        exit (1);
    }
    do
    {
        c=fgetc(portaFile); //prende il primo carattere che trova e lo mette dentro a porta file
        putchar(c); //stampa il carattere
    } while (c!=EOF);   //finchè non finiscono i caratteri, significa "end of file"
    
    for(int i=0;i<4;i++)
    {
        printf("inserisci il nome %d\n",i+1);
        scanf("%s\n",vetNomi);
        fputs(vetNomi,portaFile);  //put della stringa dentro il file .txt
        fputc('\n',portaFile);  //aggiunge il carattere
    }
    fclose(portaFile);
    return 0;
}