<?php
    include("../lib/php/db.php");
    include("../lib/php/DBlibrary.php");
    include("../lib/php/general.php");
    include("../lib/php/RegisterLibrary.php");
    session_start();

    $pdo = new DatabaseFunctions($db);

    $id = $_GET["id"] ?? null;
    $user = $_SESSION["user"] ?? null;

    $userData = $pdo->fetchDataWithCondition("user","*","id = :id",[":id" => $id]);
    foreach($userData as $value){
        $name = $value["name"];
        $surname =$value["surname"];
        $email = $value["email"];
        $image =$value["pf_image"];
    }
    $folder = "../img/uploads/";  
    $SuccessAlert = $_SESSION["Success"] ?? null;
    unset($_SESSION["Success"]);
    if(!isset($_SESSION['user'])){
        header("location: ../../code/pages/user/login/login.php");
    }
    if(!isset($_GET["id"])){
        header("location:../../pages/admin/Dashboard/Dashboard.php");
    }   
    if(!is_numeric($_GET["id"])){
        header("location:../../pages/admin/Dashboard/Dashboard.php");
    }   
    if($user != $id){
        header("location:../../pages/admin/Dashboard/Dashboard.php");
    } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User update</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/logo/favicon.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../lib/js/profileMenu.js"></script>
    <link rel="stylesheet" href="general.css">
    <link rel="stylesheet" href="update.css">
    <link rel="stylesheet" href="../lib/css/slimSelect.css">
    <link rel="stylesheet" href="../lib/css/pretty-checkbox/dist/pretty-checkbox.min.css">
    <script>
        var user = <?php echo json_encode($user)?>    
    </script>
</head>
<body>
    <?php
        $location = "User update";
        include("headerSidebar.php");
    ?>
     <div id="content" class="wrap">
        <form id="main" action="updateProfileAction.php?id=<?=$id?>" method="post" enctype="multipart/form-data">
            <h2 id="title">User update</h2>
            <div id="fields">
                <h3>User data:</h3>
                <input type="text" placeholder="name" value="<?= $name?>" name="Name">
                <input type="text" placeholder="surname"  value="<?= $surname?>" name="Surname">
                <input type="email" placeholder="email" value="<?= $email?>" name="Email">
                <input type="password" placeholder="new password" name="Password">
                <input type="password" placeholder="confirm new password" name="PasswordValid">
                <?= notify("Empty")?>
                <?= notify("PasswordValid")?>
                <?= notify("PasswordMatch")?>
                <?= notify("InputError")?>
                <?= notify("AccountExists")?>
            </div>
            <div id="img">
                <h3>Profile picture:</h3>
                <input type="file" name="image" id="custom-file" onchange="previewImage(event)">
                <div id="PrewDiv"><img id="preview" src=""></div>
                <div id="CheckboxDiv"><h3>Set to default:</h3><div class="pretty p-default p-curve p-smooth">
                    <input type="checkbox" name="Default">
                    <div class="state  p-primary-o">
                        <label></label>
                    </div>
                </div>
</div>
                <?= notify("Size")?>
                <?= notify("NotImage")?>
            </div>
            <div id="send">
                <button type="submit" name="submit">Update</button>
            </div>
            <?php
                    if($SuccessAlert != null){
                        echo "<div id='Success'><h2>*</h2><p>".$SuccessAlert."</p></div>";           
                    } 
                ?>
        </form>
     </div>
     <script>
            $(document).ready(function(){
                $("#PrewDiv").html("<img id='preview' src='<?php echo $folder.$image ?>'>")
                $("#preview").css("display","block")
            })
            setTimeout(function (){
                if($("#Success").length){
                    $('#Success').remove();
                }
            }, 5000)
            function previewImage(event) {
                $("#PrewDiv").html("<img id='preview'>")
                var input = event.target;

                if (input.files && input.files[0]) {

                    var file = input.files[0];
                    var fileType = file.type.toLowerCase(); 
                    var validExtensionsRegex = /\.(jpg|jpeg|png|webp|gif|svg)$/i;

                    if (validExtensionsRegex.test(file.name)){
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var preview = document.getElementById('preview');
                            preview.src = e.target.result;
                            preview.style.display = "block";
                        }
                        reader.readAsDataURL(input.files[0]);
                    }else{
                        $("#PrewDiv").html("<p id='ImageStatus'>Unsupported filetype</p>")
                    }

                }
            }
        </script>
</body>
</html>