<?php
    
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $DBlib = new DatabaseFunctions($db);

   //defaultní hodnoty
   //
    $heading="Enter question name...";
    $desc="Enter question description...";
   //

    //Načtení id a type z POST
    if((isset($_POST["id"]))&&(isset($_POST["type"]))){
        $id = $_POST["id"];
        $type = $_POST["type"];
        if((is_numeric($id))&&(is_numeric($type))){
            //vytvoření otázky
            $questionAttributes=[
                "heading" => $heading,
                "description" => $desc,
                "form_id" => $id,
                "type_id" => $type,
            ];
            $questionID=$DBlib->insertData("question", $questionAttributes);

            //zjištění defaultních hodnot
            $getDefault = [":id" => $type];
            $defaultKey=$DBlib->fetchDataWithCondition("default_settings", "`key`", "type_id=:id", $getDefault);
            $defaultValue=$DBlib->fetchDataWithCondition("default_settings", "value", "type_id=:id", $getDefault);

            $countDefaultSettings=count($defaultKey);

            for ($i=0; $i < $countDefaultSettings; $i++) { 
                //vytvoření nastavení otázky
                $settingsAttributes=[
                    ":keyValue" => $defaultKey[$i]["key"],
                    ":valueValue" => $defaultValue[$i]["value"],
                    ":question_id" => intval($questionID),
                ];
                $sql = "INSERT INTO `question_settings`(`key`, `value`, `question_id`) VALUES (:keyValue,:valueValue,:question_id)";

                $sql = $db->prepare($sql);
                $sql->execute($settingsAttributes);
            }

            //když ano/ne otázka, vytvoření odpovědí ano/ne
            $DatabaseQuestionType = $DBlib->fetchDataWithCondition("question_type", "name", "number = :id", $getDefault);
            
            if (($DatabaseQuestionType[0]["name"]=="Yes/No quiz")||($DatabaseQuestionType[0]["name"]=="Yes/No poll")) {
                $questionAnswers=[
                    "question_id" => intval($questionID),
                    "name" => "Yes",
                    "correctness" => "0",
                ];
                $DBlib->insertData("answer", $questionAnswers);
                $questionAnswers=[
                    "question_id" => intval($questionID),
                    "name" => "No",
                    "correctness" => "0",
                ];
                $DBlib->insertData("answer", $questionAnswers);
            }
            echo 1;
        }else{
            echo 0;
        }
    }else{
        echo 0;
    }
?> 