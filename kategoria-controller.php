<?php
require 'DB.php';
require 'kategoria.php';

// Datu-basearekin erabiltzaileak kudeatzeko objektuak sortu.
$db = new DB();
$db->konektatu();
$kategoriaDB = new Kategoria($db);

$method = $_SERVER['REQUEST_METHOD'];
$metodo = $_POST['_method'] ?? $method;
$id = $_POST['id'] ?? null;
$izena = $_POST['izena'] ?? null;
if($method === 'POST'){
    switch ($metodo) {
        case 'POST': 
           if (empty($id) || empty($izena)) {
                http_response_code(400);
                echo json_encode(["error" => "ID eta izena bete behar dira"]);
                exit();
            }

            if ($kategoriaDB->createKategoria($id, $izena)) {
                echo json_encode(["success" => "Kategoria sortuta"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Errorea kategoria sortzean"]);
            }
        break;
        case 'GET':
            $emaitza=$kategoriaDB->getKategoriak();
            //header('Content-Type: application/json');
            echo json_encode($emaitza);
        break;
        case 'PUT':
             if (empty($id) || empty($izena)) {
                http_response_code(400);
                echo json_encode(["error" => "ID eta izena derrigorrezkoak dira"]);
                exit();
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
                exit();
            }

            if ($kategoriaDB->deleteKategoria($id)) {
                echo json_encode(["success" => "Kategoria ezabatuta"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Ez da aurkitu kategoria hori"]);
            }
        break;
    }
}