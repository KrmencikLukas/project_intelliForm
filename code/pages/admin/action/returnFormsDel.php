<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $userId = (int)$_POST["userID"] ?? null;
    $count = (int)$_POST["count"] ?? null;

    $pdo = new DatabaseFunctions($db);

    if(!empty($userId) && !empty($count)){
        $params = [ 
            ":id"=> $userId,
            ":offset" => $count
        ];

        $result = $pdo->fetchDataWithCondition("form","*", "user_id = :id ORDER BY timestamp DESC LIMIT :offset ", $params);

        echo json_encode($result);
    }
?>
        