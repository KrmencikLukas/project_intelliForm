<?php
include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");
$DBlib = new DatabaseFunctions($db);

var_dump($_POST);
var_dump($_GET);

if ((isset($_GET["id"]))&&(is_numeric($_GET["id"]))&&(isset($_POST))) {
    $id=$_GET["id"];
    $formID = [":id" => $id];
    $public= $DBlib->fetchDataWithCondition("form", "public","id = :id", $formID);
    $anonymous= $DBlib->fetchDataWithCondition('form_setting', 'value','form_id = :id AND key = "anonymous"', $formID);
    $public=$public[0]["public"];
    $anonymous=$anonymous[0]["value"];
    var_dump($public);
    if ($anonymous==1) {
        # code...
    } else {
        
        
        if ((isset($_GET["guestId"]))&&(is_numeric($_GET["guestId"]))&&(isset($_GET["code"]))) {
            

        } else {
            header("Location: ../../error.php");
        }
    }
} else {
    header("Location: ../../error.php");
}
