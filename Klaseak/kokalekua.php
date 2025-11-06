<?php

class Kokalekua {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getKokalekuak() {
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

    public function getKokalekua($etiketa,$hasieraData){
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM kokalekua WHERE etiketa = ? AND hasieraData = ?");
        $stmt->bind_param("ss", $etiketa,$hasieraData);
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

    public function createKokalekua($etiketa,$idGela,$hasieraData,$amaieraData) {
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO kokalekua(etiketa,idGela,hasieraData,amaieraData) VALUES (?,?,?,?)");
        $stmt->bind_param("siss", $etiketa, $idGela, $hasieraData, $amaieraData);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function updateKokalekua($etiketa,$idGela,$hasieraData,$amaieraData){
        $stmt = $this->db->getKonexioa()->prepare("UPDATE kokalekua SET idGela = ?, hasieraData = ?, amaieraData = ? WHERE etiketa = ?");
        $stmt->bind_param("isss", $idGela, $hasieraData, $amaieraData, $etiketa);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function deleteKokalekua($etiketa,$hasieraData){
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM kokalekua WHERE etiketa = ? AND hasieraData = ?");
        $stmt->bind_param("ss", $etiketa, $hasieraData);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}
?>