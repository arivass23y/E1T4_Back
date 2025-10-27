<?php

class Kategoria {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getKategoriak() {
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM kategoria");
       if (!$emaitza) { //Emaitzarik ez badago
            echo 'ERROREA: Ezin izan dira datuak eskuratu.';
            die();
        }
        else{
            $taldeak = [];
            while ($row = $emaitza->fetch_assoc()) {$taldeak[] = $row;} // Emaitzaren lerroak array-ean sartu
            return $taldeak;
        }
    }

    public function createKategoria($id,$izena) {
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO kategoria(id,izena) VALUES (?,?)");
        $stmt->bind_param("is", $id, $izena);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function updateKategoria($izena,$id){
        $stmt = $this->db->getKonexioa()->prepare("UPDATE kategoria SET izena=? WHERE id=?");
        $stmt->bind_param("si", $izena,$id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function deleteKategoria($id){
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM kategoria WHERE id=?");
        $stmt->bind_param("i", $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}