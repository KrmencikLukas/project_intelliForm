<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    //$id = $_POST["id"];
    $id = 1;
    
    $form = $DBlib->fetchDataWithCondition("form", "*", "id=:id",[":id"=>$id])[0];

    $json = [
        "ID" => $id,
        "name" => $form["name"],
        "settings" => formSettings(),
        "questions" => questions(),
    ];

    print_r($json);


    function questionSettings($id){
        global $DBlib;
        $settings = $DBlib->fetchDataWithCondition("question_settings", "*", "question_id=:id",[":id"=>$id]);
        $settingsArr = [];
        foreach($settings as $value){
            $settingsArr[$value["key"]] = $value["value"];
        }
        return $settingsArr;
    }

    function formSettings(){
        global $DBlib, $id;
        $settings = $DBlib->fetchDataWithCondition("form_settings", "*", "form_id=:id",[":id"=>$id]);
        $settingsArr = [];
        foreach($settings as $value){
            $settingsArr[$value["key"]] = $value["value"];
        }
        return $settingsArr;
    }

    function answers($id){
        global $DBlib;
        $answers = $DBlib->fetchDataWithCondition("answer", "*", "question_id=:id",[":id"=>$id]);
        $answersArr = [];
        foreach($answers as $value){
            $answersArr[] = [
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
            $mediaArr[] = $value["path"];
        }
        return $mediaArr;
    }

    function questionType($id){
        global $DBlib;
        $type = $DBlib->fetchDataWithCondition("question_type", "*", "id=:id",[":id"=>$id])[0];
        return [
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
            $questionArr[] = [
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