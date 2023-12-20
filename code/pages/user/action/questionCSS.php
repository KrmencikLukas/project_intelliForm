<?php

//funkce css otázek
//zadají se id otázek a vrátí ti css
function SetQuestionCSS ($questionIDs, $questions, $DBlib){
    $returnCSS="";
    for ($i=0; $i < count($questionIDs); $i++) { 
        $questionID = [ "id" => $questions[$i]["id"]];
        $questionCSS=$DBlib->fetchDataWithCondition("question_settings", "*", "question_id = :id", $questionID);

        if (isset($questionCSS)) {
            foreach ($questionCSS as $key => $value) {
                if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Background color")) {
                    $returnCSS=$returnCSS.'.q'.$questions[$i]["id"].' {background-color:'.$value["value"].';}'."\n";
                }
                if (($value["question_id"]==$questions[$i]["id"])&&($value["key"]=="Text color")) {
                    $returnCSS=$returnCSS.'.q'.$questions[$i]["id"].', .q'.$questions[$i]["id"].' div div div div label {color:'.$value["value"].';}'."\n";
                }
            }
        }
    }
    return $returnCSS;
}

?>