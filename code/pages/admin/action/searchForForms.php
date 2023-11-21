<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $userId = $_POST["userID"] ?? null;
    $search = $_POST["search"] ?? null;

    $pdo = new DatabaseFunctions($db);

    if ($userId) {

        $params = [
            ':id' => $userId,
            ':name' => '%' . trim($search) . '%'
        ];
        
        $results = $pdo->fetchDataWithCondition("form", "*", "user_id = :id AND name LIKE :name LIMIT 4", $params);
        
        echo json_encode($results);
    }
?>