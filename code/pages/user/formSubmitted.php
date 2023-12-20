<?php
    include("../../assets/lib/php/db.php");
    include("../../assets/lib/php/DBlibrary.php");
    include("action/formCSS.php");
    $DBlib = new DatabaseFunctions($db);

    //kontrola adresy
    $id=$_GET["id"] ?? null;
    if ((!empty($id))&&(is_numeric($id))) {
        $formID = [ "id" => $id];
        $FormName=$DBlib->fetchDataWithCondition("form", "name", "id = :id", $formID);
        $formCSSkey=$DBlib->fetchDataWithCondition("form_settings", "`key`", "form_id = :id", $formID);
        $formCSSvalue=$DBlib->fetchDataWithCondition("form_settings", "`value`", "form_id = :id", $formID);
        $echoCSS=SetFormCSS($formCSSkey, $formCSSvalue);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted <?=$FormName[0]["name"]?> | Formative</title>
    <link rel="stylesheet" href="css/form.css">
    <style>
        <?=$echoCSS?>
    </style>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="css/submit.css">
    <link rel="icon" type="image/png" href="../../assets/img/logo/favicon.png">
</head>
<body>
    <div id="content">
        <div class="form">
            <h1 class="formHeading">Your form was sucessfully submitted.</h1>
            <div id="thanksDiv">
                <p id="thanks">Thank you for submitting this form.</p>
                <div id="checkbox">
                    <span class="material-symbols-outlined">check_box_outline_blank</span>
                    <span class="material-symbols-outlined check">done</span>
                </div>
            </div>
            <a href="" id="powered">
                <div id="formative">
                    <p>Powered by:</p>
                </div>
                <img src="../../assets/img/logo/logo.svg" alt="Formative">
            </a>
        </div>
    </div>
</body>
</html>