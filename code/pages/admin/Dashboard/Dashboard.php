<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    include("../../../assets/lib/php/general.php");

    session_start();
    $pdo = new DatabaseFunctions($db);
    


    $user = $_SESSION['user'] ?? null;

    $params = [
        ":id"=> $user
    ];
    $forms = $pdo->fetchDataWithCondition("form", "*","user_id = :id ORDER BY timestamp DESC LIMIT 4", $params);
    $countForms = $pdo->countByPDOWithCondition("form", "*","user_id = :id ", $params);

    
    if(!isset($_SESSION['user'])){
        header("location: ../../user/login/login.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../../../assets/global/general.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../js/showMoreForms.js"></script>
    <script src="../../../assets/lib/js/profileMenu.js"></script>
    <script src="../js/FormActions.js"></script>
    <link rel="icon" type="image/png" href="../../../assets/img/logo/favicon.png">
    <script>
        var countForm = <?= json_encode($countForms)?>;
        var user = <?= json_encode($user)?>
    </script>
</head>
<body>
    <?php 
        $user =  $_SESSION['user'];
        $location = "Dashboard";
        include("../../../assets/global/headerSidebar.php");
    ?>
    <div id="content" class="wrap">
        <div id="main">
            <div id="upper">
                <h2>Your forms</h2>
                <input type="text" placeholder="Search" id="SearchForm">
            </div>
            <a href="../action/createForm.php" target="_self" id="Create">Create new</a>
            <div id="forms">
                <?php
                    foreach ($forms as $val) {
                        if($val["public"] == 1){
                            $actions = " <div class='actions'>
                            <p><span class='mdi mdi-chart-bar bar'></span></p>
                            <p><span class='mdi mdi-earth-plus'></span></p>
                            <p><span class='mdi mdi-delete del'></span></p>
                            </div>
                            ";
                        }else{
                            $actions = " <div class='actions'>
                            <p><span class='mdi mdi-earth'></span></p>
                            <p><span class='mdi mdi-delete del'></span></p>
                            </div>";
                        }
                        echo "
                        <a href='../editor.php?id=" . $val["id"] . "' target='_self'>
                            <div class='form'>
                                <h2>" . $val["name"] . "</h2>
                                <div>" . timeAgo($val["timestamp"], "Created") . "</div>
                                ".$actions."
                            </div>
                        </a>
                        ";
                    }
                ?>
            </div>
            <?php
                if($countForms > 4){
                    echo '<button id="more">Show more</button>';
                } 
            ?>
        </div>
    </div>
</body>
</html>