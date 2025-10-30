<?php

class Utils
{
    public static function intValidazioa($input)
    {
        if ($input !== null && !is_numeric($input)) {
            echo "Error: no es un número";
            die();
        }
    }
    public static function dateValidazioa($input)
    {
        if ($input !== null && !DateTime::createFromFormat('Y-m-d', $input)) {
            echo "Error: formato de fecha inválido";
            die();
        }
    }
    public static function stringValidazioa($input)
    {
        if ($input !== null && !is_string($input)) {
            echo "Error: no es una cadena de texto";
            die();
        }
    }
}