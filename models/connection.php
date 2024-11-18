<?php

class Connection
{

    /*======================
      informacion de la bd
   ===========================*/
    static public function infoDatabase()
    {
        $infoDB = array(
            "database" => "employees",
            "user" => "root",
            "pass" => ""
        );

        return $infoDB;
    }

     /*============================
       Conexion a la base de datos
     ==============================*/
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

    /*=========================================
     validar existencia de un tabla en la bd
     =========================================*/

     static public function getColumsData($table, $columns){
        
        $database = Connection::infoDatabase()["database"];

        $validate = Connection::connect()
        ->query("SELECT COLUMN_NAME AS item FROM information_schema.columns WHERE table_schema = '$database' AND table_name = '$table'")
        ->fetchAll(PDO::FETCH_OBJ);

        if (empty($validate)) {
            return null;
        }else {

            $sum= 0;
            foreach ($validate as $key => $value) {

                $sum+= in_array($value->item,$columns);
               
            }

            echo '<pre>'; print_r($sum); echo '</pre>';
            count($columns);
            echo '<pre>'; print_r( count($columns)); echo '</pre>';
        }
     }

}
