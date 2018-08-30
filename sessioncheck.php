<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: sessioncheck.php
 * Desc: Returns a response with values relevant for the current session such as
 * the correct menu links for the role of the current user. (Or defaults if not
 * logged in)
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

require_once __DIR__ . "/classes/member.class.php";
require_once __DIR__ . "/classes/config.class.php";
require_once __DIR__ . "/classes/role.class.php";

session_start();
header('Content-Type: application/json');
$response = []; //Array to hold response values
$conf = Config::getInstance();

//If user is logged in prepare response with data based on user role
if(isset($_SESSION['loggedIn'])){
    $member = unserialize($_SESSION['loggedIn']);
    $response['loggedIn'] = $member->getUsername();

    $links = [];
    foreach ($member->getRoles() as $r){
        foreach($conf->getLinks($r) as $key => $value)
            $links[$key] = $value;
    }
    $response['links'] = $links;

}
else{
    $response['loggedIn'] = false;
    $response['links'] = $conf->getLinks();
}

echo json_encode($response);