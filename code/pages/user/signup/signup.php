<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/RegisterLibrary.php");
    session_start();    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup step 1</title>
    <link rel="stylesheet" href="../css/signup1.css">
    <link rel="stylesheet" href="../../../assets/global/vars.css">
    <link rel="stylesheet" href="../../../assets/global/general.css">
    <link rel="icon" type="image/png" href="../../../assets/img/logo/favicon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/viewPassword.js"></script>

</head>
<body>
    <div id="BodyWrap">
        <div id="ButtonWrap">
            <a href="../../homepage/homepage.php" target="_self" id="BackBtn"><span></span> go back</a>
        </div>
        <div id="Main">
            <form method="post" action="../action/signupAction.php">
                <h1>Sign up</h1>
                <input type="text" name="Name" placeholder="Firstname" value="<?= KeepValue("Name")?>">
                <input type="text" name="Lastname"  placeholder="Lastname" value="<?= KeepValue("Lastname")?>">
                <input type="Email" name="Email" placeholder="E-mail" value="<?= KeepValue("Email")?>">
                <?= notify("EmailFormat")?>
              
                <div class="password-input-container">
                    <input class="password-input" type="password" name="Password" placeholder="Password">
                    <span class="show-password"></span>
                </div>
                <div class="password-input-container">
                    <input type="password" name="PasswordValid" placeholder="Confirm password" class="password-input">
                    <span class="show-password"></span>


                </div>
                <?= notify("Empty")?>
                <?= notify("PasswordValid")?>
                <?= notify("PasswordMatch")?>
                <?= notify("InputError")?>
                <?= notify("AccountExists")?>
                <button type="submit" name="submit">Sign up</button>
                <p id="HaveAccount">Already have an account? <a href="../login/login.php" target="_self">Log in</a></p>
                <p id="Disclaimer">By clicking Sign up, you agree to the <a target="_self" href>Terms of service</a>and <a href="" target="_self">Privacy policy</a></p>
            </form>
        </div>
    </div>
</body>
</html>