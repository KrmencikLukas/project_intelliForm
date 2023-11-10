<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/RegisterLibrary.php");
    include("../../../assets/lib/php/DBlibrary.php");
    include("../../../assets/lib/php/HashLibrary.php");

    require ("../../../assets/lib/php/PHPMailer-master/src/PHPMailer.php");
    require ("../../../assets/lib/php/PHPMailer-master/src/Exception.php");
    require ("../../../assets/lib/php/PHPMailer-master/src/SMTP.php");
    use PHPMailer\PHPMailer\PHPMailer; 
    use PHPMailer\PHPMailer\SMTP; 
    use PHPMailer\PHPMailer\Exception; 
    session_start();
    ob_start();

    $pdo = new DatabaseFunctions($db);
    define("Location", "../signup/signup2.php");

    if(isset($_SESSION["Email"])){
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){

            $Firstname =  $_SESSION["NameDB"] ?? null;
            $Lastname =  $_SESSION["LastnameDB"]?? null;
            $Email =   $_SESSION["Email"]?? null;
            $Password =  $_SESSION["Password"] ?? null;
            $Vercode =  $_SESSION["Vercode"] ?? null;
            
            $VercodePost = $_POST["VerifCode"] ?? null;

            $error = [];

            $InputCounter = 0;
            foreach ($_POST as $value) {
                $InputCounter++;
                while($InputCounter > 2){
                    $error["InputError"] = "Unexpected amount of Inputs [ 2 Expected ]";
                    break;
                }
            }
            
            if(!is_numeric($VercodePost)){
                $error["NaN"] = "Verification code must be a number";
            }

            if(is_numeric($VercodePost)){
                if(strlen((string)$VercodePost) != 6){
                    $error["Not6Digit"] = "Verification code must be exactly 6 digits long";
                }
            }
            if($VercodePost == $Vercode){
                $params = [
                    ":email"=> $Email,
                ];
                $Exists = $pdo->countByPDOWithCondition("user","*","email = :email", $params);
                if($Exists > 0){
                   header("location: ../signup/signup.php");
                }else{
                    $arr = [
                        "name"=> $Firstname,
                        "surname"=> $Lastname,
                        "email"=> $Email,
                        "password"=> $Password,
                    ];
                    $id = $pdo->insertData("user", $arr);
                    $_SESSION["user"] = $id;
                    header("location:../signup/signup.php");
                    ob_end_flush();
                    exit;
                }
              
            }else{
                $error["WrongCode"] = "The code is incorrect";
            }

            SessionLog($error, Location);


        }
        if(isset($_COOKIE['last_activation_time']) && isset($_POST["resend"])){
            $currentTime = time();
            $lastActivationTime = $_COOKIE['last_activation_time'];
            $TimeElapsed = $currentTime - $lastActivationTime;
            if ($TimeElapsed < 300) {
                $timeRemaining = 300 - $TimeElapsed;
                $minutesRemaining = floor($timeRemaining / 60);
                $secondsRemaining = $timeRemaining % 60;
                if($VerificationCode != $_SESSION["Vercode"]){
                    $_SESSION["Cooldown"] = "Email can only be resend once every 5 minutes. Please try again in <span id='countdown'>$minutesRemaining:$secondsRemaining</span> minutes.";
                    $_SESSION["Update"] = $timeRemaining;
                    header("location:".Location);
                }
                exit;
            }
        }
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["resend"])){
            $vercode = rand(100000,999999);
            $_SESSION["Vercode"] = $vercode;
            $Email =   $_SESSION["Email"]?? null;

            $mail = new PHPMailer; 
         
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
            $mail->isSMTP();        
            $mail->Host = 'wes1-smtp.wedos.net';                
            $mail->SMTPAuth = true;
            $mail->Username = 'projekt@lukaskrmencik.cz';
            $mail->Password = 'oSjdi~_7pn';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;                 

            $mail->setFrom('projekt@lukaskrmencik.cz', 'Verification code'); 
            $mail->addReplyTo('projekt@lukaskrmencik.cz', 'Verification code'); 
            
            $mail->addAddress($Email); 
            
            $mail->isHTML(true);
            $mail->Subject = 'Verification Code'; 
            $bodyContent = '<div style=" display:flex; flex-flow: column nowrap;">'; 
            $bodyContent .= '<p style="width: fit-content; background-color: black; color: white; font-size: 24px; align-self: center;">Here is your Verification code :'.$vercode.'</p>'; 
            $bodyContent .= '<p>If you didnt ask for this Email please contact our support</p>';
            $mail->Body  = $bodyContent; 
            
            if($mail->send()) { 
                setcookie('last_activation_time', time(), time()+300);
                header("location:../signup/signup2.php");
            }
        }
    }else{
        header("location: ../signup/signup.php");
    }
?>