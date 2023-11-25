<?php

    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $DBlib = new DatabaseFunctions($db);

    //Načtení id a type z POST
    if((isset($_POST["id"]))&&(isset($_POST["type"]))){
 
        $id = $_POST["id"];
        $type = $_POST["type"];

        if($_SESSION["user"] == $DBlib->fetchDataWithCondition("form", "user_id", "id = :id", [
            ":id" => $DBlib->fetchDataWithCondition("question", "form_id", "id = :id", [":id" => $id])[0]["form_id"],
        ])[0]["user_id"]){
            
            if((is_numeric($id))&&(is_numeric($type))){

                //zjištění jaký je aktuální type
                $QuestionID = ["id" => $id];
                $oldType =  idToNumber($DBlib->fetchDataWithCondition("question", "type_id", "id=:id", $QuestionID)[0]["type_id"]);
    
                //změna typu v tabulce question
                $QuestionType=[
                    "id" => $id,
                    "type_id" => numberToId($type),
                ];
                $DBlib->updateData("question",$QuestionType,"id = :id");
    
                //odstranení nastavení
                $QuestionSettingsInfo=[
                    "id" => $id,
                ];
                $DBlib->deleteDataWithCondition("question_settings","question_id = :id",$QuestionSettingsInfo);
    
                // a vytvoření nového nastavení podle default_settings
                $getDefault = [":id" => numberToId($type)];
                $defaultKey=$DBlib->fetchDataWithCondition("default_settings", "`key`", "type_id=:id", $getDefault);
                $defaultValue=$DBlib->fetchDataWithCondition("default_settings", "value", "type_id=:id", $getDefault);
    
                $countDefaultSettings=count($defaultKey);
    
                for ($i=0; $i < $countDefaultSettings; $i++) { 
                    //vytvoření nastavení otázky
                    $settingsAttributes=[
                        ":keyValue" => $defaultKey[$i]["key"],
                        ":valueValue" => $defaultValue[$i]["value"],
                        ":question_id" => $id,
                    ];
                    $sql = "INSERT INTO `question_settings`(`key`, `value`, `question_id`) VALUES (:keyValue,:valueValue,:question_id)";
    
                    $sql = $db->prepare($sql);
                    $sql->execute($settingsAttributes);
                }
    
                //modifikace odpovědí, které jsou ovlivněny změnou typu otázky
                if ($type=="0") {
    
                    $Answers=[
                        "id" => $id,
                    ];
                    $DBlib->deleteDataWithCondition("answer","question_id = :id",$Answers);
    
                    $questionAnswers=[
                        "question_id" => $id,
                        "name" => "Yes",
                    ];
                    $DBlib->insertData("answer", $questionAnswers);
                    $questionAnswers=[
                        "question_id" => $id,
                        "name" => "No",
                    ];
                    $DBlib->insertData("answer", $questionAnswers);
    
                } elseif (($type=="1")||($type=="2")){
    
                    $DBlib->updateDataNormal("answer", ["correctness" => NULL], ["id" => $id], "question_id = :id");
    
                } elseif ($type=="3"){
                    $Answers=[
                        "id" => $id,
                    ];
                    $DBlib->deleteDataWithCondition("answer","question_id = :id",$Answers);
    
                    $questionAnswers=[
                        "question_id" => $id,
                        "name" => "Yes",
                        "correctness" => 1,
                    ];
                    $DBlib->insertData("answer", $questionAnswers);
                    $questionAnswers=[
                        "question_id" => $id,
                        "name" => "No",
                        "correctness" => 0,
                    ];
                    $DBlib->insertData("answer", $questionAnswers);
    
                } elseif ($type=="4"){
                    $type4Answers = $DBlib->fetchDataWithCondition("answer", "*", "question_id = :id", [":id" => $id]);
                    if(count($type4Answers) > 0){
                        $isCorrectness = false;
                        foreach($type4Answers as $key => $value){
                            if($value["correctness"] == 1){
                                $isCorrectness = true;
                            }
                        }
                        if(!$isCorrectness){
    
                            $DBlib->updateDataNormal("answer", ["correctness" => 0], ["id" => $id], "question_id = :id");
    
                            $data = [
                                ":id" => $type4Answers[0]["id"] 
                            ];
    
                            $sql = "UPDATE `answer` SET `correctness`= 1 WHERE `id`=:id";
    
                            $sql = $db->prepare($sql);
                            $sql->execute($data);
                        }
                    }
                }
                
                echo 1;
            }else{
                echo 0;
            }    
        }else{
            echo 0;
        }
    }else{
        echo 0;
    }


    function numberToId($type){
        global $DBlib;
        return $DBlib->fetchDataWithCondition("question_type", "id", "number = :type", [":type" => $type])[0]["id"];
    }

    function idToNumber($id){
        global $DBlib;
        return $DBlib->fetchDataWithCondition("question_type", "number", "id = :id", [":id" => $id])[0]["number"];
    }
?>