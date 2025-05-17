<?php
require_once 'function.php';    //questa require si usa quando abbiamo già fatto la require nel file precedente
class DBconf
{
    private static PDO $db; //funzione già esistente in php

    public static function getDB($config):PDO{
        if (!isset(self::$db)){ //se la variabile "DB" non è ancora stava chiamata la crea altrimenti te la ritorna senza dare errore

            try {
                self::$db=new PDO($config['dns'],$config['username'],$config['password'],$config['options'],); //si va a cercare il nome di una variabile statica dentro la stessa classe
            }catch (PDOException $e){
                logError($e);
            }

        }

        return self::$db;
    }
}