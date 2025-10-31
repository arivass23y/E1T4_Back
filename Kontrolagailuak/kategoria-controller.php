<?php
require 'DB.php';
require 'kategoria.php';
require '../Utils/utils.php';

$db = new DB();
$db->konektatu();
$kategoriaDB = new Kategoria($db);

$method = $_SERVER['REQUEST_METHOD'];
$metodo = $_POST['_method'] ?? $method; 
$id=Utils::intValidazioa($_POST['id'] ?? null);
$izena=Utils::stringValidazioa($_POST['izena'] ?? null);

if($method === 'POST'){
    switch ($metodo) {
        case 'POST': 
           if (empty($id) || empty($izena)) {
                http_response_code(400);
                echo json_encode(["error" => "ID eta izena bete behar dira"]);
                die();
            }
            if ($kategoriaDB->createKategoria($id, $izena)) {
                echo json_encode(["success" => "Kategoria sortuta"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Errorea kategoria sortzean"]);
            }
        break;
        case 'GET':
            if(empty($id)){
                $emaitza=$kategoriaDB->getKategoriak();
            }else{
                $emaitza=$kategoriaDB->getKategoria($id);
            }
            echo json_encode($emaitza);
        break;
        case 'PUT':
             if (empty($id) || empty($izena)) {
                http_response_code(400);
                echo json_encode(["error" => "ID eta izena derrigorrezkoak dira"]);
                die();
            }

            if ($kategoriaDB->updateKategoria($izena, $id)) {
                echo json_encode(["success" => "Kategoria eguneratuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Kategoria ez da existitzen"]);
            }
        break;
        case 'DEL':
            if (empty($id)) {
                echo json_encode(["error" => "ID falta da"]);
                die();
            }

            if ($kategoriaDB->deleteKategoria($id)) {
                echo json_encode(["success" => "Kategoria ezabatuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu kategoria hori"]);
            }
        default:
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}