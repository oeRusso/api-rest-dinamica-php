<?php

$server = $_SERVER['REQUEST_URI'];
$routesArray = explode("/", $server);
$routesArray = array_filter($routesArray);

// Cuando no se hacen peticiones a la api
if (count($routesArray) == 0) {
    $json = array(
        'status' => 404,
        'result' => 'Not found'
    );

    echo json_encode($json, http_response_code($json["status"]));
    return;
}

// Cuando si se hacen peticiones
if (count($routesArray) == 1 && isset($_SERVER['REQUEST_METHOD'])) {

    // Peticiones GET
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        
       include 'services/get.php';
       
    }

     // Peticiones POST
     if ($_SERVER['REQUEST_METHOD'] == "POST") {
        
        $json = array(

            'status' => 200,
            'result' => 'Solicitud POST'

        );

        echo json_encode($json, http_response_code($json["status"]));
     
    }

     // Peticiones PUT
     if ($_SERVER['REQUEST_METHOD'] == "PUT") {
        
        $json = array(

            'status' => 200,
            'result' => 'Solicitud PUT'

        );

        echo json_encode($json, http_response_code($json["status"]));
        
    }

     // Peticiones DELETE
     if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
        
        $json = array(

            'status' => 200,
            'result' => 'Solicitud DELETE'

        );

        echo json_encode($json, http_response_code($json["status"]));
      
    }
}
