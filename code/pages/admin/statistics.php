<?php

include("../../assets/lib/php/db.php");
include("../../assets/lib/php/DBlibrary.php");

$DBlib = new DatabaseFunctions($db);
session_start();

if(isset($_GET["id"])){
    if(is_numeric($_GET["id"])){
        if(isset($_SESSION["user"])){
            if($DBlib->countByPDOWithCondition("form", "id","id = :id", [":id" => $_GET["id"]])){
                if(($_SESSION["user"] ?? NULL) == $DBlib->fetchDataWithCondition("form", "user_id", "id = :id", [":id" => $_GET["id"]])[0]["user_id"]){
        
                }else{
                    header("Location: ../error.php");
                }
            }else{
                header("Location: ../error.php");
            }
        }else{
            header("Location: ../user/login/login.php");
        }
    }else{
        header("Location: ../error.php");
    }
}else{
    header("Location: ../error.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../../assets/lib/css/pretty-checkbox/dist/pretty-checkbox.min.css">
    <link rel="stylesheet" href="../../assets/global/general.css">
    <link rel="stylesheet" href="css/statistics.css">
    <script src="../../assets/lib/js/general.js"></script>
    <script src="../../assets/lib/js/profileMenu.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/statistics.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/slim-select@latest/dist/slimselect.min.js"></script>
    <link href="../../assets/lib/css/slimSelect.css" rel="stylesheet"></link>
    <script>
        id = <?php echo $_GET["id"] ?>;
    </script>
</head>
<body>

    <?php 
        $location = "Statistics";
        include("../../assets/global/headerSidebar.php") 
    ?>

    <div id="content">
        <div class="container">
            <h2>Nazev formulare</h2>
            <div class="radio">
                <input type="radio" id="summary" name="view" class="viewRadio" checked>
                <label for="summary">Summary</label>
                <input type="radio" id="people" name="view" class="viewRadio">
                <label for="people">People</label>
                <input type="radio" id="question" name="view" class="viewRadio">
                <label for="question">Question</label>
                <div class="slider"></div>
            </div>

            <div class="data">

            </div>

        </div>
    </div>

</body>
</html>