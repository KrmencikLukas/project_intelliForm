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
    $MinMaxCount = [];
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
                    } else {
                        $params = [":id" => $questionID];
                        $minInDB= $DBlib->countByPDOWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min votes"', $params );
                        $maxInDB= $DBlib->countByPDOWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max votes"', $params );
                        $minUInDB= $DBlib->countByPDOWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min upvotes"', $params );
                        $maxUInDB= $DBlib->countByPDOWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max upvotes"', $params );
                        if (($minInDB>0)||($maxInDB>0)||($minUInDB>0)||($maxUInDB>0)) {
                            $MinMaxCount[$answerID] = $questionID;
                        }
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
        foreach ($MinMaxCount as $answer => $question) {
            if (isset($formData["*q".$question."*a".$answer."*t4"])) {
                if (!isset($downvote)) {
                    $downvote[$question]=0;
                }
                if (!isset($upvote)) {
                    $upvote[$question]=0;
                }
                
                if ($formData["*q".$question."*a".$answer."*t4"]==1) {
                    $upvote[$question]=$upvote[$question]+1;
                } elseif ($formData["*q".$question."*a".$answer."*t4"]==0) {
                    $downvote[$question]=$downvote[$question]+1;
                }

            } else {
                if (!isset($count[$question])) {
                    $count[$question]=0;
                }
                $count[$question]=$count[$question]+1;
            }
        }
        $MinMaxDBCount=0;
        $FinalCount=0;
        for ($i=0; $i < count($formQuestions); $i++) { 
            $questions = [":id" => $formQuestions[$i]["id"]];
            $questionMinVote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min votes"', $questions);
            $questionMaxVote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max votes"', $questions);
            $questionMinUpvote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min upvotes"', $questions);
            $questionMaxUpvote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max upvotes"', $questions);
            $questionMinDownvote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min downvotes"', $questions);
            $questionMaxDownvote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max downvotes"', $questions);
            
            if ((!empty($questionMinVote))&&(!empty($questionMaxVote))) {
                $MinMaxDBCount=$MinMaxDBCount+1;
                if ((isset($count[$formQuestions[$i]["id"]]))&&($questionMinVote[0]["value"]<=$count[$formQuestions[$i]["id"]])&&($questionMaxVote[0]["value"]>=$count[$formQuestions[$i]["id"]])) {
                    $FinalCount=$FinalCount+1;
                } 
            } elseif ((!empty($questionMinUpvote))&&(!empty($questionMaxUpvote))) {
                $MinMaxDBCount=$MinMaxDBCount+1;
                if ((isset($upvote[$formQuestions[$i]["id"]]))&&(isset($downvote[$formQuestions[$i]["id"]]))&&($questionMinUpvote[0]["value"]<=$upvote[$formQuestions[$i]["id"]])&&($questionMaxUpvote[0]["value"]>=$upvote[$formQuestions[$i]["id"]])&&($questionMinDownvote[0]["value"]<=$downvote[$formQuestions[$i]["id"]])&&($questionMaxDownvote[0]["value"]>=$downvote[$formQuestions[$i]["id"]])) {
                    $FinalCount=$FinalCount+1;
                }
            }

        }
        
        if ($MinMaxDBCount==$FinalCount) {
            return 1;
        } else {
            return "minmax";
        }
    } else {
        return "mandatory";
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
                        var_dump($reason);
                        saveForm ($_POST, $DBlib, $guestID);
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
    }
} else {
    header("Location: ../../error.php");
}
