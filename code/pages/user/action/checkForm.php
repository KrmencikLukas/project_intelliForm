<?php
include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");
$DBlib = new DatabaseFunctions($db);

//var_dump($_POST);
//var_dump($_GET);

if ((isset($_GET["id"]))&&(is_numeric($_GET["id"]))&&(isset($_POST))) {
    $id=$_GET["id"];
    $formID = [":id" => $id];
    $public= $DBlib->fetchDataWithCondition("form", "public","id = :id", $formID);
    $anonymous= $DBlib->fetchDataWithCondition('form_settings', 'value','form_id = :id AND `key` = "anonymous"', $formID);
    $public=$public[0]["public"];
    $anonymous=$anonymous[0]["value"];
    
    if ($anonymous==1) {
        if ($public==1) {
            echo "Jsem připraven zpracovávat.";
        } else {
            if ((isset($_GET["guestId"]))&&(is_numeric($_GET["guestId"]))&&(isset($_GET["code"]))) {
                $guestID=$_GET["guestId"];
                $guestCode=$_GET["code"];
                $guestInfo = [
                    ":id" => $guestID,
                    ":form_id" => $id,
                    ":code" => $guestCode,
                ];
                $guestVerification= $DBlib->countByPDOWithCondition('guest', 'id','id = :id AND form_id = :form_id AND code = :code', $guestInfo);
                
                if ($guestVerification==1) {
                    echo "Jsem připraven zpracovávat.";
                } else {
                    header("Location: ../../error.php");
                }
            } else {
                header("Location: ../../error.php");
            }
        }
    } else {
        if ($public==1) {
            if ((isset($_POST["email"]))&&(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
                echo "Jsem připraven zpracovávat.";
            } else {
                session_start();
                $_SESSION=$_POST;
                header("Location: ../form.php?id=".$id);
            }
        } else {
            if ((isset($_GET["guestId"]))&&(is_numeric($_GET["guestId"]))&&(isset($_GET["code"]))) {
                $guestID=$_GET["guestId"];
                $guestCode=$_GET["code"];
                $guestInfo = [
                    ":id" => $guestID,
                    ":form_id" => $id,
                    ":code" => $guestCode,
                ];
                $guestVerification= $DBlib->countByPDOWithCondition('guest', 'id','id = :id AND form_id = :form_id AND code = :code', $guestInfo);
                
                if ($guestVerification==1) {
                    echo "Jsem připraven zpracovávat.";
                } else {
                    header("Location: ../../error.php");
                }
            } else {
                header("Location: ../../error.php");
            }
        }
    }
} else {
    header("Location: ../../error.php");
}
