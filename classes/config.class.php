<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: config.class.php
 * Desc: Singleton Class Config for Projekt
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

require_once __DIR__ . "/../config.php";

/*
 * Singleton config class using values from config.php
 * */
class Config {

    private static $instance;
    private $cfg;

    private function __construct($cfg)
    {
        $this->cfg = $cfg;
    }

    public static function getInstance(){
        if(self::$instance == null){
            $cfg = require(__DIR__ . "/../config.php");
            self::$instance = new Config($cfg);
        }

        return self::$instance;
    }

    public function getDbDsn(){
        $dsn = "host=" . $this->getHost() .
            " port=" . $this->getPort() .
            " dbname=" . $this->getDBName() .
            " user=" . $this->getUser() .
            " password=" . $this->getPw();

        return $dsn;
    }

    private function getHost(){
        return $this->cfg["db"]["host"];
    }

    private function getPort(){
        return $this->cfg["db"]["port"];
    }

    private function getDBName(){
        return $this->cfg["db"]["dbName"];
    }

    private function getUser(){
        return $this->cfg["db"]["user"];
    }

    private function getPw(){
        return $this->cfg["db"]["pw"];
    }

    public function getLinks(Role $role = null){
        if(!$role){
            return $this->cfg["default_link_array"];
        }

        switch($role->getRole()){
            case "member":
                return $this->cfg["member_link_array"];
                break;
            case "admin":
                return $this->cfg["admin_link_array"];
                break;
        }
    }
}