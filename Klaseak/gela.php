<?php

class Gela {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    //GET
    public function getGelak(){ //Gelak guztiak hartu
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM gela");
        if ($emaitza === false)||$emaitza->num_rows === 0 { //Emaitzak ez badaude, null bidaltzen du
            return null;
        }
        return $emaitza->fetch_all(MYSQLI_ASSOC);
    }

    public function getGela($id) { //Gela bat hartu, id-aren bidez
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM gela WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $emaitza = $stmt->get_result();
        if (!$emaitza || $emaitza->num_rows === 0) { //Emaitza es badago edo 0 filak badaude NULL bueltatzen du.
            return null; 
        }

        return $emaitza->fetch_assoc();
    }

    //POST
    public function createGela($izena,$taldea) { //Gela sortu
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO gela(izena,taldea) VALUES (?,?)");
        $stmt->bind_param("ss", $izena, $taldea);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //PUT
    public function updateGela($izena,$id,$taldea){ //Gela eguneratu
        $stmt = $this->db->getKonexioa()->prepare("UPDATE gela SET izena=?, taldea=? WHERE id=?");
        $stmt->bind_param("ssi", $izena,$taldea,$id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //DELETE
    public function deleteGela($id){ //Gela ezabatu id-aren bidez
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM gela WHERE id=?");
        $stmt->bind_param("i", $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}