<?php

include("../../assets/lib/php/db.php");
include("../../assets/lib/php/DBlibrary.php");

$DBlib = new DatabaseFunctions($db);
session_start();

//http://project.lukaskrmencik.cz/L/code/pages/user/correctAnswers.php?id=1&code=suprkod

if(isset($_GET["id"])){
    if(is_numeric($_GET["id"])){
        if(isset($_SESSION["user"])){
            if($DBlib->countByPDOWithCondition("guest", "id","id = :id", [":id" => $_GET["id"]])){
                $guest = $DBlib->fetchDataWithCondition("guest", "*", "id = :id", [":id" => $_GET["id"]])[0];

                if($guest["code"] == $_GET["code"] ?? NULL){
                    $questions = $DBlib->fetchDataWithCondition("question", "*", "form_id = :id", [":id" => $guest["form_id"]]);
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
    <title>Correct answers</title>
    <script>
        id = <?php echo $_GET["id"] ?>;
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../../assets/lib/css/pretty-checkbox/dist/pretty-checkbox.min.css">
    <link rel="stylesheet" href="../../assets/global/general.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/correctAnswers.css">
    <link rel="stylesheet" href="../admin/css/statistics.css">
    <script src="../../assets/lib/js/general.js"></script>
    <script src="../../assets/lib/js/profileMenu.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/slim-select@latest/dist/slimselect.min.js"></script>
    <link href="../../assets/lib/css/slimSelect.css" rel="stylesheet"></link>
    <link rel="icon" type="image/png" href="../../assets/img/logo/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <style id="customStyles"></style>
</head>
<body>

    <script>
        let guest = <?php echo $guest["id"] ?? "null" ?>

        let questions = <?php echo json_encode($questions ?? NULL) ?>
    </script>

    <h1>Here is your completed and corrected form.</h1>

    <div class="form">
        <div class="questionInfo">

        </div>
    </div>

    <script src="js/correctAnswers.js"></script>
</body>
</html>