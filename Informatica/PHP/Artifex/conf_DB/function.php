<?php
//=============FUNZIONE LOG===============
function logError(Exception $exception):void{
    echo "Errore nel database";
    error_log($exception->getMessage().'***'.date('Y-m-d:i:s')."\n",message_type: 3,destination: 'log/dberror.log');
}