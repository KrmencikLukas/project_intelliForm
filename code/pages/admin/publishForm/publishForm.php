<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    include("../../../assets/lib/php/general.php");

    session_start();
    $pdo = new DatabaseFunctions($db);

    $formId = $_GET["id"] ?? null;
    $user = $_SESSION["user"] ?? null;


    $currentGuests = $pdo->fetchDataWithCondition("guest","*","email IS NOT NULL AND form_id = :form", [":form" => $formId]);
    $params = [
        ":form" => $formId
    ];


    $Exists = $pdo->countByPDOWithCondition("guest","*","email IS NULL AND form_id = :form", $params);
    $EmptyData = $pdo->fetchDataWithCondition("guest","*","email IS NULL AND form_id = :form", $params);
    foreach($EmptyData as $value2){
        $emptyId = $value2["id"];
    }


    $formSpecifications = $pdo->fetchDataWithCondition("form","everyone","id= :id",["id" => $formId]);
    foreach($formSpecifications as $value3){
        $everyone = $value3["everyone"];
    }

    if(!isset($_SESSION['user'])){
        header("location: ../../user/login/login.php");
    }
    if(!isset($_GET["id"])){
        header("location:../Dashboard/Dashboard.php");
    }   
    if(!is_numeric($_GET["id"])){
        header("location:../Dashboard/Dashboard.php");
    }   
    $getValid = $pdo->countByPDOWithCondition("form", "*", "id = :id", [":id" => $formId]);
    if($getValid == 0){
        header("location:../Dashboard/Dashboard.php");
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publish form</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../../../assets/global/general.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/publish.css">  
    <link rel="icon" type="image/png" href="../../../assets/img/logo/favicon.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../../assets/lib/js/profileMenu.js"></script>
    <script src="../js/publish.js"></script></script>
    <script>
        var user = <?= json_encode($user)?>;
        <?php if($Exists){
            echo  "var unfinishedEmptyGuest =". json_encode($emptyId);
        }?>;
    </script>
</head>
<body>
    <?php
        $location = "Form publish";
        include("../../../assets/global/headerSidebar.php");
    ?>
    <div id="content" class="wrap">
        <div id="main">

            <h2>Form publish</h2>
            <h3>who can vote?</h3>
            <div class="custom-radio">
                <input type="radio" id="everyone" name="user-type" 
                <?php 
                    if($everyone == 1){
                        echo "checked";
                    }
                ?> >
                <label for="everyone">Everyone</label>

                <input type="radio" id="Peoplelink" name="user-type"                
                <?php 
                    if($everyone == 0){
                        echo "checked";
                    }
                ?> 
                >
                <label for="Peoplelink">People with Link</label>
            </div>
            <div id="invites">
                <h2>Invite people</h2>
                <button id="inviteButton" style="<?php echo $Exists == 1 ? "display:none;":""  ?>">Invite</button>
                <div id="inviteForm" style="<?php echo $Exists == 1 ? "display:flex;":""  ?>" >
                    <div id="fields">
                        <div id="left">
                            <div>
                                <h3>Name</h3>
                                <input type="text" id="name">
                            </div>
                            <div>
                                <h3 id="surnameh3">Surname</h3>
                                <input type="text" id="surname">
                            </div>
                        </div>
                        <div id="right">
                            <div>
                                <h3>Email</h3>
                                <input type="email" id="email">
                            </div>
                            <div id="methodwrap">
                                <h3 id="methodTitle">Send method</h3>
                                <div id="method" class="custom-radio">
                                    <input type="radio" id="byEmail" name="user-method" checked value="1">
                                    <label for="byEmail">Via email</label>

                                    <input type="radio" id="byLink" name="user-method" value="2">
                                    <label for="byLink">Personally</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="link"><h3>Link</h3><input id="copyField" type="text" disabled><span class="mdi mdi-content-copy copybtnform"></span></div>
                    <div id="actions">
                        <button id="save">Save</button>
                        <button id="delete">Delete</button>
                    </div>
                </div>
                <div id="invitedPeople">
                    <h2>Invited people</h2>
                    <div id="invited">
                        <?php
                            foreach($currentGuests as $value){
                                $method =$value["method"] == 0 ? "via email": "via link"; 
                                $copy = $value["method"] == 1 ? "<span class='mdi mdi-content-copy copybtn'></span>" : "";
                                echo "
                                    <div class='guest' data-email={$value["email"]} data-id='{$value["id"]}'>
                                        <div class='titles'>
                                            <h2>{$value["email"]}</h2>
                                            <p>{$value["name"]}&nbsp;&nbsp;{$value["surname"]}</p>
                                        </div>
                                        <p class='method'>{$method}</p>
                                        <div class='btns'>
                                            <button class='editButton'>Edit</button>
                                            <button class='deleteButtonMini'>Delete</button>
                                        </div>
                                        <div class='copy'>{$copy}</div>
                                    </div>
                                ";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="side">
            <button id="sendbtn">Send Email</button>
        </div>
    </div>
</body>
</html>