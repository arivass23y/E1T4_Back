<?php
require 'DB.php';
require 'ekipamendua.php';
require '../Utils/utils.php';

//BD-arekin konexioa egin
$db = new DB();
$db->konektatu();
$ekipamenduaDB = new Ekipamendua($db); //CRUD egiteko klasea
$ErabiltzaileaDB = new Erabiltzailea($db); //ApiKey balidatzeko klasea

$apiKey = $_POST['HTTP_APIKEY'] ?? ''; //ApiKey hartzen du
$emaitza=$ErabiltzaileaDB->getErabiltzaileaByCredentials($apiKey); //ApiKey ondo badago konprobatzen du

if (!$emaitza) { //ApiKey ez bada egokia
    http_response_code(401); //Errore mezua ematen du
    echo json_encode(["error" => "API gakoa ez da zuzena edo ez dago baimenduta"]);
    die();
}

//Bidalitako aldagaiak mota egokian dauden balidatu
$id=Utils::intValidazioa($_POST['id'] ?? null);
$izena=Utils::stringValidazioa($_POST['izena'] ?? null);
$deskribapena=Utils::stringValidazioa($_POST['deskribapena'] ?? null);
$marka=Utils::stringValidazioa($_POST['marka'] ?? null);
$modelo=Utils::stringValidazioa($_POST['modelo'] ?? null);
$stock=Utils::stringValidazioa($_POST['stock'] ?? null);
$idKategoria=Utils::stringValidazioa($_POST['idKategoria'] ?? null);

if($method === 'POST'){
    switch ($metodo) {
        case 'POST':  //Ekipamendua sortu nahi bada
           if (empty($izena) || empty($deskribapena) || empty($stock) || empty($idKategoria)) { //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "Izena eta deskribapena, stock eta idKategoria bete behar dira"]);
                die();
            }
            if ($ekipamenduaDB->createEkipamendua($izena,$deskribapena,$marka,$modelo,$stock,$idKategoria)) { //Ondo sortuta badago, mezua bidaltzen da
                echo json_encode(["success" => "Ekipamendua sortuta"]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(500);
                echo json_encode(["error" => "Errorea ekipamendua sortzean"]);
            }
        break;
        case 'GET': //Ekipamenduak lortu nahi bada
            if(empty($id)){ //Id-a bidaltzen ez bada, ekipamendu guztiak lortu
                $emaitza=$ekipamenduaDB->getEkipamenduak();
            }else{ //Bestela, ekipamendu bakarra lortu
                $emaitza=$ekipamenduaDB->getEkipamendua($id);
            }

            if ($emaitza === null) { //Emaitza hutsik badago, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ekipamendua ez da aurkitu"]);
            } else {
                echo json_encode($emaitza);
            }
        break;
        case 'PUT': //Ekipamendua aldatu nahi bada
             if (empty($id) || empty($izena) ||empty($deskribapena) || empty($stock) || empty($idKategoria)) { //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "Izena, deskribapena, stock eta idKategoria derrigorrezkoak dira"]);
                die();
            }

            if ($ekipamenduaDB->updateEkipamendua($id,$izena,$deskribapena,$marka,$modelo,$stock,$idKategoria)) { //Ekipamendua aldatu
                echo json_encode(["success" => "Ekipamendua eguneratuta"]);
            } else { //errorea gertatzen bada, errore mezua bidaltzen du.
                http_response_code(404);
                echo json_encode(["error" => "Ekipamendua ez da existitzen"]);
            }
        break;
        case 'DEL': //Ezabatu nahi bada ekipamendua
            if (empty($id)) { //Id-a ez badago, errore mezua
                echo json_encode(["error" => "ID falta da"]);
                die();
            }

            if ($ekipamenduaDB->deleteEkipamendua($id)) { //Ekipamendua ezabatzen da
                echo json_encode(["success" => "Ekipamendua ezabatuta"]);
            } else { //Errorea badago, errore mezua bidaltzen du
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu ekipamendua hori"]);
            }
        default: //Lehenetsiz, erroe mezua bidaltzen du metodoa ez badu onartzen
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}