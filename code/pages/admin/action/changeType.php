<?php

    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $DBlib = new DatabaseFunctions($db);

    //Načtení id a type z POST
    if((isset($_POST["id"]))&&(isset($_POST["type"]))){
        $id = $_POST["id"];
        $type = $_POST["type"];
        if((is_numeric($id))&&(is_numeric($type))){

            //zjištění jaký je aktuální type
            $QuestionID = ["id" => $id];
            $oldType=$DBlib->fetchDataWithCondition("question", "type_id", "id=:id", $QuestionID);

            //změna typu v tabulce question
            $QuestionType=[
                "id" => $id,
                "type_id" => $type,
            ];
            $DBlib->updateData("question",$QuestionType,"id = :id");

            //odstranení nastavení
            $QuestionSettingsInfo=[
                "id" => $id,
            ];
            $DBlib->deleteDataWithCondition("question_settings","question_id = :id",$QuestionSettingsInfo);

            // a vytvoření nového nastavení podle default_settings
            $getDefault = [":id" => $type];
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
            if ((($oldType[0]["type_id"]=="0")||($oldType[0]["type_id"]=="3"))&&(($type=="0")||($type=="3"))) {
                $Answers=[
                    ":id" => $id,
                ];
                $sql = "UPDATE `answer` SET `correctness`=0 WHERE `question_id`=:id";

                $sql = $db->prepare($sql);
                $sql->execute($Answers);
            } elseif ((($oldType[0]["type_id"]=="1")||($oldType[0]["type_id"]=="2")||($oldType[0]["type_id"]=="4"))&&(($type=="1")||($type=="2")||($type=="4"))){
                $Answers=[
                    ":id" => $id,
                ];
                $sql = "UPDATE `answer` SET `correctness`=0 WHERE `question_id`=:id";

                $sql = $db->prepare($sql);
                $sql->execute($Answers);
            } elseif ((($oldType[0]["type_id"]=="1")||($oldType[0]["type_id"]=="2")||($oldType[0]["type_id"]=="4"))&&(($type=="0")||($type=="3"))){
                $Answers=[
                    "id" => $id,
                ];
                $DBlib->deleteDataWithCondition("answer","question_id = :id",$Answers);

                $questionAnswers=[
                    "question_id" => $id,
                    "name" => "Yes",
                    "correctness" => "0",
                ];
                $DBlib->insertData("answer", $questionAnswers);
                $questionAnswers=[
                    "question_id" => $id,
                    "name" => "No",
                    "correctness" => "0",
                ];
                $DBlib->insertData("answer", $questionAnswers);
            } elseif ((($oldType[0]["type_id"]=="0")||($oldType[0]["type_id"]=="3"))&&(($type=="1")||($type=="2")||($type=="4"))) {
                $Answers=[
                    "id" => $id,
                ];
                $DBlib->deleteDataWithCondition("answer","question_id = :id",$Answers);
            }
            
            echo 1;
        }else{
            echo 0;
        }
    }else{
        echo 0;
    }
?>