<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $userId = (int)$_POST["userId"] ?? null;

    $pdo = new DatabaseFunctions($db);

    if($userId){
        $params = [
            ":id"=> $userId
        ];
        $countForms = $pdo->countByPDOWithCondition("form", "*","user_id = :id ", $params);
        
       echo json_encode($countForms);
    }
?>