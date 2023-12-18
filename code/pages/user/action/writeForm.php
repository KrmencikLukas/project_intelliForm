<?php

//funkce na vypisování formu ve form.php
function WriteForm ($questionIDs, $questions, $DBlib, $echoForm, $values){

    //tady oznamuji o špatně vyplněném formu
    if ((isset($values["reason"])&&($values["reason"]=="mandatory"))) {
        $echoForm=$echoForm.'<div class="question alert"><div class="formDescriptionContainer"><p class="description">Make sure to check all *Mandatory questions!</p></div></div>';
    }elseif ((isset($values["reason"])&&($values["reason"]=="minmax"))) {
        $echoForm=$echoForm.'<div class="question alert"><div class="formDescriptionContainer"><p class="description">Don\'t forget to tick the correct number of answers!</p></div></div>';
    }

    //vypisování jednotlivých otázek
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

        //tady jsou další informace o vyplnění - jestli je otázka povinna, jaký počet odpovědí má zaškrtnout
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

        //popisek
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

?>