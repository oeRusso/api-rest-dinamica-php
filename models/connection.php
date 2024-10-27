<?php

class Connection
{

    // Informacion de la bd
    static public function infoDatabase()
    {
        $infoDB = array(
            "database" => "employees",
            "user" => "root",
            "pass" => ""
        );

        return $infoDB;
    }

    static public function connect()
    {
        try {
            $link = new PDO(
                "mysql:host=localhost;dbname=" . Connection::infoDatabase()["database"],
                Connection::infoDatabase()["user"],
                Connection::infoDatabase()["pass"]
            );

            $link->exec("set names utf8");
        } catch (PDOException $e) {
            die("Error: " .$e->getMessage());
        }

        return $link;
    }
}
