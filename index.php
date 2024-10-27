<?php
// mostrar errores
ini_set('display_errors',1);
ini_set("logs_errors",1);
ini_set("error_log",  "C:/xampp/htdocs/apirest-dinamica/php_error_log");

// requerimientos

require_once 'controllers/routes.controller.php';
$index = new RoutesController();
$index->index();