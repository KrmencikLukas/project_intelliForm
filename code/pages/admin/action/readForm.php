<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    //Načtení id z POST
    $_POST["id"] = 8;

    if(isset($_POST["id"])){
        $id = $_POST["id"];
        if(is_numeric($id)){
            //Načtení formu z DB
            $form = $DBlib->fetchDataWithCondition("form", "*", "id=:id",[":id"=>$id])[0];

            //Složení JSONU
            $json = [
                "id" => $id,
                "name" => $form["name"],
                "user" => $form["user_id"],
                "settings" => formSettings(),
                "questions" => questions(),
            ];

            //Enkódování JSONU Z php pole a vypsání
            echo json_encode($json);
            print_r($json);
        }else{
            echo 0;
        }
    }else{
        echo 0;
    }


    //Funkce pro skládání JSONU

    function questionSettings($id){
        global $DBlib;
        $settings = $DBlib->fetchDataWithCondition("question_settings", "*", "question_id=:id",[":id"=>$id]);
        $settingsArr = [];
        foreach($settings as $value){
            $settingsArr[$value["id"]] = [
                "key" => $value["key"],
                "value" => $value["value"],
            ];
        }
        return $settingsArr;
    }

    function formSettings(){
        global $DBlib, $id;
        $settings = $DBlib->fetchDataWithCondition("form_settings", "*", "form_id=:id",[":id"=>$id]);
        $settingsArr = [];
        foreach($settings as $value){
            $settingsArr[$value["id"]] = [
                "key" => $value["key"],
                "value" => $value["value"],
            ];
        }
        return $settingsArr;
    }

    function answers($id){
        global $DBlib;
        $answers = $DBlib->fetchDataWithCondition("answer", "*", "question_id=:id",[":id"=>$id]);
        $answersArr = [];
        foreach($answers as $value){
            $answersArr[$value["id"]] = [
                "name" => $value["name"],
                "correctness" => $value["correctness"],
            ];
        }
        return $answersArr;
    }

    function question_media($id){
        global $DBlib;
        $media = $DBlib->fetchDataWithCondition("question_media", "*", "question_id=:id",[":id"=>$id]);
        $mediaArr = [];
        foreach($media as $value){
            $mediaArr[$value["id"]] = $value["path"];
        }
        return $mediaArr;
    }

    function questionType($id){
        global $DBlib;
        $type = $DBlib->fetchDataWithCondition("question_type", "*", "id=:id",[":id"=>$id])[0];
        return [
            "id" => $type["id"],
            "number" => $type["number"],
            "name" => $type["name"],
            "description" => $type["description"],
        ];
    }

    function questions(){
        global $DBlib, $id;
        $questions = $DBlib->fetchDataWithCondition("question", "*", "form_id=:id",[":id"=>$id]);
        $questionArr = [];
        foreach($questions as $value){
            $questionArr[$value["id"]] = [
                    "heading"=> $value["heading"],
                    "description"=> $value["description"],
                    "type"=> questionType($value["type_id"]),
                    "media"=> question_media($value["id"]),
                    "settings"=> questionSettings($value["id"]),
                    "answers"=> answers($value["id"]),
            ];
        }
        return $questionArr;
    }

?>