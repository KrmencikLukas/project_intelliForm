<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/RegisterLibrary.php");
    session_start();    
    $update = $_SESSION["Update"] ?? null;
    unset($_SESSION["Update"]);
    if(!isset($_SESSION["Email"])){
        header("location:signup.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up step 2</title>
    <link rel="stylesheet" href="../css/signup2.css">
    <link rel="stylesheet" href="../../../assets/global/vars.css">
    <link rel="stylesheet" href="../../../assets/global/general.css">
</head>
<body>
    <div id="BodyWrap">
        <div id="Main">
            <form method="post" action="../action/signupAction2.php">
                <h1>Email verification</h1>
                <input type="number" name="VerifCode" placeholder="00-00-00">
                <?= notify("InputError")?>
                <?= notify("NaN")?>
                <?= notify("Not6Digit")?>
                <?= notify("WrongCode")?>
                <button type="submit" name="submit">Sign up</button>
                <div id="resendiv">
                    <p id="resend">Didnt recieve the Email? <button type="submit" id="resendbtn" placeholder="Resend" name="resend">Resend Email</button></p>
                    <?=notify("Cooldown")?>
                </div>
            </form>
        </div>  
    </div>
    <script>
        var countdownElement = document.getElementById('countdown');
        var timeRemaining = <?=$update ?? null?>;

        function updateCountdown() {
            var minutes = Math.floor(timeRemaining / 60);
            var seconds = timeRemaining % 60;

            countdownElement.innerHTML = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

            if (timeRemaining <= 0) {
                clearInterval(countdownInterval);
            } else {
                timeRemaining--;
            }
        }
        
        var countdownInterval = setInterval(updateCountdown, 1000);
        updateCountdown(); 
    </script>
</body>
</html>