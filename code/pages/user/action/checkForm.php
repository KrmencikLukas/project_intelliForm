<?php
include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");
$DBlib = new DatabaseFunctions($db);

var_dump($_POST);
var_dump($_GET);

function validateForm ($id, $formData, $DBlib) {
    $formID = [":id" => $id];
    $questions= $DBlib->fetchDataWithCondition("question", "id","form_id = :id", $formID);
    $valid = true;

    foreach ($formData as $key => $value) {
        if (preg_match("/QUESTION/i", $key)) {
            preg_match('@^(?:QUESTION%)?([^/]+)@i', $key,$questionID);
            $questionID= $questionID[0];
            return $questionID;
        } else {
        }
        
    }
    
/*    for ($i=0; $i < count($questions); $i++) { 
        $questionID = [":id" => $questions[$i]["id"]];
        $questionMandatory= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Mandatory"', $questionID);
        
        if ((!empty($questionMandatory[0]["value"]))&&($questionMandatory[0]["value"]=="1")&&($valid==true)) {
            $answerIDs= $DBlib->fetchDataWithCondition("answer", "`id`",'question_id = :id', $questionID);
            for ($z=0; $z < count($answerIDs); $z++) { 
                if ((!empty($answerIDs[$z]["id"]))&&(!empty($questions[$i]["id"]))&&(!empty($formData["q".$questions[$i]["id"]."a".$answerIDs[$z]["id"]]))) {
                    
                } else {
                    $isSubmitted = false;
                    for ($x=0; $x < count($answerIDs); $x++) { 
                        if (!empty($formData["QUESTION%".$questions[$i]["id"]."QUESTION*ANSWER%".$answerIDs[$x]["id"]."ANSWER"])) {
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
    }*/
    return $valid;
}

function saveForm ($id, $formData, $DBlib, $guest) {
    $formID = [":id" => $id];
    $questions= $DBlib->fetchDataWithCondition("question", "id","form_id = :id", $formID);    
    for ($i=0; $i < count($questions)-1; $i++) { 
        for ($z=0; $z < count($answerIDs); $z++) { 
            if (isset($formData["q".$questions[$i]["id"]."a".$answerIDs[$z]["id"]])) {
                $values=[
                    "guest_id" => $guest,
                    "answer_id" => $formData["q".$questions[$i]["id"]],
                    "value" => 1,
                ];
                $DBlib -> insertData("guest_answer", $values);
            } else {
                $answerIDs= $DBlib->fetchDataWithCondition("answer", "`id`",'question_id = :id', $questionID);
                for ($x=0; $x < count($answerIDs); $x++) { 
                    if (isset($formData["QUESTION%".$questions[$i]["id"]."QUESTION*ANSWER%".$answerIDs[$x]["id"]."ANSWER"])) {
                        $values=[
                            "guest_id" => $guest,
                            "answer_id" => $answerIDs[$x]["id"],
                            "value" => int($formData["QUESTION%".$questions[$i]["id"]."QUESTION*ANSWER%".$answerIDs[$x]["id"]."ANSWER"]),
                        ];
                        $DBlib -> insertData("guest_answer", $values);
                    }
                }
            }
        }
        
    }
    return "ok";
}


if ((isset($_GET["id"]))&&(is_numeric($_GET["id"]))&&(isset($_POST))) {
    $id=$_GET["id"];
    $formID = [":id" => $id];
    $everyone= $DBlib->fetchDataWithCondition("form", "everyone","id = :id", $formID);
    $anonymous= $DBlib->fetchDataWithCondition('form_settings', 'value','form_id = :id AND `key` = "anonymous"', $formID);
    $everyone=$everyone[0]["everyone"];
    $anonymous=$anonymous[0]["value"];
    
    if ($anonymous==1) {
        if ($everyone==1) {
            if (validateForm ($id,$_POST,$DBlib)==true) {
                //saveForm ($id, $_POST, $DBlib, null);
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
                        //saveForm ($id, $_POST, $DBlib, null);
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
        if ($everyone==1) {
            if ((isset($_POST["email"]))&&(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
                if (validateForm ($id,$_POST,$DBlib)==true) {
                    //vytvorit guesta a uploud
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
                    var_dump(validateForm ($id,$_POST,$DBlib));
                    if (validateForm ($id,$_POST,$DBlib)==true) {
                        //saveForm ($id, $_POST, $DBlib, $guestID);
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
