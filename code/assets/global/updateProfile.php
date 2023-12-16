<?php
    include("../lib/php/db.php");
    include("../lib/php/DBlibrary.php");
    include("../lib/php/general.php");

    session_start();

    $formId = $_GET["id"] ?? null;
    $user = $_SESSION["user"] ?? null;


    if(!isset($_SESSION['user'])){
        header("location: ../../user/login/login.php");
    }
    if(!isset($_GET["id"])){
        header("location:../Dashboard/Dashboard.php");
    }   
    if(!is_numeric($_GET["id"])){
        header("location:../Dashboard/Dashboard.php");
    }   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User update</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="general.css">
    <link rel="stylesheet" href="update.css">
</head>
<body>
    <?php
        $location = "User update";
        include("headerSidebar.php");
    ?>
     <div id="content" class="wrap">
        <div id="main">
            <h2 id="title">User update</h2>
            <div id="fields">
                <h3>User data:</h3>
                <input type="text">
                <input type="text">
                <input type="email">
                <input type="password">
                <input>
            </div>
            <div id="img">
            <h3>Profile picture:</h3>
            </div>

        </div>
     </div>
</body>
</html>