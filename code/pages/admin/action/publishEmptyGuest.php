<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    include("../../../assets/lib/php/HashLibrary.php");

    $pdo = new DatabaseFunctions($db);
    $form = (int)$_POST["form"] ?? null;
    $params = [
        ":form" => $form
    ];
    $Exists = $pdo->countByPDOWithCondition("guest","*","email IS NULL AND form_id = :form", $params);
    if($Exists == 0){
        if($form){
            $emptyGuest = $pdo->insertData("guest",["form_id" => $form,"code" => generateRandomCode()]);
    
            echo json_encode($emptyGuest);
        }
    }

?>