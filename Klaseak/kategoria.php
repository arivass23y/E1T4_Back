<?php

class Kategoria {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    //GET
    public function getKategoriak() { //Kategoria guztiak hartu
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM kategoria");
        if ($emaitza === false ||$emaitza->num_rows === 0) { //Emaitzak ez badaude, null bidaltzen du
            return null;
        }
        return $emaitza->fetch_all(MYSQLI_ASSOC);
    }

    public function getKategoria($id){ //Kategoria bat hartu, id-aren bidez
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM kategoria WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $emaitza = $stmt->get_result();
        if (!$emaitza || $emaitza->num_rows === 0) { //Emaitza es badago edo 0 filak badaude NULL bueltatzen du.
            return null; 
        }

        return $emaitza->fetch_assoc();
    }

    //POST
    public function createKategoria($izena) { //Kategoria sortu
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO kategoria(izena) VALUES (?)");
        $stmt->bind_param("s", $izena);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //PUT
    public function updateKategoria($izena,$id){ //Kategoria eguneratu
        $stmt = $this->db->getKonexioa()->prepare("UPDATE kategoria SET izena=? WHERE id=?");
        $stmt->bind_param("si", $izena,$id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //DELETE
    public function deleteKategoria($id){ //Kategoria ezabatu
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM kategoria WHERE id=?");
        $stmt->bind_param("i", $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}