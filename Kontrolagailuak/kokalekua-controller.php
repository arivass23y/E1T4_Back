<?php
require '../Klaseak/DB.php';
require '../Klaseak/kokalekua.php';
require '../Klaseak/erabiltzailea.php';
require '../Utils/utils.php';

//BD-arekin konexioa egin
$db = new DB();
$db->konektatu();
$kokalekuaDB = new Kokalekua($db); //CRUD egiteko klasea
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
$etiketa=Utils::stringValidazioa($_POST['etiketa'] ?? null);
$idGela=Utils::intValidazioa($_POST['idGela'] ?? null);

$hasieraData = $_POST['hasieraData'] ?? null;

if ($hasieraData) {
    $dt = DateTime::createFromFormat('Y-m-d', trim($hasieraData));
    if ($dt !== false) {
        $hasieraData = $dt->format('Y-m-d'); // fecha lista para la BD
    } else {
        echo json_encode(["error" => "Fecha inválida"]);
        exit;
    }
}

$amaieraData=$_POST['amaieraData'] ?? null;

if ($amaieraData) {
    $dt = DateTime::createFromFormat('Y-m-d', trim($amaieraData));
    if ($dt !== false) {
        $amaieraData = $dt->format('Y-m-d'); // fecha lista para la BD
    } else {
        echo json_encode(["error" => "Fecha inválida"]);
        exit;
    }
}

if($method === 'POST'){ 
    switch ($metodo) {
        case 'POST': //Kokalekua sortu nahi bada
           if (empty($etiketa) || empty($idGela) || empty($hasieraData)) { //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "Etiketa, idGela eta hasieraData bete behar dira"]);
                die();
            }
            if ($kokalekuaDB->createKokalekua($etiketa,$idGela,$hasieraData,$amaieraData)) { //Ondo sortuta badago, mezua bidaltzen da
                echo json_encode(["success" => "Kokalekua sortuta"]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(500);
                echo json_encode(["error" => "Errorea Kokalekua sortzean"]);
            }
        break;
        case 'GET': //Kokalekuak lortu nahi bada
            if(empty($etiketa) && empty($hasieraData)){ //Id-a bidaltzen ez bada, kokalekua guztiak lortu
                $emaitza=$kokalekuaDB->getKokalekuak();
            }else{ //Bestela, kokalekua bakarra lortu
                $emaitza=$kokalekuaDB->getKokalekua($etiketa,$hasieraData);
            }

            if ($emaitza === null) { //Emaitza hutsik badago, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ekipamendua ez da aurkitu"]);
            } else {
                echo json_encode($emaitza);
            }
        break;
        case 'PUT': //Kokalekua aldatu nahi bada
             if (empty($etiketa) || empty($idGela) || empty($hasieraData)) { //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "Etiketa, idGela eta hasieraData derrigorrezkoak dira"]);
                die();
            }

            if ($kokalekuaDB->updateKokalekua($etiketa,$idGela,$hasieraData,$amaieraData)) { //Ondo eguneratuta badago, mezua bidaltzen da
                echo json_encode(["success" => "Kokalekua eguneratuta"]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Kokalekua ez da existitzen"]);
            }
        break;
        case 'DEL': //Kokalekua ezabatu nahi bada
            if (empty($etiketa) || empty($hasieraData)) { //Id-a ez badago, errore mezua
                http_response_code(400);
                echo json_encode(["error" => "Etiketa eta hasieraData derrigorrezkoak dira"]);
                die();
            }

            if ($kokalekuaDB->deleteKokalekua($etiketa,$hasieraData)) { //Ondo ezabatuta badago, mezua bidaltzen da
                echo json_encode(["success" => "Kokalekua ezabatuta"]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu Kokalekua hori"]);
            }
        break;
        default: //Metodoa ez badago onartuta   
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}