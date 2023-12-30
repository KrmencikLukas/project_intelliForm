<?php
include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");
include("saveForm.php");
include("validateForm.php");
include("../../../assets/lib/php/HashLibrary.php");
$DBlib = new DatabaseFunctions($db);

//var_dump($_POST);
//var_dump($_GET);

if ((isset($_GET["id"]))&&(is_numeric($_GET["id"]))&&(isset($_POST))) {
    $id=$_GET["id"];
    $formID = [":id" => $id];
    $everyone= $DBlib->fetchDataWithCondition("form", "everyone","id = :id", $formID);
    $anonymous= $DBlib->fetchDataWithCondition('form_settings', 'value','form_id = :id AND `key` = "anonymous"', $formID);
    $everyone=$everyone[0]["everyone"];
    $anonymous=$anonymous[0]["value"];
    
    if ($anonymous==1) {
        if ($everyone==1) {
            $reason=validateForm ($id,$_POST,$DBlib);
            if ($reason==1) {
                saveForm ($_POST, $DBlib, null);
                header("Location: ../formSubmitted.php?id=".$id);
            } else {
                session_start();
                $_SESSION=$_POST;
                $_SESSION["reason"]=$reason;
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
                    $reason=validateForm ($id,$_POST,$DBlib);
                    
                    if ($reason==1) {
                        saveForm ($_POST, $DBlib, null);
                        header("Location: ../formSubmitted.php?id=".$id);
                    } else {
                        session_start();
                        $_SESSION=$_POST;
                        $_SESSION["reason"]=$reason;
                        header("Location: ../form.php?id=".$id."&guestId=".$guestID."&code=".$guestCode);
                    }
                    
                } else {
                    header("Location: ../../error.php");
                }
            } else {
                header("Location: ../../error.php");
            }
        }
    } else {
        if ($everyone==1) {
            if ((isset($_POST["email"]))&&(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
                $reason=validateForm ($id,$_POST,$DBlib);
                
                if ($reason==1) {
                    $validateEmail = [
                        ":email" => $_POST["email"],
                        ":form_id" => $id,
                    ];
                    $emailInDB=$DBlib->countByPDOWithCondition("guest","id","email = :email AND form_id = :form_id",$validateEmail);
                    
                    if ($emailInDB==0) {
                        $insertData=[
                            "email" => $_POST["email"],
                            "form_id" => $id,
                            "code" => generateRandomCode(),
                        ];
                        $newGuest=$DBlib->insertData("guest", $insertData);
                        $newGuestID = [ "id" => $newGuest];
                        $newGuestCode=$DBlib->fetchDataWithCondition("guest", "code", "id = :id", $newGuestID);
    
                        saveForm ($_POST, $DBlib, $newGuest);
                        header("Location: ../formSubmitted.php?id=".$id."&guestId=".$newGuest."&code=".$newGuestCode[0]["code"]);
                    } else {
                        $GuestID = [ "email" => $_POST["email"], "id" => $id];
                        $Guest=$DBlib->fetchDataWithCondition("guest", "id", "form_id = :id AND email = :email", $GuestID);
                        $GuestID = [ "id" => $Guest[0]["id"]];
                        $GuestCode=$DBlib->fetchDataWithCondition("guest", "code", "id = :id", $GuestID);
                        header("Location: ../formSubmitted.php?id=".$id."&guestId=".$Guest[0]["id"]."&code=".$GuestCode[0]["code"]);
                    }
                } else {
                    session_start();
                    $_SESSION=$_POST;
                    $email = $_SESSION["email"];
                    unset($_SESSION["email"]);
                    $_SESSION["goodEmail"]=$email;
                    $_SESSION["reason"]=$reason;
                    header("Location: ../form.php?id=".$id);
                }
                
            } else {
                session_start();
                $_SESSION=$_POST;
                $_SESSION["reason"]="email";
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
                    $reason=validateForm ($id,$_POST,$DBlib);
                    if ($reason==1) {
                        saveForm ($_POST, $DBlib, $guestID);
                        header("Location: ../formSubmitted.php?id=".$id."&guestId=".$guestID."&code=".$guestCode);
                    } else {
                        session_start();
                        $_SESSION=$_POST;
                        $_SESSION["reason"]=$reason;
                        header("Location: ../form.php?id=".$id."&guestId=".$guestID."&code=".$guestCode);
                    }
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
