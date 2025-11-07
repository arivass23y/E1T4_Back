<?php
require 'DB.php';
require 'Erabiltzailea.php';
require '../Utils/utils.php';

$db = new DB();
$db->konektatu();
$ErabiltzaileaDB = new Erabiltzailea($db);

$apiKey = $_POST['HTTP_APIKEY'] ?? '';
$emaitza=$ErabiltzaileaDB->getErabiltzaileaByCredentials($apiKey);

$method = $_SERVER['REQUEST_METHOD'];
$metodo = $_POST['_method'] ?? $method; 


if (!$emaitza && $metodo !== 'POST') {
    echo 'ERROREA: Ez daukazu gaitasunak, mesedez .';
    die();
}

$nan=Utils::stringValidazioa($_POST['nan'] ?? null);
$izena=Utils::stringValidazioa($_POST['izena'] ?? null);
$abizena=Utils::stringValidazioa($_POST['abizena'] ?? null);
$erabiltzailea=Utils::stringValidazioa($_POST['erabiltzailea'] ?? null);
$pasahitza=Utils::stringValidazioa($_POST['pasahitza'] ?? null);
$rola=Utils::charValidazioa($_POST['rola'] ?? null);


if($method === 'POST'){
    switch ($metodo) {
        case 'POST': 
            if (!empty($pasahitza)) {
                $hash = password_hash($pasahitza, PASSWORD_BCRYPT,['cost' => 12]);
            }    
            if (empty($nan) || empty($hash) || empty($erabiltzailea) || empty($rola) || empty($izena) || empty($abizena)) {
                http_response_code(400);
                echo json_encode(["error" => "Kanpo guztiak bete behar dira"]);
                die();
            }
            $emaitza=$ErabiltzaileaDB->createErabiltzailea($nan, $izena, $abizena, $erabiltzailea, $hash, $rola);
            if ($emaitza) {
                echo json_encode(["success" => "Erabiltzailea sortuta",
                    "apiKey" => $erabiltzailea
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Errorea Erabiltzailea sortzean"]);
            }
        break;
        case 'GET':
            if(empty($erabiltzailea)){
                $emaitza=$ErabiltzaileaDB->getErabiltzaileak();
            }else{
                $emaitza=$ErabiltzaileaDB->getErabiltzailea($nan);
            }
            echo json_encode($emaitza);
        break;
        case 'PUT':
            if (!empty($pasahitza)) {
                $hash = password_hash($pasahitza, PASSWORD_BCRYPT,['cost' => 12]);
            }
            if (empty($nan) || empty($hash) || empty($erabiltzailea) || empty($rola) || empty($izena) || empty($abizena)) {
                http_response_code(400);
                echo json_encode(["error" => "nan eta izena derrigorrezkoak dira"]);
                die();
            }
            if ($ErabiltzaileaDB->updateErabiltzailea($nan, $izena,$abizena,$erabiltzailea,$hash,$rola)) {
                echo json_encode(["success" => "Erabiltzailea eguneratuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Erabiltzailea ez da existitzen"]);
            }
        break;
        case 'DEL':
            if (empty($nan)) {
                echo json_encode(["error" => "nan falta da"]);
                die();
            }

            if ($ErabiltzaileaDB->deleteErabiltzailea($nan)) {
                echo json_encode(["success" => "Erabiltzailea ezabatuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu Erabiltzailea hori"]);
            }
        case 'LOGIN':
            if (empty($erabiltzailea) || empty($pasahitza)) {
                http_response_code(400);
                echo json_encode(["error" => "erabiltzailea eta pasahitza derrigorrezkoak dira"]);
                die();
            }
            $emaitza = $ErabiltzaileaDB->Login($erabiltzailea,$pasahitza);
            if ($emaitza && password_verify($pasahitza, $emaitza['pasahitza'])) {
                echo json_encode(["success" => "Login ondo",
                    "apiKey" => $emaitza['apiKey']
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["error" => "Erabiltzailea edo pasahitza okerra"]);
            }
        default:
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}