<?php

session_start();

include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");

$DBlib = new DatabaseFunctions($db);


//funkce na css formu
function SetFormCSS($formID){
    global $DBlib;
    $formCSSkey = $DBlib->fetchDataWithCondition("form_settings", "`key`", "form_id = :id", [":id"=>$formID]);
    $formCSSvalue = $DBlib->fetchDataWithCondition("form_settings", "`value`", "form_id = :id", [":id"=>$formID]);

    $returnCSS="";
    for ($i=0; $i < count($formCSSkey); $i++) { 
        if ($formCSSkey[$i]["key"]=="font") {
            $returnCSS=$returnCSS."p, .questionHeading, .question label {font-family:".$formCSSvalue[$i]["value"]."}";
        }
    }
    return $returnCSS;
  }
  
//funkce na vypisování formu
function WriteQuestion ($question){
    global $DBlib;
    $echoForm = "";
    $answerIDs="";
    $answers="";
    $questionID = [ "id" => $question["id"]];
  
    $answerIDs=$DBlib->fetchDataWithCondition("answer", "id", "question_id = :id", $questionID);
    $answers=$DBlib->fetchDataWithCondition("answer", "*", "question_id = :id", $questionID);
    
    //vypisuje divy na otázky (bez odpovědí) a popis otázky (pokud je)
    if (($question["type_id"]==1)||($question["type_id"]==5)) {
        $echoForm=$echoForm.'<div class="question type0 q'.$question["id"].'">';
    } else {
        $echoForm=$echoForm.'<div class="question q'.$question["id"].'">';
    }
  
    $questionSettings=$DBlib->fetchDataWithCondition("question_settings", "*", "question_id = :id", $questionID);
  
    if (isset($questionSettings)) {
        foreach ($questionSettings as $key => $value) {
            if (($value["question_id"]==$question["id"])&&($value["key"]=="Mandatory")&&($value["value"]=="1")) {
                $echoForm=$echoForm.'<div class="mandatory"><p>*Mandatory</p></div>';
            }
        }
    }
  
    $echoForm=$echoForm.'<h2 class="questionHeading">'.$question["heading"].'</h2><div class="descriptionContainer"><p class="description">'.nl2br(str_replace(" ","&nbsp;",$question["description"])).'</p></div><div class="answers">';
    
    //vypisuje odpovedi podle typu otázky a hodnot v db
    for ($x=0; $x < count($answerIDs); $x++) { 
        $correctness = "";
        if(isset($_POST["guest"])){
            if(isset($question["answers"][$x]["guests"])){
                foreach($question["answers"][$x]["guests"] as $key => $value){
                    if($value["guest_id"] == $_POST["guest"]){
                        $correctness = "checked";
                    }
                }
            }
        }else{
            if($answers[$x]["correctness"] == 1){
                $correctness = "checked";
            }
        }

        if (($question["type_id"]==1)||($question["type_id"]==5)||($question["type_id"]==4)) {
            if (($question["type_id"]==1)||($question["type_id"]==5)) {
                if ($x==0) {
                    $echoForm=$echoForm.'<div class="answer yes"><div class="pretty p-locked p-toggle p-plain"><input type="radio" name="'.$answers[$x]["id"].'" '.$correctness.'><div class="state p-off"><label>Yes</label></div><div class="state p-on"><label class="color">Yes</label></div></div><div class="peopleCount'.$answers[$x]["id"].' peopleCount"></div></div>';
                } else {
                    $echoForm=$echoForm.'<div class="answer no"><div class="pretty p-locked p-toggle p-plain"><input type="radio" name="'.$answers[$x]["id"].'"'.$correctness.'><div class="state p-off"><label>No</label></div><div class="state p-on"><label class="color">No</label></div></div><div class="peopleCount'.$answers[$x]["id"].' peopleCount"></div></div>';
                }
            } elseif ($question["type_id"]==4) {
                $echoForm=$echoForm.'<div class="answer typeA2"><div class="pretty p-locked p-icon p-round p-smooth p-bigger p-toggle up answerBox"><input type="checkbox" name="'.$answers[$x]["id"].'"'.$correctness.'><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-up"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-up"></i><label></label></div></div><div class="pretty p-locked p-icon p-round p-smooth p-bigger p-toggle down answerBox"><input type="checkbox"><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-down"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-down"></i><label></label></div></div><p class="answerDesc">'.$answers[$x]["name"].'</p><div class="peopleCount'.$answers[$x]["id"].' peopleCount"></div></div>';
            }
            
        } else {
            $echoForm=$echoForm.'<div class="answer"><div class="pretty p-locked p-icon p-round p-smooth p-bigger answerBox"><input type="checkbox" name="'.$answers[$x]["id"].'" '.$correctness.'><div class="state p-primary"><i class="icon mdi mdi-check"></i><label></label></div></div>';
            $echoForm=$echoForm.'<p class="answerDesc">'.$answers[$x]["name"].'</p><div class="peopleCount'.$answers[$x]["id"].' peopleCount"></div></div>';
        }
    }
    $echoForm=$echoForm.'</div></div>';
  
    return $echoForm;
}
  
  //funkce css otázek
function SetQuestionCSS ($question){
    global $DBlib;
    $returnCSS="";
    $questionID = [ "id" => $question["id"]];
    $questionCSS=$DBlib->fetchDataWithCondition("question_settings", "*", "question_id = :id", $questionID);
  
    if (isset($questionCSS)) {
        foreach ($questionCSS as $key => $value) {
            if (($value["question_id"]==$question["id"])&&($value["key"]=="Background color")) {
                $returnCSS=$returnCSS.'.q'.$question["id"].' {background-color:'.$value["value"].'}';
            }
            if (($value["question_id"]==$question["id"])&&($value["key"]=="Text color")) {
                $returnCSS=$returnCSS.'.q'.$question["id"].', .q'.$question["id"].' div div div div label {color:'.$value["value"].'}';
            }
        }
        }
    return $returnCSS;
}


if(isset($_POST["data"])){
    echo WriteQuestion ($_POST["data"]);
}

?>

<script>
    $("#customStyles").append("<?php echo SetQuestionCSS($_POST["data"]) . " " . SetFormCSS($_POST["data"]["form_id"]) ?>");
</script>