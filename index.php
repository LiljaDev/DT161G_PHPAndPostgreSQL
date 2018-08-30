<?PHP
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: index.php
 * Desc: Start page for Projekt
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/
$title = "DT161G - Joli1407 - Project";

require_once __DIR__ . "/classes/member.class.php";
require_once __DIR__ . "/classes/database.class.php";
require_once __DIR__ . "/classes/category.class.php";
require_once __DIR__ . "/util.php";

/*******************************************************************************
 * HTML section starts here
 ******************************************************************************/
?>
<!DOCTYPE html>
<html lang="sv-SE">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title ?></title>
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

        <div id="introduction">
            <p>
                Welcome to the next generation image portal! <br>
                Log in using the form above and start uploading images in the blink of an eye. <br>
                In case you're a mere visitor wishing to browse some of our fine selection of images you can do so from the menu on the left, either by uploader or an uploaders category. <br>
                As a logged in user you can create and delete categories and upload images from your user page. <br>
                As a logged in administrator you can create and delete users from the admin page. <br>
            </p>
        </div>

        <div id="count">
        </div>
    </main>

    </body>
</html>
