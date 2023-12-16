<?php
    include("../../assets/lib/php/db.php");
    include("../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    //testovaci
    //http://project.lukaskrmencik.cz/S/code/pages/user/form.php?id=82&guestId=1&code=suprkod

    //funkce na css formu
    function SetFormCSS($formCSSkey, $formCSSvalue){
        $returnCSS="";
        for ($i=0; $i < count($formCSSkey); $i++) { 
            if ($formCSSkey[$i]["key"]=="color") {
                $returnCSS=$returnCSS.".form {background-color:".$formCSSvalue[$i]["value"]."}";
            }
            if ($formCSSkey[$i]["key"]=="background color") {
                $returnCSS=$returnCSS."body {background-color:".$formCSSvalue[$i]["value"]."}";
            }
            if ($formCSSkey[$i]["key"]=="font") {
                $returnCSS=$returnCSS.".formHeading, p, .questionHeading, label {font-family:".$formCSSvalue[$i]["value"]."}";
            }
        }
        return $returnCSS;
    }

    //funkce na vypisování formu
    function WriteForm ($questionIDs, $questions, $DBlib, $echoForm, $values){
        if ((isset($values["reason"])&&($values["reason"]=="mandatory"))) {
            $echoForm=$echoForm.'<div class="question alert"><div class="formDescriptionContainer"><p class="description">Make sure to check all *Mandatory questions!</p></div></div>';
        }elseif ((isset($values["reason"])&&($values["reason"]=="minmax"))) {
            $echoForm=$echoForm.'<div class="question alert"><div class="formDescriptionContainer"><p class="description">Don\'t forget to tick the correct number of answers!</p></div></div>';
        }
        for ($i=0; $i < count($questionIDs); $i++) { 
            $answerIDs="";
            $answers="";
            $questionID = [ "id" => $questions[$i]["id"]];

            $answerIDs=$DBlib->fetchDataWithCondition("answer", "id", "question_id = :id", $questionID);
            $answers=$DBlib->fetchDataWithCondition("answer", "*", "question_id = :id", $questionID);
            
            //vypisuje divy na otázky (bez odpovědí) a popis otázky (pokud je)
            if (($questions[$i]["type_id"]==1)||($questions[$i]["type_id"]==5)) {
                $echoForm=$echoForm.'<div class="question type0 q'.$questions[$i]["id"].'">';
            } else {
                $echoForm=$echoForm.'<div class="question q'.$questions[$i]["id"].'">';
            }

            $questionSettings=$DBlib->fetchDataWithCondition("question_settings", "*", "question_id = :id", $questionID);

            if (isset($questionSettings)) {
                $echoForm=$echoForm.'<div class="mandatory">';
                foreach ($questionSettings as $key => $value) {
                    if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Mandatory")&&($value["value"]=="1")) {
                        $echoForm=$echoForm.'<p>*Mandatory</p>';
                    }
                    if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Min votes")) {
                        $echoForm=$echoForm.'<p>min. tick '.$value["value"].'</p>';
                    }
                    if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Max votes")) {
                        $echoForm=$echoForm.'<p>max. tick '.$value["value"].'</p>';
                    }
                    if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Min upvotes")) {
                        $echoForm=$echoForm.'<p>Upvotes '.$value["value"].' - ';
                    }
                    if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Max upvotes")) {
                        $echoForm=$echoForm.$value["value"].'</p>';
                    }
                    if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Min downvotes")) {
                        $echoForm=$echoForm.'<p>Downvotes '.$value["value"].' - ';
                    }
                    if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Max downvotes")) {
                        $echoForm=$echoForm.$value["value"].'</p>';
                    }
                }
                $echoForm=$echoForm.'</div>';
            }

            $echoForm=$echoForm.'<h2 class="questionHeading">'.$questions[$i]["heading"].'</h2><div class="descriptionContainer"><p class="description">'.nl2br(str_replace(" ","&nbsp;",$questions[$i]["description"])).'</p></div><div class="answers">';
            
            //vypisuje odpovedi podle typu otázky a hodnot v db
            for ($x=0; $x < count($answerIDs); $x++) { 
                if (($questions[$i]["type_id"]==1)||($questions[$i]["type_id"]==5)||($questions[$i]["type_id"]==4)) {
                    if (($questions[$i]["type_id"]==1)||($questions[$i]["type_id"]==5)) {
                        $checked1='';
                        $checked0='';
                        if (isset($values["*q".$questions[$i]["id"]."*t".$questions[$i]["type_id"]])) {
                            if ($values["*q".$questions[$i]["id"]."*t".$questions[$i]["type_id"]] == 1) {
                                $checked1 = 'checked=""';
                            } elseif ($values["*q".$questions[$i]["id"]."*t".$questions[$i]["type_id"]] == 0) {
                                $checked0 = 'checked=""';
                            }
                        }
                        if ($x==0) {
                            $echoForm=$echoForm.'<div class="answer yes"><div class="pretty p-toggle p-plain"><input type="radio" name="*q'.$questions[$i]["id"].'*t'.$questions[$i]["type_id"].'" value="1" '.$checked1.'><div class="state p-off"><label>Yes</label></div><div class="state p-on"><label class="color">Yes</label></div></div></div>';
                        } else {
                            $echoForm=$echoForm.'<div class="answer no"><div class="pretty p-toggle p-plain"><input type="radio" name="*q'.$questions[$i]["id"].'*t'.$questions[$i]["type_id"].'" value="0" '.$checked0.'><div class="state p-off"><label>No</label></div><div class="state p-on"><label class="color">No</label></div></div></div>';
                        }
                    } elseif ($questions[$i]["type_id"]==4) {

                        if ((isset($values['*q'.$questions[$i]["id"].'*a'.$answers[$x]["id"].'*t'.$questions[$i]["type_id"]]))&&($values['*q'.$questions[$i]["id"].'*a'.$answers[$x]["id"].'*t'.$questions[$i]["type_id"]]=="1")) {
                            $checkedUP='checked=""';
                            $checkedDOWN='';
                        } elseif ((isset($values['*q'.$questions[$i]["id"].'*a'.$answers[$x]["id"].'*t'.$questions[$i]["type_id"]]))&&($values['*q'.$questions[$i]["id"].'*a'.$answers[$x]["id"].'*t'.$questions[$i]["type_id"]]==0)) {
                            $checkedDOWN='checked=""';
                            $checkedUP='';
                        } else {
                            $checkedUP='';
                            $checkedDOWN='';
                        }
                        $echoForm=$echoForm.'<div class="answer typeA2"><div class="pretty p-icon p-round p-smooth p-bigger p-toggle up answerBox"><input type="radio" name="*q'.$questions[$i]["id"].'*a'.$answers[$x]["id"].'*t'.$questions[$i]["type_id"].'" value="1"  '.$checkedUP.'><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-up"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-up"></i><label></label></div></div><div class="pretty p-icon p-round p-smooth p-bigger p-toggle down answerBox"><input type="radio" name="*q'.$questions[$i]["id"].'*a'.$answers[$x]["id"].'*t'.$questions[$i]["type_id"].'" value="0"  '.$checkedDOWN.'><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-down"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-down"></i><label></label></div></div><p class="answerDesc">'.$answers[$x]["name"].'</p></div>';
                    }
                    
                } else {
                    if ((!empty($values["*q".$questions[$i]["id"]."*a".$answers[$x]["id"]."*t".$questions[$i]["type_id"]]))&&($values["*q".$questions[$i]["id"]."*a".$answers[$x]["id"]."*t".$questions[$i]["type_id"]]==1)) {
                        $checked='checked=""';
                    } else {
                        $checked='';
                    }
                    $echoForm=$echoForm.'<div class="answer"><div class="pretty p-icon p-round p-smooth p-bigger answerBox"><input type="checkbox" name="*q'.$questions[$i]["id"].'*a'.$answers[$x]["id"].'*t'.$questions[$i]["type_id"].'" value="1" '.$checked.'><div class="state p-primary"><i class="icon mdi mdi-check"></i><label></label></div></div>';
                    $echoForm=$echoForm.'<p class="answerDesc">'.$answers[$x]["name"].'</p></div>';
                }
            }
            $echoForm=$echoForm.'</div></div>';
        }
        
        return $echoForm;
    }

    //funkce css otázek
    function SetQuestionCSS ($questionIDs, $questions, $DBlib){
        $returnCSS="";
        for ($i=0; $i < count($questionIDs); $i++) { 
            $questionID = [ "id" => $questions[$i]["id"]];
            $questionCSS=$DBlib->fetchDataWithCondition("question_settings", "*", "question_id = :id", $questionID);

            if (isset($questionCSS)) {
                foreach ($questionCSS as $key => $value) {
                    if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Background color")) {
                        $returnCSS=$returnCSS.'.q'.$questions[$i]["id"].' {background-color:'.$value["value"].'}';
                    }
                    if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Text color")) {
                        $returnCSS=$returnCSS.'.q'.$questions[$i]["id"].', .q'.$questions[$i]["id"].' div div div div label {color:'.$value["value"].'}';
                    }
                }
            }
        }
        return $returnCSS;
    }

    //funkce na kontrolu, jestli už guest form jednou neodeselal
    function isInDatabase ($id,$DBlib){
        $params = [":id" => $id];
        $inDB= $DBlib->countByPDOWithCondition("guest_answer", "id","guest_id = :id", $params );
        if ($inDB==0) {
            return true;
        } else {
            return false;
        }
        
    }

    //session (pouze v případě špatně vyplněného emailu)
    session_start();
    $values="";
    if (!empty($_SESSION)) {
        $values=$_SESSION;
        //var_dump($values);
    }
    session_destroy();
    //kontrola adresy
    $id=$_GET["id"] ?? null;
    if ((!empty($id))&&(is_numeric($id))) {
        $formID = [ "id" => $id];
        $everyone=$DBlib->fetchDataWithCondition("form", "everyone", "id = :id", $formID);
        $anonymous=$DBlib->fetchDataWithCondition("form_settings", "value", 'form_id = :id AND `key` = "anonymous"', $formID);
        //kontrola jestli je anonymní
        if (!empty($everyone)) {
            //téměř všechna data z db
            $FormName=$DBlib->fetchDataWithCondition("form", "name", "id = :id", $formID);
            $formCSSkey=$DBlib->fetchDataWithCondition("form_settings", "`key`", "form_id = :id", $formID);
            $formCSSvalue=$DBlib->fetchDataWithCondition("form_settings", "`value`", "form_id = :id", $formID);
            $questionIDs=$DBlib->fetchDataWithCondition("question", "id", "form_id = :id", $formID);
            $questions=$DBlib->fetchDataWithCondition("question", "*", "form_id = :id", $formID);

            if ((isset($anonymous[0]["value"]))&&($anonymous[0]["value"]==1)) {
                //kontrola jestli je everyone
                if ($everyone[0]["everyone"]==1) {
                    $echoCSS=SetFormCSS($formCSSkey, $formCSSvalue);
                    //vypisuje nadpis a informuje o anonymitě
                    $echoForm='<div class="question"><h1 class="formHeading">'.$FormName[0]["name"].'</h1><div class="formDescriptionContainer"><p class="description">This form is anonymous. Your answers will not be linked to you in any way.</p></div></div>';
                    $echoForm=WriteForm ($questionIDs, $questions, $DBlib, $echoForm, $values);
                    $echoCSS=$echoCSS.SetQuestionCSS ($questionIDs, $questions, $DBlib);
                    $actionAdress="id=".$id;
                } else{
                    //kontrola jestli guest existuje
                    if ((!empty($_GET["guestId"]))&&(!empty($_GET["code"]))&&(is_numeric($_GET["guestId"]))) {
                    
                        $guest=$_GET["guestId"];
                        $code=$_GET["code"];
    
                        $guestCodeID = [ 
                            "id" => $guest,
                            "code" => $code,
                            "form_id" => $id,
                        ];
                        $guestEmail=$DBlib->fetchDataWithCondition("guest", "email", "id = :id AND code = :code AND form_id = :form_id", $guestCodeID);
                        
                        if (!empty($guestEmail)) {
                            $echoCSS=SetFormCSS($formCSSkey, $formCSSvalue);
                            //vypisuje nadpis a informuje o anonymitě
                            $echoForm='<div class="question"><h1 class="formHeading">'.$FormName[0]["name"].'</h1><div class="formDescriptionContainer"><p class="description">This form is anonymous. Your answers will not be linked to you in any way.</p></div></div>';
                            $echoForm=WriteForm ($questionIDs, $questions, $DBlib, $echoForm, $values);
                            $echoCSS=$echoCSS.SetQuestionCSS ($questionIDs, $questions, $DBlib);
                            $actionAdress="id=".$id."&guestId=".$guest."&code=".$code;
                        } else {
                            header("Location: ../error.php");
                        }
                    } else {
                        header("Location: ../error.php");
                    }
                }
            } else {
                if ($everyone[0]["everyone"]==1) {
                    $echoCSS=SetFormCSS($formCSSkey, $formCSSvalue);
                    //vypisuje nadpis a informuje že uklada email , taky chce abys ho zadal
                    if (isset($values["reason"])&&($values["reason"]=="email")) {
                        $EmailError= "<div class='error'><p>This email adress is invalid</p></div>";
                    } else {
                        $EmailError= "";
                    }
                    if (isset($values["goodEmail"])) {
                        $goodEmail=$values["goodEmail"];
                    } else {
                        $goodEmail="";
                    }
                    
                    $echoForm='<div class="question"><h1 class="formHeading">'.$FormName[0]["name"].'</h1><div class="formDescriptionContainer"><p class="description">Your email adress is going to be saved with your answers.<br>Enter a valid email adress: <input type="email" class="emailAdress" name="email" value="'.$goodEmail.'"></input></p>'.$EmailError.'</div></div>';
                    $echoForm=WriteForm ($questionIDs, $questions, $DBlib, $echoForm, $values);
                    $echoCSS=$echoCSS.SetQuestionCSS ($questionIDs, $questions, $DBlib);
                    $actionAdress="id=".$id;
                } else {
                    //kontrola jestli guest existuje
                    if ((!empty($_GET["guestId"]))&&(!empty($_GET["code"]))&&(is_numeric($_GET["guestId"]))) {
                    
                        $guest=$_GET["guestId"];
                        $code=$_GET["code"];
    
                        $guestCodeID = [ 
                            "id" => $guest,
                            "code" => $code,
                            "form_id" => $id,
                        ];
                        $guestEmail=$DBlib->fetchDataWithCondition("guest", "email", "id = :id AND code = :code AND form_id = :form_id", $guestCodeID);
                        
                        if (!empty($guestEmail)) {
                            if (isInDatabase ($guest,$DBlib)) {
                                $echoCSS=SetFormCSS($formCSSkey, $formCSSvalue);
                                //vypisuje nadpis a informuje že uklada email
                                $echoForm='<div class="question"><h1 class="formHeading">'.$FormName[0]["name"].'</h1><div class="formDescriptionContainer"><p class="description">Your email adress <span class="emailAdress">'.$guestEmail[0]["email"].'</span> is going to be saved with your answers.</p></div></div>';    
                                $echoForm=WriteForm ($questionIDs, $questions, $DBlib, $echoForm, $values);
                                $echoCSS=$echoCSS.SetQuestionCSS ($questionIDs, $questions, $DBlib);
                                $actionAdress="id=".$id."&guestId=".$guest."&code=".$code;
                            } else {
                                $echoForm='<div class="question"><h1 class="formHeading">'.$FormName[0]["name"].'</h1><div class="formDescriptionContainer"><p class="description">You have already submitted this form.</p></div></div>';    
                                $echoCSS="#submit {display: none;} .form { min-height: 0px } .description {text-align: center;}";
                            }
                        } else {
                            header("Location: ../error.php");
                        }
                    } else {
                        header("Location: ../error.php");
                    }
                }
                
            }
            
        } else {
            header("Location: ../error.php");
        }
    } else {
        header("Location: ../error.php");
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$FormName[0]["name"]?> | Intelifom</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/lib/css/pretty-checkbox/dist/pretty-checkbox.min.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="icon" type="image/png" href="../../assets/img/logo/favicon.png">
    <style>
        <?=$echoCSS?>
    </style>
</head>
<body>
    <div id="content">
        <form class="form" action="action/checkForm.php?<?=$actionAdress?>" method="post">
            <?=$echoForm?>
            <input type="submit" value="Submit" name="submit" id="submit">
        </form>
    </div>
</body>
</html>