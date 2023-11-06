<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    //$id = $_POST["id"];
    $id = 1;
    
    $form = $DBlib->fetchDataWithCondition("form", "*", "id=:id",[":id"=>$id])[0];

    /*
    {
        ID: 1, //ID formu co se upravuje v DB, může být null pokud je neznámé
        name: "", //Název formuláře
        settings: {
            bgColor: "#N3V1M" //Příklad nastavení
            //Zde může být neomezeně dalších nastavení
        },
        questions: [
            {
                heading: "", //Nadpis otázky
                description: "", //Popis otázky
                type: 1, //Typy budou očíslované od 0 do 2
                media: [
                    "/mujobrazek.png", //Cesta k souboru, v určité složce
                    //Zde může být neomezeně dalších cest
                ],
                settings: {
                    bgColor: "#N3V1M" //Příklad nastavení
                    //Zde může být neomezeně dalších nastavení
                },
                answers: [
                    "text odpovedi",
                    //Zde může být neomezeně dalších odpovědí
                ]
            },
            //Zde může být neomezeně dalších otázek
        ]
    
    }
    */

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

    function questions(){
        global $DBlib, $id;
        $questions = $DBlib->fetchDataWithCondition("question", "*", "form_id=:id",[":id"=>$id]);
        $questionArr = [];
        foreach($questions as $value){
            $questionArr[] = [
                    "heading"=> $value["heading"],
                    "description"=> $value["description"],
                    "type"=> 1,
                    "media"=> [
                        "/mujobrazek.png",
                    ],
                    "settings"=> questionSettings($value["id"]),
                    "answers"=> [
                        "text odpovedi",
                    ]
            ];
        }
        return $questionArr;
    }


    $json = [
        "ID" => $id,
        "name" => $form["name"],
        "settings" => formSettings(),
        "questions" => questions(),
    ];

?>