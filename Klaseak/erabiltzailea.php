<?php

class Erabiltzailea {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getErabiltzaileak(){
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM erabiltzailea");
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

    public function getErabiltzailea($id) {
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM erabiltzailea WHERE id = ?");
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

    public function createErabiltzailea($nan,$izena,$abizena,$erabiltzailea,$pasahitza,$rola) {
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO erabiltzailea(nan,izena,abizena,erabiltzailea,pasahitza,rola) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $nan,$izena,$abizena,$erabiltzailea,$pasahitza,$rola);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
    
    public function updateErabiltzailea($nan,$izena,$abizena,$erabiltzailea,$pasahitza,$rola){
        $stmt = $this->db->getKonexioa()->prepare("UPDATE erabiltzailea SET nan=?, izena=?, abizena=?, erabiltzailea=?, pasahitza=?, rola=? WHERE id=?");
        $stmt->bind_param("ssssssi", $nan,$izena,$abizena,$erabiltzailea,$pasahitza,$rola,$id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function deleteErabiltzailea($id){
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM erabiltzailea WHERE id=?");
        $stmt->bind_param("i", $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}