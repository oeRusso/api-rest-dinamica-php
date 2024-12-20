<?php
require_once "controllers/get.controller.php";

$table = explode("?", $routesArray[1])[0]; //esto se hace para capturar el nombre de la tabla sin capturar los demas valores que se envian despues

$select = $_GET['select'] ?? "*";
$orderBy = $_GET['orderBy'] ?? null;
$orderMode = $_GET['orderMode'] ?? null;
$startAt = $_GET['startAt'] ?? null;
$endAt = $_GET['endAt'] ?? null;
$filterTo = $_GET['filterTo'] ?? null;
$inTo = $_GET['inTo'] ?? null;



$response = new GetController();

/*=====================================
    Peticiones GET con filtro
=======================================*/

if (isset($_GET['linkTo']) && isset($_GET['equalTo']) && !isset($_GET['rel']) && !isset($_GET['type'])) {
    $response->getDataFilter($table, $select, $_GET['linkTo'], $_GET['equalTo'], $orderBy, $orderMode, $startAt, $endAt);
    /*================================================
    peticiones GET sin filtro entre tablas relacionadas
   ===================================================*/
} else if (isset($_GET['rel']) && isset($_GET['type']) && $table == "relations" && !isset($_GET['linkTo']) && !isset($_GET['equalTo'])) {
    $response->getRelData($_GET['rel'], $_GET['type'], $select, $orderBy, $orderMode, $startAt, $endAt);

    /*================================================
       peticiones GET con filtro entre tablas relacionadas
    ===================================================*/
} else if (isset($_GET['rel']) && isset($_GET['type']) && $table == "relations" && isset($_GET['linkTo']) && isset($_GET['equalTo'])) {
    $response->getRelDataFilter($_GET['rel'], $_GET['type'], $select, $_GET['linkTo'], $_GET['equalTo'], $orderBy, $orderMode, $startAt, $endAt);

    /*================================================
       peticiones GET para el buscador sin relaciones
    ===================================================*/
} else if (!isset($_GET['rel']) && !isset($_GET['type']) && isset($_GET['linkTo']) && isset($_GET['search'])) {
    $response->getDataSearch($table, $select, $_GET['linkTo'], $_GET['search'], $orderBy, $orderMode, $startAt, $endAt);
}
/*================================================
peticiones GET para el buscador con relaciones
===================================================*/ else if (isset($_GET['rel']) && isset($_GET['type']) && $table == "relations" && isset($_GET['linkTo']) && isset($_GET['search'])) {

    $response->getRelDataSearch($_GET['rel'], $_GET['type'], $select, $_GET['linkTo'], $_GET['search'], $orderBy, $orderMode, $startAt, $endAt);

    /*================================================
peticiones GET para relaciones de rango
===================================================*/
} else if (!isset($_GET['rel']) && !isset($_GET['type'])&&isset($_GET['linkTo']) && isset($_GET['between1']) && isset($_GET['between2'])) {


    $response->getDataRange($table, $select, $_GET['linkTo'], $_GET['between1'], $_GET['between2'], $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);
    /*==============================================================
peticiones GET para relaciones de rango entre tablas relacionadas
================================================================*/
} else if (isset($_GET['rel']) && isset($_GET['type']) && $table == "relations" && isset($_GET['linkTo']) && isset($_GET['between1']) && isset($_GET['between2'])) {


    $response->getRelDataRange($_GET['rel'], $_GET['type'], $select, $_GET['linkTo'], $_GET['between1'], $_GET['between2'], $orderBy, $orderMode, $startAt, $endAt, $filterTo, $inTo);
} else {
    /*===========================
     Peticiones GET sin filtro
    ============================*/

    $response->getData($table, $select, $orderBy, $orderMode, $startAt, $endAt);
}
