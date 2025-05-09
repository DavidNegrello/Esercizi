<?php

namespace App\Controller;
require 'App\Model\Cioccolato.php';
use App\Model\Cioccolato;
class ProductController
{
    //classe per ogni tabella del database
    private Cioccolato $cioccolati;
    public function __construct($db)
    {
        $this->cioccolati = new cioccolato($db);
    }

    function PaginaProdotto():void
    {
        $cioccolati = $this->cioccolati->showAll();
        require 'App/View/products.php';
    }
}