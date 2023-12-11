<?php
include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");
$DBlib = new DatabaseFunctions($db);

//var_dump($_POST);
//var_dump($_GET);

function validateForm ($id, $formData, $DBlib) {
    $formID = [":id" => $id];
    $questions= $DBlib->fetchDataWithCondition("question", "id","form_id = :id", $formID);
    $valid = true;
    
    for ($i=0; $i < count($questions); $i++) { 
        $questionID = [":id" => $questions[$i]["id"]];
        $questionMandatory= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Mandatory"', $questionID);
        if ((isset($questionMandatory[0]["value"]))&&($questionMandatory[0]["value"]=="1")&&($valid==true)) {
            if (isset($formData["q".$questions[$i]["id"]])) {
                $valid = true;
            } else {
                $answerIDs= $DBlib->fetchDataWithCondition("answer", "`id`",'question_id = :id', $questionID);
                $isSubmitted = false;
                for ($x=0; $x < count($answerIDs); $x++) { 
                    if (isset($formData["QUESTION%".$questions[$i]["id"]."QUESTION*ANSWER%".$answerIDs[$x]["id"]."ANSWER"])) {
                        $isSubmitted = true;
                    }
                }
                if ($isSubmitted == true) {
                    $valid = true;
                } else {
                    $valid = false;
                }
            }
        }
    }
    return $valid;
}


if ((isset($_GET["id"]))&&(is_numeric($_GET["id"]))&&(isset($_POST))) {
    $id=$_GET["id"];
    $formID = [":id" => $id];
    $public= $DBlib->fetchDataWithCondition("form", "public","id = :id", $formID);
    $anonymous= $DBlib->fetchDataWithCondition('form_settings', 'value','form_id = :id AND `key` = "anonymous"', $formID);
    $public=$public[0]["public"];
    $anonymous=$anonymous[0]["value"];
    
    if ($anonymous==1) {
        if ($public==1) {
            if (validateForm ($id,$_POST,$DBlib)==true) {
                # code...
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
                    if (validateForm ($id,$_POST,$DBlib)==true) {
                        # code...
                    } else {
                        session_start();
                        $_SESSION=$_POST;
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
        if ($public==1) {
            if ((isset($_POST["email"]))&&(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
                if (validateForm ($id,$_POST,$DBlib)==true) {
                    # code...
                } else {
                    session_start();
                    $_SESSION=$_POST;
                    $email = $_SESSION["email"];
                    unset($_SESSION["email"]);
                    $_SESSION["goodEmail"]=$email;
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
                    if (validateForm ($id,$_POST,$DBlib)==true) {
                        # code...
                    } else {
                        session_start();
                        $_SESSION=$_POST;
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
