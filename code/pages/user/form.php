<?php
    include("../../assets/lib/php/db.php");
    include("../../assets/lib/php/DBlibrary.php");
    include("action/formCSS.php");
    include("action/writeForm.php");
    include("action/questionCSS.php");
    include("action/isInDB.php");
    $DBlib = new DatabaseFunctions($db);

    //testovaci
    //http://project.lukaskrmencik.cz/S/code/pages/user/form.php?id=82&guestId=1&code=suprkod

    
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
                                header("Location: formSubmitted.php?id=".$id);
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
    <title><?=$FormName[0]["name"]?> | Formative</title>
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