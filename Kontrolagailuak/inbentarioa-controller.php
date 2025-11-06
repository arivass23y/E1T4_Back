<?php
require 'DB.php';
require 'inbentarioa.php';
require '../Utils/utils.php';

$db = new DB();
$db->konektatu();
$inbentarioaDB = new Inbentarioa($db);

$method = $_SERVER['REQUEST_METHOD'];
$metodo = $_POST['_method'] ?? $method; 
$etiketa=Utils::stringValidazioa($_POST['etiketa'] ?? null);
$idEkipamendua=Utils::stringValidazioa($_POST['idEkipamendu'] ?? null);
$erosketaData=Utils::dateValidazioa($_POST['erosketaData'] ?? null);

if($method === 'POST'){
    switch ($metodo) {
        case 'POST': 
           if (empty($etiketa) || empty($idEkipamendua) || empty($erosketaData)) {
                http_response_code(400);
                echo json_encode(["error" => "Izena eta taldea bete behar dira"]);
                die();
            }
            if ($inbentarioaDB->createinbentarioa($etiketa,$idEkipamendua,$erosketaData)) {
                echo json_encode(["success" => "inbentarioa sortuta"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Errorea inbentarioa sortzean"]);
            }
        break;
        case 'GET':
            if(empty($etiketa)){
                $emaitza=$inbentarioaDB->getinbentarioak();
            }else{
                $emaitza=$inbentarioaDB->getinbentarioa($etiketa);
            }
            echo json_encode($emaitza);
        break;
        case 'PUT':
             if (empty($etiketa) || empty($idEkipamendua) || empty($erosketaData)) {
                http_response_code(400);
                echo json_encode(["error" => "Etiketa, idEkipamendua eta erosketaData derrigorrezkoak dira"]);
                die();
            }

            if ($inbentarioaDB->updateinbentarioa($etiketa, $idEkipamendua, $erosketaData)) {
                echo json_encode(["success" => "inbentarioa eguneratuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "inbentarioa ez da existitzen"]);
            }
        break;
        case 'DEL':
            if (empty($etiketa)) {
                echo json_encode(["error" => "etiketa falta da"]);
                die();
            }

            if ($inbentarioaDB->deleteinbentarioa($etiketa)) {
                echo json_encode(["success" => "inbentarioa ezabatuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu inbentarioa hori"]);
            }
        default:
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}