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
    <title>Document</title>
    <link rel="stylesheet" href="../css/signup1.css">
    <link rel="stylesheet" href="../../../assets/global/vars.css">
    <link rel="stylesheet" href="../../../assets/global/general.css">
</head>
<body>
    <div id="BodyWrap">
        <div id="ButtonWrap">
            <a href="" target="_self" id="BackBtn">&#x21A9; go back</a>
        </div>
        <div id="Main">
            <form method="post" action="../action/signupAction.php">
                <h1>Sign up</h1>
                <input type="text" name="Name" placeholder="Firstname" value="<?= KeepValue("Name")?>">
                <input type="text" name="Lastname"  placeholder="Lastname" value="<?= KeepValue("Lastname")?>">
                <input type="Email" name="Email" placeholder="E-mail" value="<?= KeepValue("Email")?>">
                <?= notify("EmailFormat")?>
                <input type="password" name="Password" placeholder="Password">
                <input type="password" name="PasswordValid" placeholder="Confirm password">
                <?= notify("PasswordValid")?>
                <?= notify("PasswordMatch")?>
                <?= notify("InputError")?>
                <?= notify("AccountExists")?>
                <button type="submit" name="submit">Sign up</button>
                <p>Already have an account? <a>Log in</a></p>
            </form>
        </div>
    </div>
</body>
</html>