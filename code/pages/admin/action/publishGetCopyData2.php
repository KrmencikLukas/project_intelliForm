<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $pdo = new DatabaseFunctions($db);

    session_start();

    $form = (int)$_POST["form"] ?? null;
    $EditGuest =(int)$_POST["EditGuest"] ?? null;
    $method = (int)$_POST["Method"] ?? null;
    if($form && $EditGuest && $method){
        $recipients = $pdo->fetchDataWithCondition("guest", "*", "form_id =:form AND method = :method AND id = :id",[":form" => $form, ":id" => $EditGuest, ":method" => $method]);
        $formSpecifications = $pdo->fetchDataWithCondition("form","*","id = :id",[":id" => $form]);
        $formSpecifications2 = $pdo->fetchDataWithCondition("form_settings","value","form_id= :id AND `key` = 'anonymous' ",[":id" => $form] );

        foreach($formSpecifications as $value){
            $private = $value["everyone"];
        }
        foreach($formSpecifications2 as $value3){
            $anonym = $value3["value"];
        }
        if(empty($recipients)){
            echo "emp";
        }
        $link2 = "";
        foreach($recipients as $value2){
            if($private == 0){
                $link2 = "id={$form}&guestId={$value2["id"]}&code={$value2["code"]}";
            }else{
                $link2 = "id={$form}";
            } 
        }
        if($link2 !== ""){
            echo json_encode($link2);
        }else{
            echo "avs";
        }

    }else{
        "ssd";
    }
?>