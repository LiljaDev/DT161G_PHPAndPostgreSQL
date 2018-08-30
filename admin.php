<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: admin.php
 * Desc: Admin page that enables actions such as user creation/deletion.
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/
require_once __DIR__ . "/classes/member.class.php";
require_once __DIR__ . "/classes/role.class.php";
require_once __DIR__ . "/classes/database.class.php";
require_once __DIR__ . "/util.php";

//Check roles of logged in user
session_start();
$isAdmin = false;
if(isset($_SESSION["loggedIn"]) == true){
    $member = unserialize($_SESSION["loggedIn"]);
    $roles = $member->getRoles();
    foreach ($roles as $role) {
        if($role->getRole() == "admin"){
            $isAdmin = true;
            break;
        }
    }
}
//No admin role = redirect to start page
if($isAdmin == false){
    header("Location: index.php"); /* Redirect browser */
    exit;
}

//Handle user creation/deletion post
$dbc = Database::getInstance();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //User creation request, hashes password and stores hash+salt+algo in db
    if(isset($_POST["newUsername"]) && isset($_POST["newUserPassword"])){
        $newUsername = $_POST["newUsername"];
        $newUserPassword = password_hash($_POST["newUserPassword"], PASSWORD_DEFAULT);
        $dbc->insertUser($newUsername, $newUserPassword);
    }
    else if(isset($_POST["userToDelete"])){ //User deletion request
        $dbc->deleteUser($_POST["userToDelete"]);
    }
}

//Get all users from db
$dbc = Database::getInstance();
$users = $dbc->findAllUsers();

/*******************************************************************************
 * Outputs a list of users by username
 ******************************************************************************/
function outputUsers($users){
    echo "<ul>";
    foreach($users as $u){
        echo "<li>" . htmlspecialchars($u->getUsername()) . "</li>";
    }
    echo "</ul>";
}

/*******************************************************************************
 * Outputs user options for use in form (value id, text username)
 ******************************************************************************/
function outputUserOptions($users){
    foreach($users as $u){
        echo "<option value=\"" . $u->getId() . "\">" . htmlspecialchars($u->getUsername()) . "</option>";
    }
}

$title = "DT161G - Joli1407 - Admin page";
?>

<!DOCTYPE html>
<html lang="sv-SE">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DT161G-Laboration4-admin</title>
    <link rel="stylesheet" href="css/style.css"/>
    <script src="js/main.js"></script>
</head>
    <body>
        <header>
            <h1><?php echo $title ?></h1>
            <aside>
                <div id="login">
                    <form id="loginForm">
                        <label><b>Username</b></label>
                        <input type="text" placeholder="m" name="uname" id="uname"
                               required maxlength="10" value="m" autocomplete="off">
                        <label><b>Password</b></label>
                        <input type="password" placeholder="Enter Password" name="psw" id="psw"
                               required>
                        <button type="button" id="loginButton">Login</button>
                    </form>
                </div>

                <div id="logout">
                    <button type="button" id="logoutButton">Logout</button>
                </div>
            </aside>
        </header>
        <main>
            <nav>
                <h3>Menu</h3>
                <ul id="mainLinks">
                </ul>
                <h3>Images</h3>
                <ul id="userLinks">
                    <?php  outputAllUserLinks(); ?>
                </ul>
            </nav>


            <div id="adminUserList">
                <h4>Users</h4>
                <?php outputUsers($users); ?>
            </div>

            <h4>Create user</h4>
            <form id="userCreationForm" method="POST">
                <label>Username:</label>
                <input type="text" name="newUsername" id="newUsername" required>
                <label>Password:</label>
                <input type="text" name="newUserPassword" id="newUserPassword" required>
                <button type="submit">Create</button>
            </form>

            <h4>Delete user</h4>
            <form id="userDeletionForm" method="POST">
                <select name="userToDelete" id="userToDelete">
                    <?php outputUserOptions($users); ?>
                </select>
                <button type="submit">Delete</button>
            </form>

        </main>
    </body>
</html>
