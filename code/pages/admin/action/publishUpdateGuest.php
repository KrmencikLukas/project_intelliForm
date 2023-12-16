<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    
    $pdo = new DatabaseFunctions($db);

    $form = (int)$_POST["form"] ?? null;
    $email = $_POST["email"] ?? null;
    if($form && $email){
        $arr2 = [
            ":email" => $email,
            ":form" => $form
        ];
        $guest = $pdo->fetchDataWithCondition("guest","*","email = :email AND form_id = :form", $arr2);

        echo json_encode($guest);
    }
?>