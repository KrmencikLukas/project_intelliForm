<?php

include("../../assets/lib/php/db.php");
include("../../assets/lib/php/DBlibrary.php");

$DBlib = new DatabaseFunctions($db);
session_start();

if(isset($_GET["id"])){
    if(is_numeric($_GET["id"])){
        if(isset($_SESSION["user"])){
            if($DBlib->countByPDOWithCondition("form", "id","id = :id", [":id" => $_GET["id"]])){
                if(($_SESSION["user"] ?? NULL) == $DBlib->fetchDataWithCondition("form", "user_id", "id = :id", [":id" => $_GET["id"]])[0]["user_id"]){

                    if(!isset($_GET["page"])){
                        $_GET["page"] = "summary";
                    }
        
                }else{
                    header("Location: ../error.php");
                }
            }else{
                header("Location: ../error.php");
            }
        }else{
            header("Location: ../user/login/login.php");
        }
    }else{
        header("Location: ../error.php");
    }
}else{
    header("Location: ../error.php");
}

//funkce od simona


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
function WriteForm ($questionIDs, $questions, $DBlib, $echoForm){
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
            foreach ($questionSettings as $key => $value) {
                if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Mandatory")&&($value["value"]=="1")) {
                    $echoForm=$echoForm.'<div class="mandatory"><p>*Mandatory</p></div>';
                }
            }
        }

        $echoForm=$echoForm.'<h2 class="questionHeading">'.$questions[$i]["heading"].'</h2><div class="descriptionContainer"><p class="description">'.nl2br(str_replace(" ","&nbsp;",$questions[$i]["description"])).'</p></div><div class="answers">';
        
        //vypisuje odpovedi podle typu otázky a hodnot v db
        for ($x=0; $x < count($answerIDs); $x++) { 
            if (($questions[$i]["type_id"]==1)||($questions[$i]["type_id"]==5)||($questions[$i]["type_id"]==4)) {
                if (($questions[$i]["type_id"]==1)||($questions[$i]["type_id"]==5)) {
                    if ($x==0) {
                        $echoForm=$echoForm.'<div class="answer yes"><div class="pretty p-toggle p-plain"><input type="radio" name="'.$answers[$x]["id"].'" checked=""><div class="state p-off"><label>Yes</label></div><div class="state p-on"><label class="color">Yes</label></div></div></div>';
                    } else {
                        $echoForm=$echoForm.'<div class="answer no"><div class="pretty p-toggle p-plain"><input type="radio" name="'.$answers[$x]["id"].'"><div class="state p-off"><label>No</label></div><div class="state p-on"><label class="color">No</label></div></div></div>';
                    }
                } elseif ($questions[$i]["type_id"]==4) {
                    $echoForm=$echoForm.'<div class="answer typeA2"><div class="pretty p-icon p-round p-smooth p-bigger p-toggle up answerBox"><input type="checkbox" name="'.$answers[$x]["id"].'"><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-up"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-up"></i><label></label></div></div><div class="pretty p-icon p-round p-smooth p-bigger p-toggle down answerBox"><input type="checkbox"><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-down"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-down"></i><label></label></div></div><p class="answerDesc">'.$answers[$x]["name"].'</p></div>';
                }
                
            } else {
                $echoForm=$echoForm.'<div class="answer"><div class="pretty p-icon p-round p-smooth p-bigger answerBox"><input type="checkbox" name="'.$answers[$x]["id"].'"><div class="state p-primary"><i class="icon mdi mdi-check"></i><label></label></div></div>';
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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <script>
        id = <?php echo $_GET["id"] ?>;
        page = "<?php echo $_GET["page"] ?>";
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../../assets/lib/css/pretty-checkbox/dist/pretty-checkbox.min.css">
    <link rel="stylesheet" href="../../assets/global/general.css">
    <link rel="stylesheet" href="css/statistics.css">
    <script src="../../assets/lib/js/general.js"></script>
    <script src="../../assets/lib/js/profileMenu.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/statistics.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/slim-select@latest/dist/slimselect.min.js"></script>
    <link href="../../assets/lib/css/slimSelect.css" rel="stylesheet"></link>
    <link rel="icon" type="image/png" href="../../assets/img/logo/favicon.png">
</head>
<body>

    <?php 
        $location = "Statistics";
        include("../../assets/global/headerSidebar.php") 
    ?>

    <div id="content">
        <div class="container">
            <h2>Nazev formulare</h2>
            <div class="radio">
                <input type="radio" id="summary" name="view" class="viewRadio" <?php echo ($_GET["page"] == "summary") ? "checked" : "" ?>>
                <label for="summary">Summary</label>
                <input type="radio" id="people" name="view" class="viewRadio" <?php echo ($_GET["page"] == "people") ? "checked" : "" ?>>
                <label for="people">People</label>
                <input type="radio" id="questions" name="view" class="viewRadio" <?php echo ($_GET["page"] == "questions") ? "checked" : "" ?>>
                <label for="questions">Questions</label>
                <div class="slider"></div>
            </div>

            <div class="data">
            
            </div>

        </div>
    </div>

</body>
</html>