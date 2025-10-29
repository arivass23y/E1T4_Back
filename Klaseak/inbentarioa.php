<?php

class Inbentarioa {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getInbentarioak() {
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM inbentarioa");
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

    public function getInbentarioa($id){
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM inbentarioa WHERE id = ?");
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

    public function createInbentarioa($etiketa,$idEkipamendua,$erosketaData) {
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO inbentarioa(etiketa,idEkipamendua,erosketaData) VALUES (?,?,?)");
        $stmt->bind_param("sis", $etiketa, $idEkipamendua, $erosketaData);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function updateInbentarioa($etiketa,$idEkipamendua,$erosketaData){
        $stmt = $this->db->getKonexioa()->prepare("UPDATE inbentarioa SET idEkipamendua=?, erosketaData=? WHere etiketa = ?");
        $stmt->bind_param("sis", $idEkipamendua,$erosketaData,$etiketa);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function deleteInbentarioa($id){
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM inbentarioa WHERE id=?");
        $stmt->bind_param("i", $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}