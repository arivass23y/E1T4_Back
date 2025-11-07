<?php
require '../Klaseak/DB.php';
require '../Klaseak/erabiltzailea.php';
require '../Utils/utils.php';

//BD-arekin konexioa egin
$db = new DB();
$db->konektatu();
$ErabiltzaileaDB = new Erabiltzailea($db); //CRUD egiteko klasea

$apiKey = $_POST['HTTP_APIKEY'] ?? ''; //ApiKey hartzen du
$emaitza=$ErabiltzaileaDB->getErabiltzaileaByCredentials($apiKey); //ApiKey ondo badago konprobatzen du

$method = $_SERVER['REQUEST_METHOD']; //HTTP metodoa lortu
$metodo = $_POST['_method'] ?? $method; //Metodoa lortu, _method aldagaiaren bidez edo bestela HTTP metodoa bera

//ApiKey balidatu, baina POST edo LOGIN metodoetarako ez da beharrezkoa
if ((!$emaitza|| $emaitza->num_rows === 0) && ($metodo !== 'POST'|| $metodo !== 'LOGIN')) {
    echo 'ERROREA: Ez daukazu gaitasunak, mesedez .';
    die();
}

//Bidalitako aldagaiak mota egokian dauden balidatu
$nan=Utils::stringValidazioa($_POST['nan'] ?? null);
$izena=Utils::stringValidazioa($_POST['izena'] ?? null);
$abizena=Utils::stringValidazioa($_POST['abizena'] ?? null);
$erabiltzailea=Utils::stringValidazioa($_POST['erabiltzailea'] ?? null);
$pasahitza=Utils::stringValidazioa($_POST['pasahitza'] ?? null);
$rola=Utils::charValidazioa($_POST['rola'] ?? null);


if($method === 'POST'){
    switch ($metodo) {
        case 'POST': //Erabiltzailea sortu nahi bada
            if (!empty($pasahitza)) { //Pasahitza huts ez badago, hash-eatu
                $hash = password_hash($pasahitza, PASSWORD_BCRYPT,['cost' => 12]);
            }    
            if (empty($nan) || empty($hash) || empty($erabiltzailea) || empty($rola) || empty($izena) || empty($abizena)) {  //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "Kanpo guztiak bete behar dira"]);
                die();
            }
            $emaitza=$ErabiltzaileaDB->createErabiltzailea($nan, $izena, $abizena, $erabiltzailea, $hash, $rola);
            if ($emaitza) { //Ondo sortuta badago, mezua bidaltzen da
                echo json_encode(["success" => "Erabiltzailea sortuta",
                    "apiKey" => $erabiltzailea
                ]);
            } else { // Errorea gertatzen bada, errore mezua bidaltzen da
                http_response_code(500);
                echo json_encode(["error" => "Errorea Erabiltzailea sortzean"]);
            }
        break;
        case 'GET': //Erabiltzaileak lortu nahi bada
            if(empty($erabiltzailea)){  //Erabiltzailea bidaltzen ez bada, erabiltzaile guztiak lortu
                $emaitza=$ErabiltzaileaDB->getErabiltzaileak();
            }else{ //Bestela, erabiltzaile bakarra lortu
                $emaitza=$ErabiltzaileaDB->getErabiltzailea($nan);
            }
            if ($emaitza === null) { //Emaitza hutsik badago, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Ekipamendua ez da aurkitu"]);
            } else {
                echo json_encode($emaitza);
            }
        break;
        case 'PUT': //Erabiltzailea aldatu nahi bada
            if (!empty($pasahitza)) { //Pasahitza huts ez badago, hash-eatu
                $hash = password_hash($pasahitza, PASSWORD_BCRYPT,['cost' => 12]);
            }
            if (empty($nan) || empty($hash) || empty($erabiltzailea) || empty($rola) || empty($izena) || empty($abizena)) { //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "nan eta izena derrigorrezkoak dira"]);
                die();
            }
            if ($ErabiltzaileaDB->updateErabiltzailea($nan, $izena,$abizena,$erabiltzailea,$hash,$rola)) { //Erabiltzailea aldatu
                echo json_encode(["success" => "Erabiltzailea eguneratuta"]);
            } else {    //errorea gertatzen bada, errore mezua bidaltzen du.
                http_response_code(404);
                echo json_encode(["error" => "Erabiltzailea ez da existitzen"]);
            }
        break;
        case 'DEL': //Ezabatu nahi bada erabiltzailea
            if (empty($nan)) {  //Nan-a ez badago, errore mezua
                http_response_code(400);
                echo json_encode(["error" => "nan falta da"]);
                die();
            }

            if ($ErabiltzaileaDB->deleteErabiltzailea($nan)) { //Erabiltzailea ezabatzen da
                echo json_encode(["success" => "Erabiltzailea ezabatuta"]);
            } else { //Errorea badago, errore mezua bidaltzen du
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu Erabiltzailea hori"]);
            }
        case 'LOGIN': //Erabiltzailea login egin nahi badu
            if (empty($erabiltzailea) || empty($pasahitza)) { //Aldagaia guztiak nuloak ez diren konprobatu
                http_response_code(400);
                echo json_encode(["error" => "erabiltzailea eta pasahitza derrigorrezkoak dira"]);
                die();
            }
            $emaitza = $ErabiltzaileaDB->Login($erabiltzailea); //Erabiltzailea existitzen den konprobatzen du eta bere informazioa hartzen du pasahitza hash-eatua barne
            if ($emaitza === null) { //Emaitza hutsik badago, errore mezua bidaltzen da
                http_response_code(404);
                echo json_encode(["error" => "Erabiltzailea ez da aurkitu"]);
            } else {
                if ($emaitza && password_verify($pasahitza, $emaitza['pasahitza'])) { //Psahitza bera dela konprobatzen du
                    echo json_encode(["success" => "Login ondo",
                        "apiKey" => $emaitza['apiKey']
                    ]);
                } else { //Erabiltzailea edo pasahitza okerra badago, errore mezua bidaltzen du
                    http_response_code(401);
                    echo json_encode(["error" => "Erabiltzailea edo pasahitza okerra"]);
                }
         }   
        break;
        default: //Lehenetsiz, erroe mezua bidaltzen du metodoa ez badu onartzen
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}