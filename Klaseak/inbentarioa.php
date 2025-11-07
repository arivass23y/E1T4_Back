<?php

class Inbentarioa {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // GET
    public function getInbentarioak() { //Inbentario guztiak hartu
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM inbentarioa");
        if ($emaitza === false ||$emaitza->num_rows === 0) { //Emaitzak ez badaude, null bidaltzen du
            return null;
        }
        return $emaitza->fetch_all(MYSQLI_ASSOC);
    }

    public function getInbentarioa($etiketa){ //Gela bat hartu, etiketaren bidez
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM inbentarioa WHERE etiketa = ?");
        $stmt->bind_param("s", $etiketa);
        $stmt->execute();
        $emaitza = $stmt->get_result();
        if (!$emaitza || $emaitza->num_rows === 0) { //Emaitza es badago edo 0 filak badaude NULL bueltatzen du.
            return null; 
        }

        return $emaitza->fetch_assoc();
    }

    //POST
    public function createInbentarioa($etiketa,$idEkipamendua,$erosketaData) { //Inbentarioa sortu
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO inbentarioa(etiketa,idEkipamendu,erosketaData) VALUES (?,?,?)");
        $stmt->bind_param("sis", $etiketa, $idEkipamendua, $erosketaData);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //PUT
    public function updateInbentarioa($etiketa,$idEkipamendua,$erosketaData){ //Inbentarioa eguneratu
        $stmt = $this->db->getKonexioa()->prepare("UPDATE inbentarioa SET idEkipamendua = ?, erosketaData = ? WHere etiketa = ?");
        $stmt->bind_param("sis", $idEkipamendua,$erosketaData,$etiketa);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //DELETE
    public function deleteInbentarioa($etiketa){ //Inbentarioa ezabatu etiketaren bidez
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM inbentarioa WHERE etiketa = ?");
        $stmt->bind_param("s", $etiketa);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}