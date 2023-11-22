<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    include("../../../assets/lib/php/general.php");
    session_start();
    $db = new DatabaseFunctions($db);
    
    $user = $_SESSION['user'] ?? null;
    $params = [
        ":id"=> $user
    ];
    var_dump($_SESSION);
    $forms = $db->fetchDataWithCondition("form", "*","user_id = :id ORDER BY timestamp DESC LIMIT 4", $params);
    $countForms = $db->countByPDOWithCondition("form", "*","user_id = :id ", $params);
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
    <script>
        var countForm = <?= json_encode($countForms)?>;
        var user = <?= json_encode($user)?>
    </script>
</head>
<body>
    <?php 
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
                        echo "
                        <a href='../editor.php?id=" . $val["id"] . "' target='_self'>
                            <div class='form'>
                                <h2>" . $val["name"] . "</h2>
                                <div>" . timeAgo($val["timestamp"], "Last edited") . "</div>
                                <div class='actions'>
                                    <p><span class='mdi mdi-earth'></span></p>
                                    <p><span class='mdi mdi-delete del'></span></p>
                                </div>
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