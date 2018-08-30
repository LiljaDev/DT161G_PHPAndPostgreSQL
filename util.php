<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: util.php
 * Desc: Util functionality
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

require_once __DIR__ . "/classes/member.class.php";
require_once __DIR__ . "/classes/database.class.php";
require_once __DIR__ . "/classes/category.class.php";

/*******************************************************************************
 * set debug true/false to change php.ini
 * To get more debug information when developing set to true,
 * for production set to false
 ******************************************************************************/
$debug = true;

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

/*******************************************************************************
 * Outputs links to images.php with appropriate parameters (user and all categories)
 * wrapped <li> tags.
 * Data is fetched from db on each call
 *******************************************************************************/
function outputAllUserLinks(){
    $dbc = Database::getInstance();
    $users = $dbc->findAllUsers();

    foreach ($users as $user){
        print("<li>" . "<a href=\"images.php?user=". $user->getUsername() . "\" >" . htmlspecialchars($user->getUsername()) . "</a></li>");
        foreach (($user->getCategories()) as $category){
            print("<li>" . "<a href=\"images.php?user=". $user->getUsername() . "&category=" . $category->getId() .  "\" > <i>" . htmlspecialchars($category->getCategory()) . "</i></a></li>");
        }
    }
}

?>
