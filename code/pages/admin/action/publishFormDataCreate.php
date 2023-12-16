<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    include("../../../assets/lib/php/HashLibrary.php");

    $pdo = new DatabaseFunctions($db);

    $name = htmlspecialchars($_POST['name'] ?? null);
    $surname = htmlspecialchars($_POST['surname'] ?? null);
    $email = $_POST['email'] ?? null;
    $form = (int)$_POST["form"] ?? null;
    $guest_id = (int)$_POST["guest"] ?? null;

    $error = [];
    if(!empty($email) && !empty($guest_id)){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error["EmailFormat"] = "Invalid Email format";
        }
        $params = [
            ":email" => $email,
            ":form" => $form,
            ":id" => $guest_id
        ];
        $Exists = $pdo->countByPDOWithCondition("guest","*","email = :email AND form_id = :form AND id != :id", $params);
        if($Exists > 0){
            $error["Duplicate"] = "Duplicate guests are not allowed";
        }

        if(!$error){
            $updateData = [
                "email" => $email,
                "name" => $name,
                "surname" => $surname,
            ];
            $pdo->updateDataNormal("guest",$updateData, ["id" => $guest_id], "id = :id");

            $CreatedGuest=$pdo->fetchDataWithCondition("guest", "*", "id = :id", [":id" => $guest_id]);

            echo json_encode($CreatedGuest);
        }else{
            echo json_encode($error);
        }

    }else{
        echo "lukas krmencik";
    }
?>