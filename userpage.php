<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: userpage.php
 * Desc: Userpage page for Projekt
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

require_once __DIR__ . "/classes/member.class.php";
require_once __DIR__ . "/classes/category.class.php";
require_once __DIR__ . "/classes/database.class.php";
require_once __DIR__ . "/util.php";

//False means that user is not logged in and is redirected to the start page
session_start();
if (isset($_SESSION["loggedIn"]) == false){
    header("Location: index.php"); /* Redirect browser */
    exit;
}

$title = "DT161G - Joli1407 - User page";
$displayUploadForm = true;  //Form is only to be displayed if the user has any categories
$dbc = Database::getInstance();
$loggedInUser = unserialize($_SESSION['loggedIn']); //Unserialize logged in user object

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //Handle category creation
    if(isset($_POST["newCategory"])){
        $dbc->insertCategory($loggedInUser->getUsername(), $_POST["newCategory"]);
    }

    //Handle category deletion
    if(isset($_POST["categoryToDelete"])){
        $dbc->deleteCategory($_POST["categoryToDelete"]);
    }
}

//Then lookup the user in db to fetch any changes
$userStatus = ResultEnum::OK;
$loggedInUser = $dbc->findUser($loggedInUser->getUsername(), $userStatus);
$categories = $loggedInUser->getCategories();

//If user doesn't have any categories the image upload form should not be shown
if(count($categories) == 0){
    $displayUploadForm = false;
}

/*******************************************************************************
 * Outputs category options for use in a form (value is the id and text is
 * the name of the category)
 ******************************************************************************/
function outputCategoryOptions($categories){
    foreach($categories as $category){
        echo "<option value=\"" . $category->getId() . "\">" . htmlspecialchars($category->getCategory()) . "</option>";
    }
}

/*******************************************************************************
 * Outputs categories wrapped in ul/li tags
 ******************************************************************************/
function outputCategoryList($categories){
    echo "<ul>";
    foreach($categories as $category){
        echo "<li>" . htmlspecialchars($category->getCategory()) . "</li>";
    }
    echo "</ul>";
}

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

    <div id="userCategoryList">
        <h4>User Categories</h4>
        <?php outputCategoryList($categories); ?>
    </div>

    <h4>Create category</h4>
    <form id="categoryCreationForm" method="POST">
        <input type="text" name = "newCategory" id="newCategory" required>
        <button type="submit">Create</button>
    </form>

    <h4>Delete category</h4>
    <form id="categoryDeletionForm" method="POST">
        <select name="categoryToDelete" id="categoryToDelete">
            <?php outputCategoryOptions($categories); ?>
        </select>
        <button type="submit">Delete</button>
    </form>

    <h4>Upload image</h4>
    <?php if($displayUploadForm): ?>
        <form id="imageUploadForm">
            <label><b>Image</b></label>
            <input type="file" placeholder="m" name="image" id="image" accept="image/*">
            <select name="categories" id="uploadCategory">
                <?php outputCategoryOptions($categories); ?>
            </select>
            <button type="button" id="imageUploadButton">Upload</button>
        </form>
    <?php endif; ?>

    <div id="imageUploadFeedback">

    </div>
</main>

<footer>
</footer>

</body>
</html>



