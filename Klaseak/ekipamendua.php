<?php

class Ekipamendua {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getEkipamenduak() {
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM ekipamendua");
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

    public function getEkipamendua($id){
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM ekipamendua WHERE id = ?");
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

    public function createEkipamendua($izena,$deskribapena,$marka,$modelo,$stock,$idKategoria) {
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO ekipamendua(izena,deskribapena,marka,modelo,stock,idKategoria) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("sssssi", $izena, $deskribapena, $marka, $modelo, $stock, $idKategoria);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function updateEkipamendua($id,$izena,$deskribapena,$marka,$modelo,$stock,$idKategoria){
        $stmt = $this->db->getKonexioa()->prepare("UPDATE ekipamendua SET izena=?, deskribapena=?, marka=?, modelo=?, stock=?, idKategoria=? WHERE id=?");
        $stmt->bind_param("ssssssi", $izena,$deskribapena,$marka,$modelo,$stock,$idKategoria,$id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function updateEkipamenduaStock($id,$stock){
        $stmt = $this->db->getKonexioa()->prepare("UPDATE ekipamendua SET stock=? WHERE id=?");
        $stmt->bind_param("si", $stock,$id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function deleteEkipamendua($id){
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM ekipamendua WHERE id=?");
        $stmt->bind_param("i", $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}