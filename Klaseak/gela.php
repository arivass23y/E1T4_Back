<?php

class Gela {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getGelak(){
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM gela");
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

    public function getGela($id) {
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM gela WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $emaitza = $stmt->get_result();
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

    public function createGela($izena,$taldea) {
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO gela(izena,taldea) VALUES (?,?)");
        $stmt->bind_param("ss", $izena, $taldea);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function updateGela($izena,$id,$taldea){
        $stmt = $this->db->getKonexioa()->prepare("UPDATE gela SET izena=?, taldea=? WHERE id=?");
        $stmt->bind_param("ssi", $izena,$taldea,$id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function deleteGela($id){
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM gela WHERE id=?");
        $stmt->bind_param("i", $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}