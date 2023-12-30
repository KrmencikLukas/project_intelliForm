<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    
    $pdo = new DatabaseFunctions($db);

    $form = (int)$_POST["form"] ?? null;
    $everyone = (int)$_POST["everyone"] ?? null;

    if($form && $everyone == 0 || $everyone == 1){
        $arr2 = [
            "everyone" => $everyone,
        ];
        $update = $pdo->updateDataNormal("form",$arr2 ,[":id" => $form],"id = :id");
        echo $everyone;
    }