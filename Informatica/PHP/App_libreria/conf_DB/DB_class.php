<?php

class DB_class
{
    private static PDO $db; //funzione già esistente

    public static function getDB($config):PDO{
        self::$db=new PDO($config['dns'],$config['username'],$config['password'],$config['options'],); //si va a cercare il nome di una variabile statica dentro la stessa classe
        return self::$db;
    }
}