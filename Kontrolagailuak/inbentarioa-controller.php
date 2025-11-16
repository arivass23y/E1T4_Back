<?php
require '../Klaseak/DB.php';
require '../Klaseak/inbentarioa.php';
require '../Klaseak/erabiltzailea.php';
require '../Utils/utils.php';

//BD-arekin konexioa egin
$db = new DB();
$db->konektatu();
$inbentarioaDB = new Inbentarioa($db); //CRUD egiteko klasea
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
$idinbentarioa=Utils::intValidazioa($_POST['idEkipamendu'] ?? null);
$erosketaData=$_POST['erosketaData'] ?? null;
$erosketaData = DateTime::createFromFormat('Y-m-d', $erosketaData);

if($method === 'POST'){
    switch ($metodo) {
        case 'POST': //Inbentarioa sortu nahi bada
           if (empty($etiketa) || empty($idinbentarioa) || empty($erosketaData)) { //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "Etiketa, idinbentarioa eta erosketaData bete behar dira"]);
                die();
            }
            if ($inbentarioaDB->createinbentarioa($etiketa,$idinbentarioa,$erosketaData)) {
                echo json_encode(["success" => "inbentarioa sortuta"]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(500);
                echo json_encode(["error" => "Errorea inbentarioa sortzean"]);
            }
        break;
        case 'GET': //Inbentarioak lortu nahi bada
            if(empty($etiketa)){ //Etiketa bidaltzen ez bada, inbentario guztiak lortu
                $emaitza=$inbentarioaDB->getinbentarioak();
            }else{ //Bestela, inbentario bakarra lortu
                $emaitza=$inbentarioaDB->getinbentarioa($etiketa);
            }
            if ($emaitza === null) { //Emaitza hutsik badago, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ekipamendua ez da aurkitu"]);
            } else {
                echo json_encode($emaitza);
            }
        break;
        case 'PUT': //Inbentarioa aldatu nahi bada
             if (empty($etiketa) || empty($idinbentarioa) || empty($erosketaData)) { //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "Etiketa, idinbentarioa eta erosketaData derrigorrezkoak dira"]);
                die();
            }

            if ($inbentarioaDB->updateinbentarioa($etiketa, $idinbentarioa, $erosketaData)) {
                echo json_encode(["success" => "inbentarioa eguneratuta"]);
            } else { //Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "inbentarioa ez da existitzen"]);
            }
        break;
        case 'DEL': //Inbentarioa ezabatu nahi bada
            if (empty($etiketa)) { //Etiketa ez badago, errore mezua
                http_response_code(400);
                echo json_encode(["error" => "etiketa falta da"]);
                die();
            }

            if ($inbentarioaDB->deleteinbentarioa($etiketa)) { //Inbentarioa ezabatu
                echo json_encode(["success" => "inbentarioa ezabatuta"]);
            } else { //Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu inbentarioa hori"]);
            }
        default: //Metodoa ez badago onartuta
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}