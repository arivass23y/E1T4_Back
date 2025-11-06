<?php
require 'DB.php';
require 'ekipamendua.php';
require '../Utils/utils.php';

$db = new DB();
$db->konektatu();
$ekipamenduaDB = new Ekipamendua($db);

$method = $_SERVER['REQUEST_METHOD'];
$metodo = $_POST['_method'] ?? $method; 
$id=Utils::intValidazioa($_POST['id'] ?? null);
$izena=Utils::stringValidazioa($_POST['izena'] ?? null);
$deskribapena=Utils::stringValidazioa($_POST['deskribapena'] ?? null);
$marka=Utils::stringValidazioa($_POST['marka'] ?? null);
$modelo=Utils::stringValidazioa($_POST['modelo'] ?? null);
$stock=Utils::stringValidazioa($_POST['stock'] ?? null);
$idKategoria=Utils::stringValidazioa($_POST['idKategoria'] ?? null);

if($method === 'POST'){
    switch ($metodo) {
        case 'POST': 
           if (empty($izena) || empty($deskribapena) || empty($stock) || empty($idKategoria)) {
                http_response_code(400);
                echo json_encode(["error" => "Izena eta deskribapena, stock eta idKategoria bete behar dira"]);
                die();
            }
            if ($ekipamenduaDB->createEkipamendua($izena,$deskribapena,$marka,$modelo,$stock,$idKategoria)) {
                echo json_encode(["success" => "Ekipamendua sortuta"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Errorea ekipamendua sortzean"]);
            }
        break;
        case 'GET':
            if(empty($id)){
                $emaitza=$ekipamenduaDB->getEkipamenduak();
            }else{
                $emaitza=$ekipamenduaDB->getEkipamendua($id);
            }
            echo json_encode($emaitza);
        break;
        case 'PUT':
             if (empty($id) || empty($izena) ||empty($deskribapena) || empty($stock) || empty($idKategoria)) {
                http_response_code(400);
                echo json_encode(["error" => "Izena, deskribapena, stock eta idKategoria derrigorrezkoak dira"]);
                die();
            }

            if ($ekipamenduaDB->updateEkipamendua($id,$izena,$deskribapena,$marka,$modelo,$stock,$idKategoria)) {
                echo json_encode(["success" => "Ekipamendua eguneratuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Ekipamendua ez da existitzen"]);
            }
        break;
        case 'DEL':
            if (empty($id)) {
                echo json_encode(["error" => "ID falta da"]);
                die();
            }

            if ($ekipamenduaDB->deleteEkipamendua($id)) {
                echo json_encode(["success" => "Ekipamendua ezabatuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu ekipamendua hori"]);
            }
        default:
            http_response_code(405);
            echo json_encode(["error" => "Metodoa ez da onartzen"]);
        break;
    }
}