<?php
require 'DB.php';
require 'Kokalekua.php';
require '../Utils/utils.php';

$db = new DB();
$db->konektatu();
$kokalekuaDB = new Kokalekua($db);

$method = $_SERVER['REQUEST_METHOD'];
$metodo = $_POST['_method'] ?? $method; 
$etiketa=Utils::intValidazioa($_POST['etiketa'] ?? null);
$idGela=Utils::stringValidazioa($_POST['idGela'] ?? null);
$hasieraData=Utils::dateValidazioa($_POST['hasieraData'] ?? null);
$amaieraData=Utils::dateValidazioa($_POST['amaieraData'] ?? null);

if($method === 'POST'){
    switch ($metodo) {
        case 'POST': 
           if (empty($etiketa) || empty($idGela) || empty($hasieraData)) {
                http_response_code(400);
                echo json_encode(["error" => "Etiketa, idGela eta hasieraData bete behar dira"]);
                die();
            }
            if ($kokalekuaDB->createKokalekua($etiketa,$idGela,$hasieraData,$amaieraData)) {
                echo json_encode(["success" => "Kokalekua sortuta"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Errorea Kokalekua sortzean"]);
            }
        break;
        case 'GET':
            if(empty($id)){
                $emaitza=$kokalekuaDB->getKokalekuak();
            }else{
                $emaitza=$kokalekuaDB->getKokalekua($etiketa,$hasieraData);
            }
            echo json_encode($emaitza);
        break;
        case 'PUT':
             if (empty($etiketa) || empty($idGela) || empty($hasieraData)) {
                http_response_code(400);
                echo json_encode(["error" => "ID eta izena derrigorrezkoak dira"]);
                die();
            }

            if ($kokalekuaDB->updateKokalekua($etiketa,$idGela,$hasieraData,$amaieraData)) {
                echo json_encode(["success" => "Kokalekua eguneratuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Kokalekua ez da existitzen"]);
            }
        break;
        case 'DEL':
            if (empty($id)) {
                echo json_encode(["error" => "ID falta da"]);
                die();
            }

            if ($kokalekuaDB->deleteKokalekua($etiketa,$hasieraData)) {
                echo json_encode(["success" => "Kokalekua ezabatuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu Kokalekua hori"]);
            }
        default:
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}