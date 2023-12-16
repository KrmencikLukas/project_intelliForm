<?php
include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");
$DBlib = new DatabaseFunctions($db);

//var_dump($_POST);
//var_dump($_GET);

function validateForm ($id, $formData, $DBlib) {
    $formID = [":id" => $id];
    $formQuestions= $DBlib->fetchDataWithCondition("question", "id",'form_id = :id', $formID);
    $mandatoryCount= 0;
    $sentCount = [];
    for ($i=0; $i < count($formQuestions); $i++) { 
        $questions = [":id" => $formQuestions[$i]["id"]];
        $questionMandatory= $DBlib->countByPDOWithCondition("question_settings", "id",'question_id = :id AND `key` = "Mandatory" AND `value` = 1', $questions);
        if ($questionMandatory==1) {
            $mandatoryCount= $mandatoryCount+1;
        }
    }
    foreach ($formData as $key => $value) {
        if (($key!="submit")&&($key!="email")) {
            $answerData = explode("*",$key);
            if ((isset($answerData[0]))&&(isset($answerData[1]))&&(isset($answerData[2]))&&(isset($answerData[3]))) {
                $questionID = strpos($answerData[1], "",1);
                $questionID = substr($answerData[1], $questionID);
                $params = [":id" => $questionID];
                $questionInDB= $DBlib->countByPDOWithCondition("question", "id","id = :id", $params );
                $questionMandatory= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Mandatory"', $params);

                if ($questionInDB==1) {
                    $answerID = strpos($answerData[2], "",1);
                    $answerID = substr($answerData[2], $answerID);
                    $params = [":id" => $answerID];
                    $answerInDB= $DBlib->countByPDOWithCondition("answer", "id","id = :id", $params );
                    if (($answerInDB==1)&&(!in_array($questionID,$sentCount))&&(isset($questionMandatory[0]["value"]))&&($questionMandatory[0]["value"]==1)) {
                        $sentCount[]=$questionID;
                    }
                }
            } elseif ((isset($answerData[0]))&&(isset($answerData[1]))&&(isset($answerData[2]))) {
                $questionID = strpos($answerData[1], "",1);
                $questionID = substr($answerData[1], $questionID);
                $params = [":id" => $questionID];
                $questionInDB= $DBlib->countByPDOWithCondition("question", "id","id = :id", $params );
                if ($questionInDB==1) {
                    $sentCount[]=$questionID;
                }
            }
        }
    }
    
    if (count($sentCount)==$mandatoryCount) {
        return 1;
    } else {
        return 0;
    }

}

function saveForm ($formData, $DBlib, $guest) {
    
    foreach ($formData as $key => $value) {
        if (($key!="submit")&&($key!="email")) {
            $answerData = explode("*",$key);
            $questionID = strpos($answerData[1], "",1);
            $questionID = substr($answerData[1], $questionID);
            $params = [":id" => $questionID];
            $questionInDB= $DBlib->countByPDOWithCondition("question", "id","id = :id", $params );
            
            if ($questionInDB==1) {
                if ((isset($answerData[0]))&&(isset($answerData[1]))&&(isset($answerData[2]))&&(isset($answerData[3]))) {
                    $answerID = strpos($answerData[2], "",1);
                    $answerID = substr($answerData[2], $answerID);
                    $params = [":id" => $answerID];
                    $answerInDB= $DBlib->countByPDOWithCondition("answer", "id","id = :id", $params );
                    if ($answerInDB==1) {
                        $insertData=[
                            "guest_id" => $guest,
                            "answer_id" => $answerID,
                            "value" => $value,
                        ];
                        $DBlib->insertData("guest_answer", $insertData);
                    }
                } elseif ((isset($answerData[0]))&&(isset($answerData[1]))&&(isset($answerData[2]))) {
                    $answer= $DBlib->fetchDataWithCondition("answer", "`id`, `name`",'question_id = :id', $params);
                    for ($i=0; $i < count($answer); $i++) { 
                        if (($answer[$i]["name"]=="Yes")&&($value==1)) {
                            $insertData=[
                                "guest_id" => $guest,
                                "answer_id" => $answer[$i]["id"],
                                "value" => 1,
                            ];
                            $DBlib->insertData("guest_answer", $insertData);
                        } elseif (($answer[$i]["name"]=="No")&&($value==0)) {
                            $insertData=[
                                "guest_id" => $guest,
                                "answer_id" => $answer[$i]["id"],
                                "value" => 1,
                            ];
                            $DBlib->insertData("guest_answer", $insertData);
                        }
                        
                    }
                    
                }
            }
        }
    }
    return 1;
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
                saveForm ($_POST, $DBlib, null);
                header("Location: ../formSubmitted.php?id=".$id);
            } else {
                session_start();
                $_SESSION=$_POST;
                $_SESSION["reason"]="mandatory";
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
                        saveForm ($_POST, $DBlib, null);
                        header("Location: ../formSubmitted.php?id=".$id);
                    } else {
                        session_start();
                        $_SESSION=$_POST;
                        $_SESSION["reason"]="mandatory";
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
                    $validateEmail = [":email" => $_POST["email"]];
                    $emailInDB=$DBlib->countByPDOWithCondition("guest","id","email = :email",$validateEmail);
                    if ($emailInDB==0) {
                        $insertData=[
                            "email" => $_POST["email"],
                            "form_id" => $id,
                        ];
                        $newGuest=$DBlib->insertData("guest", $insertData);
    
                        saveForm ($_POST, $DBlib, $newGuest);
                        header("Location: ../formSubmitted.php?id=".$id);
                    } else {
                        header("Location: ../formSubmitted.php?id=".$id);
                    }
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
                        saveForm ($_POST, $DBlib, $guestID);
                        header("Location: ../formSubmitted.php?id=".$id);
                    } else {
                        session_start();
                        $_SESSION=$_POST;
                        $_SESSION["reason"]="mandatory";
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
