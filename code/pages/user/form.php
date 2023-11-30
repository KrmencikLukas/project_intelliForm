<?php
    include("../../assets/lib/php/db.php");
    include("../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    //testovaci
    //http://project.lukaskrmencik.cz/S/code/pages/user/form.php?id=82&guestId=1&code=suprkod

    //kontrola adresy
    $id=$_GET["id"] ?? null;
    if ((!empty($id))&&(is_numeric($id))) {
        $formID = [ "id" => $id];
        $public=$DBlib->fetchDataWithCondition("form", "public", "id = :id", $formID);
        //kontrola jestli je public
        if (!empty($public)) {
            if ($public[0]["public"]==1) {
                $FormName=$DBlib->fetchDataWithCondition("form", "name", "id = :id", $formID);
                $formCSSkey=$DBlib->fetchDataWithCondition("form_settings", "`key`", "form_id = :id", $formID);
                $formCSSvalue=$DBlib->fetchDataWithCondition("form_settings", "`value`", "form_id = :id", $formID);

                $echoCSS="";
                for ($i=0; $i < count($formCSSkey); $i++) { 
                    if ($formCSSkey[$i]["key"]=="color") {
                        $echoCSS=$echoCSS.".form {background-color:".$formCSSvalue[$i]["value"]."}";
                    }
                    if ($formCSSkey[$i]["key"]=="background color") {
                        $echoCSS=$echoCSS."body {background-color:".$formCSSvalue[$i]["value"]."}";
                    }
                }
                $echoForm='<div class="question"><h1 class="formHeading">'.$FormName[0]["name"].'</h1><div class="formDescriptionContainer"><p class="description">This form is anonymous. Your answers will not be linked to you in any way.</p></div></div>';

                $questionIDs=$DBlib->fetchDataWithCondition("question", "id", "form_id = :id", $formID);
                $questions=$DBlib->fetchDataWithCondition("question", "*", "form_id = :id", $formID);

                
            } else {
                if ((!empty($_GET["guestId"]))&&(!empty($_GET["code"]))&&(is_numeric($_GET["guestId"]))) {
                    
                    $guest=$_GET["guestId"];
                    $code=$_GET["code"];

                    $guestCodeID = [ 
                        "id" => $guest,
                        "code" => $code,
                    ];
                    $guestEmail=$DBlib->fetchDataWithCondition("guest", "email", "id = :id AND code = :code", $guestCodeID);
                    
                    if (!empty($guestEmail)) {
                        $FormName=$DBlib->fetchDataWithCondition("form", "name", "id = :id", $formID);
                        $formCSSkey=$DBlib->fetchDataWithCondition("form_settings", "`key`", "form_id = :id", $formID);
                        $formCSSvalue=$DBlib->fetchDataWithCondition("form_settings", "`value`", "form_id = :id", $formID);

                        $echoCSS="";
                        for ($i=0; $i < count($formCSSkey); $i++) { 
                            if ($formCSSkey[$i]["key"]=="color") {
                                $echoCSS=$echoCSS.".form {background-color:".$formCSSvalue[$i]["value"]."}";
                            }
                            if ($formCSSkey[$i]["key"]=="background color") {
                                $echoCSS=$echoCSS."body {background-color:".$formCSSvalue[$i]["value"]."}";
                            }
                        }

                        $echoForm='<div class="question"><h1 class="formHeading">'.$FormName[0]["name"].'</h1><div class="formDescriptionContainer"><p class="description">Your email adress <span class="emailAdress">'.$guestEmail[0]["email"].'</span> is going to be saved with your answers.</p></div></div>';

                        $questionIDs=$DBlib->fetchDataWithCondition("question", "id", "form_id = :id", $formID);
                        $questions=$DBlib->fetchDataWithCondition("question", "*", "form_id = :id", $formID);

                        

                    } else {
                        header("Location: ../error.php");
                    }
                } else {
                    header("Location: ../error.php");
                }
            }
            for ($i=0; $i < count($questionIDs); $i++) { 
                $answerIDs="";
                $answers="";
                $questionID = [ "id" => $questions[$i]["id"]];

                $answerIDs=$DBlib->fetchDataWithCondition("answer", "id", "question_id = :id", $questionID);
                $answers=$DBlib->fetchDataWithCondition("answer", "*", "question_id = :id", $questionID);
                
                if (($questions[$i]["type_id"]==1)||($questions[$i]["type_id"]==5)) {
                    $echoForm=$echoForm.'<div class="question type0">';
                } else {
                    $echoForm=$echoForm.'<div class="question">';
                }
                $echoForm=$echoForm.'<h2 class="questionHeading">'.$questions[$i]["heading"].'</h2><div class="descriptionContainer"><p class="description">'.$questions[$i]["description"].'</p></div><div class="answers">';
                
                for ($x=0; $x < count($answerIDs); $x++) { 
                    if (($questions[$i]["type_id"]==1)||($questions[$i]["type_id"]==5)||($questions[$i]["type_id"]==4)) {
                        if (($questions[$i]["type_id"]==1)||($questions[$i]["type_id"]==5)) {
                            if ($x==0) {
                                $echoForm=$echoForm.'<div class="answer yes"><div class="pretty p-toggle p-plain"><input type="radio" name="radio1" checked=""><div class="state p-off"><label>Yes</label></div><div class="state p-on"><label class="color">Yes</label></div></div></div>';
                            } else {
                                $echoForm=$echoForm.'<div class="answer no"><div class="pretty p-toggle p-plain"><input type="radio" name="radio1"><div class="state p-off"><label>No</label></div><div class="state p-on"><label class="color">No</label></div></div></div>';
                            }
                        } elseif ($questions[$i]["type_id"]==4) {
                            $echoForm=$echoForm.'<div class="answer typeA2"><div class="pretty p-icon p-round p-smooth p-bigger p-toggle up answerBox"><input type="checkbox"><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-up"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-up"></i><label></label></div></div><div class="pretty p-icon p-round p-smooth p-bigger p-toggle down answerBox"><input type="checkbox"><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-down"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-down"></i><label></label></div></div><p class="answerDesc">'.$answers[$x]["name"].'</p></div>';
                        }
                        
                    } else {
                        $echoForm=$echoForm.'<div class="answer"><div class="pretty p-icon p-round p-smooth p-bigger answerBox"><input type="checkbox"><div class="state p-primary"><i class="icon mdi mdi-check"></i><label></label></div></div>';
                        $echoForm=$echoForm.'<p class="answerDesc">'.$answers[$x]["name"].'</p>';
                    }
                    
                
                }
                
                $echoForm=$echoForm.'</div></div>';
            }
            $echoForm=$echoForm.'<input type="submit" value="Submit" name="submit" id="submit">';
            
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
    <style>
        <?=$echoCSS?>
    </style>
</head>
<body>
    <div id="content">
        <div class="form">
            <?=$echoForm?>
        </div>
    </div>
</body>
</html>