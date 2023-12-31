<?php
    include("../lib/php/db.php");
    include("../lib/php/DBlibrary.php");

    $pdo = new DatabaseFunctions($db);

    $user = $_POST["userID"] ?? null;
    if($user){
        $arr = [ ":id" => $user];
        $resp = $pdo->fetchDataWithCondition("user","*","id = :id", $arr);
        
        $name ="";
        $surname = "";
        $email = "";
        
        foreach($resp as $val){
            $name =$val["name"];
            $surname = $val["surname"];
            $email = $val["email"];
            $image = $val["pf_image"];
        }
        $userData = [
            "name" => $name,
            "surname" => $surname,
            "email" => $email,
            "image" => $image
        ];


        echo json_encode($userData);
    }
?>