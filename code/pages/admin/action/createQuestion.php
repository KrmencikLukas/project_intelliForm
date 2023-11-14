<?php
    
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $DBlib = new DatabaseFunctions($db);

   //defaultní hodnoty
   //
    $heading="Enter question name...";
    $desc="Description question name...";
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
            echo 1;
        }else{
            echo 0;
        }
    }else{
        echo 0;
    }
?>