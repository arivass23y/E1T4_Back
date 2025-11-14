<?php

class Ekipamendua {
    private $db;

    public function __construct($db) { //Bd-arekin konexioa hartzen du.
        $this->db = $db;
    }

    //GET
    public function getEkipamenduak() { //Ekipamendu guztiak hartu 
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM ekipamendua");
        if ($emaitza === false ||$emaitza->num_rows === 0) { //Emaitzak ez badaude, null bidaltzen du
            return null;
        }
        return $emaitza->fetch_all(MYSQLI_ASSOC);
    }

    public function getEkipamendua($id) { //Ekipamendu bat hartu, id-aren bidez
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM ekipamendua WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $emaitza = $stmt->get_result();

        if (!$emaitza || $emaitza->num_rows === 0) { //Emaitza es badago edo 0 filak badaude NULL bueltatzen du.
            return null; 
        }

        return $emaitza->fetch_assoc();
    }

    //POST
    public function createEkipamendua($izena,$deskribapena,$marka,$modelo,$stock,$idKategoria) { //Ekipamendua sortu
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO ekipamendua(izena,deskribapena,marka,modelo,stock,idKategoria) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssii", $izena, $deskribapena, $marka, $modelo, $stock, $idKategoria);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //PUT
    public function updateEkipamendua($id,$izena,$deskribapena,$marka,$modelo,$stock,$idKategoria){ //Ekipamendua eguneratu
        $stmt = $this->db->getKonexioa()->prepare("UPDATE ekipamendua SET izena=?, deskribapena=?, marka=?, modelo=?, stock=?, idKategoria=? WHERE id=?");
        $stmt->bind_param("ssssiii", $izena,$deskribapena,$marka,$modelo,$stock,$idKategoria,$id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function updateEkipamenduaStock($id,$stock){ //Ekipamenduaren stock-a bakarrik eguneratu
        $stmt = $this->db->getKonexioa()->prepare("UPDATE ekipamendua SET stock=? WHERE id=?");
        $stmt->bind_param("si", $stock,$id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //DELETE
    public function deleteEkipamendua($id){ //Ekipamendua ezabatu id-aren bidez
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM ekipamendua WHERE id=?");
        $stmt->bind_param("i", $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}