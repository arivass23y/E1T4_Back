<?php

class Kokalekua {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    //GET
    public function getKokalekuak() { //Kokaleku guztiak hartu
        $emaitza = $this->db->getKonexioa()->query("SELECT * FROM kokalekua");
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

    public function getKokalekua($etiketa,$hasieraData){ //Kokaleku bat hartu, etiketa eta hasieraDataren bidez
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM kokalekua WHERE etiketa = ? AND hasieraData = ?");
        $stmt->bind_param("ss", $etiketa,$hasieraData);
        $stmt->execute();
        $emaitza = $stmt->get_result();
        if (!$emaitza || $emaitza->num_rows === 0) { //Emaitza es badago edo 0 filak badaude NULL bueltatzen du.
            return null; 
        }

        return $emaitza->fetch_assoc();
    }

    //POST
    public function createKokalekua($etiketa,$idGela,$hasieraData) { //Kokalekua sortu
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO kokalekua(etiketa,idGela,hasieraData) VALUES (?,?,?)");
        $stmt->bind_param("sis", $etiketa, $idGela, $hasieraData);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //PUT
    public function updateKokalekua($etiketa,$idGela,$hasieraData,$amaieraData){ //Kokalekua eguneratu
        $stmt = $this->db->getKonexioa()->prepare("UPDATE kokalekua SET idGela = ?, amaieraData = ? WHERE etiketa = ? AND hasieraData = ?");
        $stmt->bind_param("isss", $idGela, $amaieraData, $etiketa, $hasieraData);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    //DELETE
    public function deleteKokalekua($etiketa,$hasieraData){ //Kokalekua ezabatu
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM kokalekua WHERE etiketa = ? AND hasieraData = ?");
        $stmt->bind_param("ss", $etiketa, $hasieraData);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}
?>