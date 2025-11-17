<?php
require '../Klaseak/DB.php';
require '../Klaseak/kategoria.php';
require '../Klaseak/erabiltzailea.php';
require '../Utils/utils.php';

//BD-arekin konexioa egin
$db = new DB();
$db->konektatu();
$kategoriaDB = new Kategoria($db); //CRUD egiteko klasea
$ErabiltzaileaDB = new Erabiltzailea($db); //ApiKey balidatzeko klasea

$method = $_SERVER['REQUEST_METHOD']; //HTTP metodoa lortu
$metodo = $_POST['_method'] ?? $method; //Metodoa lortu, _method aldagaiaren bidez edo bestela HTTP metodoa bera

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

if($method === 'POST'){
    switch ($metodo) {
        case 'POST': //Kategoria sortu nahi bada
           if (empty($izena)) { //Izena bidaltzen ez bada, errore mezua
                http_response_code(400);
                echo json_encode(["error" => "Izena derrigorrezkoa da"]);
                die();
            }
            if ($kategoriaDB->createKategoria($izena)) { //Ondo sortuta badago, mezua bidaltzen da
                echo json_encode(["success" => "Kategoria sortuta"]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(500);
                echo json_encode(["error" => "Errorea kategoria sortzean"]);
            }
        break;
        case 'GET': //Kategoria lortu nahi bada
            if(empty($id)){ //Id-a bidaltzen ez bada, kategoriak guztiak lortu
                $emaitza=$kategoriaDB->getKategoriak();
            }else{ //Bestela, kategoria bakarra lortu
                $emaitza=$kategoriaDB->getKategoria($id);
            }
            if ($emaitza === null) { //Emaitza hutsik badago, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ekipamendua ez da aurkitu"]);
            } else {
                echo json_encode($emaitza);
            }
        break;
        case 'PUT': //Kategoria aldatu nahi bada
             if (empty($id) || empty($izena)) { //Id-a edo izena bidaltzen ez bada, errore mezua
                http_response_code(400);
                echo json_encode(["error" => "ID eta izena derrigorrezkoak dira"]);
                die();
            }

            if ($kategoriaDB->updateKategoria($izena, $id)) { //Ondo eguneratuta badago, mezua bidaltzen da
                echo json_encode(["success" => "Kategoria eguneratuta"]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Kategoria ez da existitzen"]);
            }
        break;
        case 'DEL': //Kategoria ezabatu nahi bada
            if (empty($id)) { //ID ez badago, errore mezua
                http_response_code(400);
                echo json_encode(["error" => "ID falta da"]);
                die();
            }

            if ($kategoriaDB->deleteKategoria($id)) {   //Ondo ezabatuta badago, mezua bidaltzen da
                echo json_encode(["success" => "Kategoria ezabatuta"]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu kategoria hori"]);
            }
        break;
        default: //Metodoa ez badago onartuta
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}