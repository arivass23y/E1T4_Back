<?php

class DB {
    private $konexioa;
    private $user ;
    private $host;
    private $pass ;
    private $db;
    
    public function __construct() //Klasearen konstruktorea BD-aren datuekin
    {
        $this->user = "root";
        $this->host = "localhost";
        $this->pass = "";
        $this->db = "santurtzibd";
    }

    public function konektatu() { //BD-arekin konektatu
        $this->konexioa = new mysqli($this->host,$this->user,$this->pass,$this->db);  
        if ($this->konexioa->connect_errno) {
            printf("Konexio errorea: %s\n", $this->konexioa->connect_error);
            die();
        }
        else {
            return $this->konexioa;
        }       
    }

    public function getKonexioa() {
        return $this->konexioa;
    }

    public function __destruct() {
        if ($this->konexioa) {
            $this->konexioa->close();
        }
    }
}