<?PHP
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: images.php
 * Desc: Image page for Projekt
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

require_once __DIR__ . "/classes/database.class.php";
require_once __DIR__ . "/classes/image.class.php";

$title = "DT161G - Joli1407 - Uploads";
$username = "No User is set!";
$category = "";
$error = "";

//Get the supplied params for image display
if (isset($_GET["user"])) {
    $username = $_GET["user"];
}
if (isset($_GET["category"])){
    $category = $_GET["category"];
}

//Get images for user/category
$dbc = Database::getInstance();
$imagesStatus = ResultEnum::OK;
if($category == ""){
    $images = $dbc->findImages($username,$imagesStatus);
}
else{
    $images = $dbc->findImagesInCategory($category,$imagesStatus);
}

//Display eventual error message
switch($imagesStatus){
    case ResultEnum::USER_NOT_FOUND:
        $error = "User not found!";
        break;
    case ResultEnum::CATEGORY_NOT_FOUND_OR_EMPTY:
        $error = "Category empty or not found!";
        break;
}

/*******************************************************************************
 * Output all images from $images
 ******************************************************************************/
function outputImages($images){
    if(!$images){
        return false;
    }

    foreach($images as $image){
        //Decode the data to get the type (perhaps store this in db)
        $finfo = finfo_open();
        $imageData = base64_decode($image->getImage());
        $type = finfo_buffer($finfo, $imageData, FILEINFO_MIME_TYPE);
        //Output img tag for image display
        echo "<img src=\"data:" . $type . ";base64," . $image->getImage() . "\" />";
    }
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
    <?php if($imagesStatus === ResultEnum::OK): ?>
    Images uploaded by: <?php echo htmlspecialchars($username); endif;?>
</header>

<main>
    <div id="imageDiv">
        <?php
            if($imagesStatus != ResultEnum::OK){
                echo $error;
            }
            outputImages($images)
        ?>
    </div>
</main>

<footer>
</footer>

</body>
</html>
