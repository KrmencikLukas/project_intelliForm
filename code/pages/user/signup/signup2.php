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
    <title>Sign up step 2</title>
    <link rel="stylesheet" href="../css/signup2.css">
    <link rel="stylesheet" href="../../../assets/global/vars.css">
    <link rel="stylesheet" href="../../../assets/global/general.css">
</head>
<body>
    <div id="BodyWrap">
        <div id="Main">
            <form method="post" action="../action/signupAction.php">
                <h1>Sign up</h1>

                <button type="submit" name="submit">Sign up</button>
                <p>Already have an account? <a>Log in</a></p>
            </form>
        </div>
    </div>
</body>
</html>