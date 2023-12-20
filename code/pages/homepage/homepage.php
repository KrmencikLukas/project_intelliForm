<?php
    session_start();
    $user = $_SESSION["user"] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" href="../../assets/global/general.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../assets/lib/js/profileMenu.js"></script>
</head>
<body>
    <div id="backgroundOverlay" class="hidden">
        <div class="preview-container">
            <div id="Arrow"></div>
            <div id="backWrap">
                <span id="close"><span class="mdi mdi-close-thick"></span></span>
            </div>
            <div id="profile">
                <img src="" alt='pf img' id="pfPic">
                <h2 id="userEmail"></h2>
                <p id="userNames"></p>
            </div>
            <div id="links">
                <a href="../../assets/global/updateProfile.php?id=<?= $user ?>" target="_self"><span class="mdi mdi-account-cog settingsIcon"></span></a>
                <a href="../../assets/global/logout.php" target="_self"><span class="mdi mdi-logout logout"></span></a>
            </div>
        </div>
    </div>
    <div id="Header">
        <div id="header">
            <div id="HeaderLeft">
                <div id="logomain">
                    <img src="../../assets/img/logo/logo.svg" alt="logo"> 
                </div>
            </div>
            <div id="PageSpecific">
            </div>
            <div id="HeaderRight">
                <?php
                    if($user){
                        echo "<a href='../admin/Dashboard/Dashboard.php' target='_self' id='dashboard'><p>Go to Dashboard</p></a><div class='userIconDiv'>
                        <img class='UserIcon' src='' alt=''></div>";
                    }else{
                        echo "<a href='../user/login/login.php' target='_self' id='login'><p>Sign in</p></a>";
                    }
                ?>
            </div>
        </div>
    </div>
    <div id="bigimage">
        <img src="" id="bg" alt="background">
        <div id="gradient-overlay"></div>
        <div id="introText"><p>Helping you shape your ideas into Interactive Forms and Polls.</p></div>
    </div>
<script type="module">
    import{updateImageSource} from "../../pages/homepage/homepage.js";
    updateImageSource();
    window.addEventListener('resize', updateImageSource);
</script>
<script>
    <?php
        if($user){
            echo "$(document).ready(function(){
        
                $.ajax({
                    url:'../../assets/global/profileMenu.php',
                    type: 'POST',
                    data:{userID:{$user}},
                    success:function(userdata){
                        let fetchedData =JSON.parse(userdata)
                        $('#userEmail').text(fetchedData.email)
                        $('#userNames').text(fetchedData.name+ '  ' + fetchedData.surname)
                        $('.UserIcon').attr('src', '../../assets/img/uploads/' + fetchedData.image)
                        $('#pfPic').attr('src', '../../assets/img/uploads/' + fetchedData.image)
                    }
                })
            }) ";
        }
    ?>
</script>

</body>
</html>