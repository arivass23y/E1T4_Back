<?php

class Erabiltzailea {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    //GET
    public function getErabiltzaileak(){ //Erabiltzaile guztiak hartu
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM erabiltzailea");
        if ($emaitza === false ||$emaitza->num_rows === 0) { //Emaitzak ez badaude, null bidaltzen du
            return null;
        }
        return $emaitza->fetch_all(MYSQLI_ASSOC);
    }

    public function getErabiltzailea($nan) { //Erabiltzaile bat hartu, nan-aren bidez
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM erabiltzailea WHERE nan = ?");
        $stmt->bind_param("s", $nan);
        $stmt->execute();
        $emaitza = $stmt->get_result();
        if (!$emaitza || $emaitza->num_rows === 0) { //Emaitza es badago edo 0 filak badaude NULL bueltatzen du.
            return null; 
        }

        return $emaitza->fetch_assoc();
    }

    public function getErabiltzaileaByCredentials($apiKey) { // Berrikusi ea ApiKey-a baliozkoa den eta itzuli API Key-ren erabiltzailearen baimenak
 
        $stmt = $this->db->getKonexioa()->prepare("SELECT rola FROM erabiltzailea WHERE api_key = ?");
        $stmt->bind_param("s", $apiKey);
        $stmt->execute();
        $emaitza = $stmt->get_result();
         if (!$emaitza || $emaitza->num_rows === 0) { //Emaitza es badago edo 0 filak badaude NULL bueltatzen du.
            return null; 
        }

        return $emaitza->fetch_assoc();
    }

    public function Login($erabiltzailea,$pasahitza){ // Begiratu erabiltzailea eta pasahitza zuzenak diren webgunera sartzeko eta Api Key emateko
        $stmt = $this->db->getKonexioa()->prepare("SELECT apiKey,rola FROM erabiltzailea WHERE erabiltzailea = ? AND pasahitza=?");
        $stmt->bind_param("ss", $erabiltzailea,$pasahitza);
        $stmt->execute();
        $emaitza = $stmt->get_result();

        if (!$emaitza || $emaitza->num_rows === 0) { //Emaitza es badago edo 0 filak badaude NULL bueltatzen du.
            return null; 
        }

        return $emaitza->fetch_assoc();
    }

    //POST
    public function createErabiltzailea($nan,$izena,$abizena,$erabiltzailea,$pasahitza,$rola) { //Erabiltzailea sortu
        $randomBytes = random_bytes(32); //32 ausazko byte sortzen ditu
        $key = bin2hex($randomBytes); //Byte bitar horiek kate hamaseitar irakurgarri bihurtzen ditu

        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO erabiltzailea(nan,izena,abizena,erabiltzailea,pasahitza,rola) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $nan,$izena,$abizena,$erabiltzailea,$pasahitza,$rola);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
    
    //PUT
    public function updateErabiltzailea($nan,$izena,$abizena,$erabiltzailea,$pasahitza,$rola){ //Erabiltzailea eguneratu
        $stmt = $this->db->getKonexioa()->prepare("UPDATE erabiltzailea SET izena=?, abizena=?, erabiltzailea=?, pasahitza=?, rola=? WHERE nan=?");
        $stmt->bind_param("ssssss",$izena,$abizena,$erabiltzailea,$pasahitza,$rola,$nan);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //DELETE
    public function deleteErabiltzailea($nan){ //Erabiltzailea ezabatu nan-aren bidez
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM erabiltzailea WHERE nan=?");
        $stmt->bind_param("s", $nan);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}