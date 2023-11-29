<?php
    
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    session_start();
    $DBlib = new DatabaseFunctions($db);

   //defaultní hodnoty
   //
    $heading="";
    $desc="";
   //

    //Načtení id a type z POST
    if((isset($_POST["id"]))&&(isset($_POST["type"]))){
        $id = $_POST["id"];
        $type = $_POST["type"];
        $typeId = $DBlib->fetchDataWithCondition("question_type", "id", "number = :type", [":type" => $type])[0]["id"];
        if((is_numeric($id))&&(is_numeric($type))){

            if(($_SESSION["user"] ?? NULL) == $DBlib->fetchDataWithCondition("form", "user_id", "id = :id", [":id" => $id,])[0]["user_id"]){
                //vytvoření otázky
                $questionAttributes=[
                    "heading" => $heading,
                    "description" => $desc,
                    "form_id" => $id,
                    "type_id" => $typeId,
                ];
                $questionID=$DBlib->insertData("question", $questionAttributes);

                //zjištění defaultních hodnot
                $getDefault = [":id" => $typeId];
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
                if ($type == 0 || $type == 3) {
                    if($type == 3){
                        $correctness = 1;
                    }else{
                        $correctness = 0;
                    }
                        
                    $questionAnswers=[
                        "question_id" => intval($questionID),
                        "name" => "Yes",
                        "correctness" => $correctness,
                    ];
                    $DBlib->insertData("answer", $questionAnswers);
                    $questionAnswers=[
                        "question_id" => intval($questionID),
                        "name" => "No",
                        "correctness" => 0,
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
    }else{
        echo 0;
    }
?> 