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
    <link rel="icon" type="image/png" href="../../assets/img/logo/favicon.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../assets/lib/js/profileMenu.js"></script>
    <script src="animation.js"></script>
    <link rel="icon" type="image/png" href="../../assets/img/logo/favicon.png">
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
    <div id="AboutPlatform">
        <div id="greetings">
            <h2>Welcome to our innovative platform for form creation</h2>
        </div>
        <div class="info">
            <img src="../../assets/img/homepage/form.gif" alt="form" id="formGif" class="infoImage">
            <div class="text">
                <h3>Question Customization</h3>
                <p>We give you control over the appearance and features of your forms.</p>
            </div>
        </div>
        <div class="info">
            <img src="../../assets/img/homepage/easy.jpg" alt="form" class="infoImage">
            <div class="text">
                <h3>Easy to use</h3>
                <p>Our user-friendly interface allows easy form creation without the need for complex technical skills.</p>
            </div>
        </div>
        <div class="info">
            <img src="../../assets/img/homepage/chart.jpeg" alt="form" class="infoImage">
            <div class="text">
                <h3>Real-time Results</h3>
                <p>We instantly display results and graphs, allowing you to quickly and efficiently analyze responses.</p>
            </div>
        </div>
        <div class="info">
            <div class="text">
                <a href="../admin/Dashboard/Dashboard.php">Let's make some forms!</a>
            </div>
        </div>
    </div>
    <div id="footer">
        <div id="firstLine">
            <div id="logobottom">
                <img src="../../assets/img/logo/logoWhite.svg" alt="logo" id="invert-color"> 
            </div>
            <a href="">Homepage</a>
            <a href="../user/login/login.php">Login</a>
            <a href="../user/signup/signup.php">Sign Up</a>
            <a href="../admin/Dashboard/Dashboard.php">Dashboard</a>
            <a href="../user/about.html">About</a>
            <a href="../user/questionsAnswers.html">Q&A</a>
        </div>
        <div id="else">
            <ul>
                <li><a href="../user/about.html#pp">Privacy Policy</a></li>
                <li><a href="../user/about.html#tou">Terms of Use</a></li>
            </ul>
            <div id="copyright">
                <p>Try our platform today and start collecting and analyzing information efficiently and stylishly. Thank you for being with us!</p>
                <p>Copyright Formative 2023</p>
            </div>
        </div>
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