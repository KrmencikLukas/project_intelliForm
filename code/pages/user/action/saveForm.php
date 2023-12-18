<?php

//ukládá zodpovězené otázky do DB

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
?>