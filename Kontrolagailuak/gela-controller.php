<?php
require 'DB.php';
require 'gela.php';
require '../Utils/utils.php';

$db = new DB();
$db->konektatu();
$gelaDB = new Gela($db);

$method = $_SERVER['REQUEST_METHOD'];
$metodo = $_POST['_method'] ?? $method; 
$id=Utils::intValidazioa($_POST['id'] ?? null);
$izena=Utils::stringValidazioa($_POST['izena'] ?? null);
$taldea=Utils::stringValidazioa($_POST['taldea'] ?? null);

if($method === 'POST'){
    switch ($metodo) {
        case 'POST': 
           if (empty($izena) || empty($taldea)) {
                http_response_code(400);
                echo json_encode(["error" => "Izena eta taldea bete behar dira"]);
                die();
            }
            if ($gelaDB->createGela($izena,$taldea)) {
                echo json_encode(["success" => "Gela sortuta"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Errorea gela sortzean"]);
            }
        break;
        case 'GET':
            if(empty($id)){
                $emaitza=$gelaDB->getGelak();
            }else{
                $emaitza=$gelaDB->getGela($id);
            }
            echo json_encode($emaitza);
        break;
        case 'PUT':
             if (empty($id) || empty($izena)) {
                http_response_code(400);
                echo json_encode(["error" => "ID eta izena derrigorrezkoak dira"]);
                die();
            }

            if ($gelaDB->updateGela($izena, $id, $taldea)) {
                echo json_encode(["success" => "Gela eguneratuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Gela ez da existitzen"]);
            }
        break;
        case 'DEL':
            if (empty($id)) {
                echo json_encode(["error" => "ID falta da"]);
                die();
            }

            if ($gelaDB->deleteGela($id)) {
                echo json_encode(["success" => "Gela ezabatuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu gela hori"]);
            }
        default:
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}