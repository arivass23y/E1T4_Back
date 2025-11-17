<?php
require '../Klaseak/DB.php';
require '../Klaseak/gela.php';
require '../Utils/utils.php';
require '../Klaseak/erabiltzailea.php';
//BD-arekin konexioa egin
$db = new DB();
$db->konektatu();
$gelaDB = new Gela($db); //CRUD egiteko klasea
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
$taldea=Utils::stringValidazioa($_POST['taldea'] ?? null);

if($method === 'POST'){
    switch ($metodo) {
        case 'POST': //Gela sortu nahi bada
           if (empty($izena)|| empty($taldea)) { //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "Izena eta taldea bete behar dira"]);
                die();
            }
            if ($gelaDB->createGela($izena,$taldea)) {
                echo json_encode(["success" => "Gela sortuta"]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(500);
                echo json_encode(["error" => "Errorea gela sortzean"]);
            }
        break;
        case 'GET': //Gelak lortu nahi bada
            if(empty($id)){ //Id-a bidaltzen ez bada, gelak guztiak lortu
                $emaitza=$gelaDB->getGelak();
            }else{ //Bestela, gela bakarra lortu
                $emaitza=$gelaDB->getGela($id);
            }
             if ($emaitza === null) { //Emaitza hutsik badago, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ekipamendua ez da aurkitu"]);
            } else {
                echo json_encode($emaitza);
            }
        break;
        case 'PUT': //Gela aldatu nahi bada
             if (empty($id) || empty($izena)) { //Id eta izena bidaltzen ez bada, errore mezua
                http_response_code(400);
                echo json_encode(["error" => "ID eta izena derrigorrezkoak dira"]);
                die();
            }

            if ($gelaDB->updateGela($izena, $id, $taldea)) {
                echo json_encode(["success" => "Gela eguneratuta"]);
            } else {  //Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Gela ez da existitzen"]);
            }
        break;
        case 'DEL': //Gela ezabatu nahi bada
            if (empty($id)) { //Id-a ez badago, errore mezua
                http_response_code(400);
                echo json_encode(["error" => "ID falta da"]);
                die();
            }

            if ($gelaDB->deleteGela($id)) { //Gela ezabatuta badago, mezua bidaltzen da
                echo json_encode(["success" => "Gela ezabatuta"]);
            } else { //Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu gela hori"]);
            }
        break;
        default: //Metodoa ez badago onartuta
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}