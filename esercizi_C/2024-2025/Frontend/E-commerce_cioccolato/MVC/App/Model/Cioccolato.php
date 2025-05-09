<?php

namespace App\Model;
use Exception;
use PDO;

class Cioccolato
{
    private PDO $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    //CRUD database
    public function showAll(): array
    {
        try {
        $lista = [];
        $query = "
    SELECT 
        c.id,
        c.nome ,
        t.nome AS tipo_cioccolata,
        c.anno_produzione,
        c.prezzo_vendita,
        c.immagine
    FROM cioccolata c
    JOIN tipo_cioccolata t ON c.tipo_id = t.id
    ORDER BY c.id
    limit 10
";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            while ($product = $stmt->fetch()) {
                $lista[] = $product;
            }
            $stmt->closeCursor();
        } catch (Exception $e) {
        header('location: ../View/error_page.php');
        }
        return $lista;
    }

}