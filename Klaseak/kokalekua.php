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

    public function getKokalekua($id){
        $stmt = $this->db->getKonexioa()->prepare("SELECT * FROM kokalekua WHERE id = ?");
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

    public function createKokalekua($etiketa,$idGela,$hasieraData,$amaieraData) {
        $stmt = $this->db->getKonexioa()->prepare("INSERT INTO kokalekua(etiketa,idGela,hasieraData,amaieraData) VALUES (?,?,?,?)");
        $stmt->bind_param("siss", $etiketa, $idGela, $hasieraData, $amaieraData);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function updateKokalekua($etiketa,$idGela,$hasieraData,$amaieraData){
        $stmt = $this->db->getKonexioa()->prepare("UPDATE kokalekua SET etiketa=?, idGela=?, hasieraData=?, amaieraData=? WHERE id=?");
        $stmt->bind_param("sissi", $etiketa, $idGela, $hasieraData, $amaieraData, $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }

    public function deleteKokalekua($id){
        $stmt = $this->db->getKonexioa()->prepare("DELETE FROM kokalekua WHERE id=?");
        $stmt->bind_param("i", $id);
        $emaitza = $stmt->execute();
        $stmt->close();
        return $emaitza;
    }
}
?>