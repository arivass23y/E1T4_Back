<?php

class Utils
{
    public static function intValidazioa($input) //Aldagaia zenbakia dela konprobatzen du
    {
        if ($input !== null && !is_numeric($input)) {
            http_response_code(400);
            echo json_encode(["error" => "Datuaren formatua ez da zuzena"]);
            die();
        }
        else {
            return intval($input);
        }
    }
    public static function dateValidazioa($input) //Aldagaia date dela konprobatzen du
    {
        if ($input !== null && !DateTime::createFromFormat('Y-m-d', $input)) {
            http_response_code(400);
            echo json_encode(["error" => "Datuaren formatua ez da zuzena"]);
            die();
        }
        else {
            return $input;
        }
    }
    public static function stringValidazioa($input) //Aldagaia string dela konprobatzen du
    {
        if ($input !== null && !is_string($input)) {
            http_response_code(400);
            echo json_encode(["error" => "Datuaren formatua ez da zuzena"]);
            die();
        }
        else {
            return $input;
        }
    }
    public static function charValidazioa($input) //Aldagaia char dela konprobatzen du
    {
        if ($input !== null && !is_string($input) && strlen($input) === 1) {
            http_response_code(400);
            echo json_encode(["error" => "Datuaren formatua ez da zuzena"]);
            die();
        }
        else {
            return $input;
        }
    }
}