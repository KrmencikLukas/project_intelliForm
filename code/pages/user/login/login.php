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
    <title>Log in</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../../../assets/global/vars.css">
    <link rel="stylesheet" href="../../../assets/global/general.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/viewPassword.js"></script>

</head>
<body>
    <div id="BodyWrap">
        <div id="ButtonWrap">
            <a href="" target="_self" id="BackBtn"><span></span> go back</a>
        </div>
        <div id="Main">
            <form method="post" action="../action/loginAction.php">
                <h1>Log in</h1>
                <input type="Email" name="Email" placeholder="E-mail" value="<?= KeepValue("Email")?>">
                <?= notify("EmailFormat")?>
              
                <div class="password-input-container">
                    <input class="password-input" type="password" name="Password" placeholder="Password">
                    <span class="show-password"></span>
                </div>
                
                <?= notify("Empty")?>
                <?= notify("AccountExists")?>
                <?= notify("InputError")?>

                <button type="submit" name="submit">Log in</button>
                <p id="HaveAccount">Dont have an account? <a href="../signup/signup.php" target="_self">Sign up </a> now</p>
 
            </form>
        </div>
    </div>
</body>
</html>