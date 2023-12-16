<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    
    $pdo = new DatabaseFunctions($db);

    $form = (int)$_POST["form"] ?? null;
    $method = (int)$_POST["method"] ?? null;
    $guest = (int)$_POST["guest"] ?? null;

    if($form && $method == 0 || $method == 1){
        $arr2 = [
            "method" => $method,
        ];
        $update = $pdo->updateDataNormal("guest",$arr2 ,[":id" => $guest, ":form" => $form],"id = :id AND form_id = :form");
        echo $update;
    }