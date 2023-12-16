<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $pdo = new DatabaseFunctions($db);

    session_start();

    $form = (int)$_POST["form"] ?? null;
    $EditGuest =(int)$_POST["EditGuest"] ?? null;

    if($form && $EditGuest){
        $recipients = $pdo->fetchDataWithCondition("guest", "*", "form_id =:form AND method = 1 AND id = :id",[":form" => $form, ":id" => $EditGuest]);
        $formSpecifications = $pdo->fetchDataWithCondition("form","*","id = :id",[":id" => $form]);
        $formSpecifications2 = $pdo->fetchDataWithCondition("form_settings","value","form_id= :id AND `key` = 'anonymous' ",[":id" => $form] );

        foreach($formSpecifications as $value){
            $private = $value["everyone"];
        }
        foreach($formSpecifications2 as $value3){
            $anonym = $value3["value"];
        }

        $link2 = "";
        foreach($recipients as $value2){
            if($private == 0){
                $link2 = "id={$form}&guestId={$value2["id"]}&code={$value2["code"]}";
            }else{
                $link2 = "id={$form}";
            } 
        }
        echo json_encode($link2);
    }
?>