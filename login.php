<?PHP
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: login.php
 * Desc: Login page for Projekt
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/
require_once __DIR__ . "/util.php";
require_once __DIR__ . "/classes/database.class.php";

//Validate login true/false, additional error message if false
function validateLogin($login, &$errorMsg){
    $dbc = Database::getInstance();
    $userStatus = ResultEnum::OK;
    $user = $dbc->findUser($login['uname'], $userStatus);

    if($user){
        if(password_verify($login['psw'], $user->getPassword())){
            return true;
        }
    }

    //Message does not tell what credentials were wrong in order to stop clients
    //from guessing usernames
    $errorMsg = "Incorrect login!";
    return false;
}

session_start();
header('Content-Type: application/json');

//Get login input and validate
$json = file_get_contents('php://input');
$login = json_decode($json, true);
$errorMsg = "";
$result = validateLogin($login, $errorMsg);

//Construct response
$response['result'] = $result;
if($result == true){
    $dbc = Database::getInstance();
    $userStatus = ResultEnum::OK;
    $m = $dbc->findUser($login['uname'], $userStatus);
    $_SESSION['loggedIn'] = serialize($m);

    $conf = Config::getInstance();
    $links = [];
    foreach ($m->getRoles() as $r){
        foreach($conf->getLinks($r) as $key => $value)
            $links[$key] = $value;
    }
    $response['links'] = $links;
}
else{
    $response['errorMsg'] = $errorMsg;
}
echo json_encode($response);

?>