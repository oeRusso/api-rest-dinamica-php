<?php

require_once "connection.php";

class GetModel
{

    /*===================================
       Peticiones GET sin filtro
    ====================================*/
    static public function getData($table, $select, $orderBy, $orderMode, $startAt, $endAt)
    {

        /*========================================================
          validar existencia de un tabla y de las columnas en la bd
        ==========================================================*/
       $selectArray = explode(',',$select);

       echo '<pre>'; print_r(Connection::getColumsData($table, $selectArray)); echo '</pre>';

       return;
        if (empty(Connection::getColumsData($table, $selectArray))) {
           
            return null;
        }


        /*===================================
            Sin ordenar y sin limitar datos
        ====================================*/

        $sql = "SELECT $select FROM $table";


        /*===================================
              Ordenar datos sin limites
        ====================================*/


        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

            $sql = "SELECT $select FROM $table ORDER BY $orderBy $orderMode";
        }

        /*===================================
               ordenar y limitar datos
        ====================================*/

        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table ORDER BY $orderBy $orderMode LIMIT $startAt,$endAt";
        }

        /*===================================
               Limitar datos sin ordenar
        ====================================*/

        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table LIMIT $startAt,$endAt";
        }


        $stmt = Connection::connect()->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /*===================================
             Peticiones GET con filtro 
     ====================================*/

    static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {

        /*=========================================
            validar existencia de un tabla en la bd
       =========================================*/
        if (empty(Connection::getColumsData($table))) {

            return null;
        }
        $linkToArray = explode(",", $linkTo);
        $equalToArray = explode(",", $equalTo);
        $linkToText = "";

        if (count($linkToArray) > 1) {

            foreach ($linkToArray as $key => $value) {

                if ($key > 0) {
                    $linkToText .= "AND " . $value . " = :" . $value . " ";
                }
            }
        }

        /*===================================
            Sin filtrar y eliminar datos
         ====================================*/

        $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText";

        /*===================================
             Ordenar datos sin limites
         ====================================*/

        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";
        }


        /*===================================
             ordenar y limitar datos
         ====================================*/


        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode LIMIT $startAt,$endAt";
        }

        /*===================================
           Limitar datos sin ordenar
         ====================================*/


        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText LIMIT $startAt,$endAt";
        }


        $stmt = Connection::connect()->prepare($sql);

        foreach ($linkToArray as $key => $value) {

            $stmt->bindParam(":" . $value, $equalToArray[$key], PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }



    /*=========================================================
         Peticiones GET sin filtro entre tablas relacionadas
    ============================================================*/
    static public function getRelData($rel, $type, $select, $orderBy, $orderMode, $startAt, $endAt)
    {


        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $innerJoinText = "";

        if (count($relArray) > 1) {

            foreach ($relArray as $key => $value) {

                /*=========================================
                    validar existencia de un tabla en la bd
                 =========================================*/
                if (empty(Connection::getColumsData($value))) {

                    return null;
                }

                if ($key > 0) {
                    $innerJoinText .= "INNER JOIN " . $value . " ON " . $relArray[0] . ".id_" . $typeArray[$key] . "_" . $typeArray[0] . " =
                     " . $value . ".id_" . $typeArray[$key] . " ";
                }
            }

            /*===================================
            Sin ordenar y sin limitar  datos
            ====================================*/
            $sql = "SELECT $select FROM $relArray[0] $innerJoinText ";

            /*===================================
                Ordenar datos sin limites
            ====================================*/

            if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText ORDER BY $orderBy $orderMode";
            }

            /*===================================
                ordenar y limitar datos
            ====================================*/

            if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText ORDER BY $orderBy $orderMode LIMIT $startAt,$endAt";
            }


            /*===================================
                Limitar datos sin ordenar
            ====================================*/

            if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText LIMIT $startAt,$endAt";
            }


            $stmt = Connection::connect()->prepare($sql);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {
            return null;
        }
    }

    /*=========================================================
         Peticiones GET con filtro entre tablas relacionadas
    ============================================================*/
    static public function getRelDataFilter($rel, $type, $select, $linkTo, $equalTo, $orderBy, $orderMode, $startAt, $endAt)
    {
        /*=========================================================
                organizamos los filtros
        ============================================================*/
        $linkToArray = explode(",", $linkTo);
        $equalToArray = explode(",", $equalTo);
        $linkToText = "";

        if (count($linkToArray) > 1) {

            foreach ($linkToArray as $key => $value) {
                /*=========================================
                    validar existencia de un tabla en la bd
                 =========================================*/
                if (empty(Connection::getColumsData($value))) {

                    return null;
                }

                if ($key > 0) {
                    $linkToText .= "AND " . $value . " = :" . $value . " ";
                }
            }
        }

        /*=========================================================
                  organizamos las relaciones 
        ============================================================*/

        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $innerJoinText = "";

        if (count($relArray) > 1) {

            foreach ($relArray as $key => $value) {

                if ($key > 0) {
                    $innerJoinText .= "INNER JOIN " . $value . " ON " . $relArray[0] . ".id_" . $typeArray[$key] . "_" . $typeArray[0] . " =
                     " . $value . ".id_" . $typeArray[$key] . " ";
                }
            }

            /*===================================
            Sin ordenar y sin limitar  datos
            ====================================*/
            $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ";

            /*===================================
                Ordenar datos sin limites
            ====================================*/

            if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";
            }

            /*===================================
                ordenar y limitar datos
            ====================================*/

            if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode LIMIT $startAt,$endAt";
            }


            /*===================================
                Limitar datos sin ordenar
            ====================================*/

            if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText LIMIT $startAt,$endAt";
            }


            $stmt = Connection::connect()->prepare($sql);


            foreach ($linkToArray as $key => $value) {

                $stmt->bindParam(":" . $value, $equalToArray[$key], PDO::PARAM_STR);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {
            return null;
        }
    }
    /*================================================
    peticiones GET para el buscador sin relaciones
   ===================================================*/
    static public function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {
        $linkToArray = explode(",", $linkTo);
        $searchArray = explode("_", $search);
        $linkToText = "";

        if (count($linkToArray) > 1) {

            foreach ($linkToArray as $key => $value) {

                /*=========================================
                    validar existencia de un tabla en la bd
                 =========================================*/
                if (empty(Connection::getColumsData($value))) {

                    return null;
                }
                if ($key > 0) {
                    $linkToText .= "AND " . $value . " = :" . $value . " ";
                }
            }
        }
        /*===================================
            Sin ordenar y sin limitar datos
        ====================================*/

        $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ";


        /*===================================
              Ordenar datos sin limites
        ====================================*/


        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode";
        }

        /*===================================
               ordenar y limitar datos
        ====================================*/

        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode LIMIT $startAt,$endAt";
        }

        /*===================================
               Limitar datos sin ordenar
        ====================================*/

        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText LIMIT $startAt,$endAt";
        }


        $stmt = Connection::connect()->prepare($sql);

        foreach ($linkToArray as $key => $value) {
            if ($key > 0) {

                $stmt->bindParam(":" . $value, $searchArray[$key], PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /*================================================
    peticiones GET para el buscador entre tablas relacionadas
   ===================================================*/
    static public function getRelDataSearch($rel, $type, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt)
    {
        /*=========================================================
                organizamos los filtros
        ============================================================*/
        $linkToArray = explode(",", $linkTo);
        $searchArray = explode("_", $search);
        $linkToText = "";

        if (count($linkToArray) > 1) {

            foreach ($linkToArray as $key => $value) {

                /*=========================================
                    validar existencia de un tabla en la bd
                 =========================================*/
                if (empty(Connection::getColumsData($value))) {

                    return null;
                }

                if ($key > 0) {
                    $linkToText .= "AND " . $value . " = :" . $value . " ";
                }
            }
        }

        /*=========================================================
                  organizamos las relaciones 
        ============================================================*/

        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $innerJoinText = "";

        if (count($relArray) > 1) {

            foreach ($relArray as $key => $value) {

                if ($key > 0) {
                    $innerJoinText .= "INNER JOIN " . $value . " ON " . $relArray[0] . ".id_" . $typeArray[$key] . "_" . $typeArray[0] . " =
                     " . $value . ".id_" . $typeArray[$key] . " ";
                }
            }

            /*===================================
            Sin ordenar y sin limitar  datos
            ====================================*/
            $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText";

            /*===================================
                Ordenar datos sin limites
            ====================================*/

            if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode";
            }

            /*===================================
                ordenar y limitar datos
            ====================================*/

            if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText  ORDER BY $orderBy $orderMode LIMIT $startAt,$endAt";
            }


            /*===================================
                Limitar datos sin ordenar
            ====================================*/

            if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText  LIMIT $startAt,$endAt";
            }


            $stmt = Connection::connect()->prepare($sql);


            foreach ($linkToArray as $key => $value) {
                if ($key > 0) {

                    $stmt->bindParam(":" . $value, $searchArray[$key], PDO::PARAM_STR);
                }
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {
            return null;
        }
    }

    static public function getDataRange($table, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
    {

        /*=========================================
        validar existencia de un tabla en la bd
       =========================================*/
        if (empty(Connection::getColumsData($table))) {

            return null;
        }

        $filter = "";

        if ($filterTo != null & $inTo != null) {
            $filter = 'AND ' . $filterTo . ' IN (' . $inTo . ')';
        }




        /*===================================
            Sin ordenar y sin limitar datos
        ====================================*/

        $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter";


        /*===================================
              Ordenar datos sin limites
        ====================================*/


        if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

            $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode";
        }

        /*===================================
               ordenar y limitar datos
        ====================================*/

        if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode LIMIT $startAt,$endAt";
        }

        /*===================================
               Limitar datos sin ordenar
        ====================================*/

        if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

            $sql = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter LIMIT $startAt,$endAt";
        }


        $stmt = Connection::connect()->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    static public function getRelDataRange($rel, $type, $select, $linkTo, $between1, $between2, $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo)
    {

        $filter = "";

        if ($filterTo != null & $inTo != null) {
            $filter = 'AND ' . $filterTo . ' IN (' . $inTo . ')';
        }

        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $innerJoinText = "";

        if (count($relArray) > 1) {

            foreach ($relArray as $key => $value) {

                /*=========================================
                    validar existencia de un tabla en la bd
                 =========================================*/
                if (empty(Connection::getColumsData($value))) {

                    return null;
                }

                if ($key > 0) {
                    $innerJoinText .= "INNER JOIN " . $value . " ON " . $relArray[0] . ".id_" . $typeArray[$key] . "_" . $typeArray[0] . " =
                     " . $value . ".id_" . $typeArray[$key] . " ";
                }
            }

            /*===================================
                Sin ordenar y sin limitar datos
            ====================================*/

            $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter";


            /*===================================
                Ordenar datos sin limites
            ====================================*/


            if ($orderBy != null && $orderMode != null && $startAt == null && $endAt == null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode";
            }

            /*===================================
                ordenar y limitar datos
            ====================================*/

            if ($orderBy != null && $orderMode != null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode LIMIT $startAt,$endAt";
            }

            /*===================================
                Limitar datos sin ordenar
            ====================================*/

            if ($orderBy == null && $orderMode == null && $startAt != null && $endAt != null) {

                $sql = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter LIMIT $startAt,$endAt";
            }


            $stmt = Connection::connect()->prepare($sql);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {
            return null;
        }
    }
}
