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

    $pdo = new DatabaseFunctions($db);
    
    ob_start();
    session_start();
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
        $Firstname = htmlspecialchars($_POST['Name'] ?? null);
        $Lastname = htmlspecialchars($_POST['Lastname'] ?? null);
        $Email = $_POST['Email'] ?? null;
        $Password = $_POST['Password'] ?? null;
        $PasswordCheck = $_POST['PasswordValid'] ?? null;
        

        $error = [];
        define("Location", "../signup/signup.php");
        
        $params = [
            ":email" => $Email
        ];
        $Exists = $pdo->countByPDOWithCondition("user","*","email = :email", $params);
        if($Exists > 0){
            $error["AccountExists"] = "Account already exists";
        }

        $InputCounter = 0;
        foreach ($_POST as $value) {
            $InputCounter++;
            while($InputCounter > 6){
                $error["InputError"] = "Unexpected amount of Inputs [ 6 Expected ]";
                break;
            }
        }
        if(!filter_var($Email, FILTER_VALIDATE_EMAIL)){
            $error["EmailFormat"] = "Invalid Email format";
        }

        if(empty($Password) || empty($PasswordCheck) || empty($Firstname) || empty($Lastname) || empty($Email)){
            $error["Empty"] = "All fields are requiered";
        }
        $error = array_merge($error, PasswordValidation($Password, $PasswordCheck));
        $error = array_merge($error, PasswordMatch($Password, $PasswordCheck));

        SessionLog($error, Location);
       foreach($_POST as $key2 => $value2){
            $HeaderInitiate = true;
            if($key2 == "Name"){
                $_SESSION["Name"] = $_POST['Name'] ?? null;
            }
            if($key2 == "Lastname"){
                $_SESSION["Lastname"] = $_POST['Lastname'] ?? null;
            }
            if($key2 == "Email"){
                $_SESSION["Email"] = $_POST['Email'] ?? null;
            }else{
                $HeaderInitiate = false;
            }

            if($HeaderInitiate == true){
                header("location:../signup/signup.php");
            } 
        }

        if(SessionLog($error, Location) == false){
            $_SESSION["Password"]= HashSalt($Password,$Email);
            $_SESSION["NameDB"]=$Firstname;
            $_SESSION["LastnameDB"]=$Lastname;
            $vercode = rand(100000,999999);
            $_SESSION["Vercode"] = $vercode;
            $_SESSION["Email"] = $Email;

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
            
            // Add a recipient 
            $mail->addAddress($Email); 
            

            $mail->isHTML(true);
            $mail->Subject = 'Verification Code'; 
            $bodyContent = '<h1>ok</h1>'; 
            $bodyContent .= '<p>Here is your Verification code :'.$vercode.'</p>'; 
            $bodyContent .= '<p>If you didnt ask for this Email please contact our support</p>';
            $mail->Body  = $bodyContent; 
            
            if($mail->send()) { 
                header("location:../signup/signup2.php");
                ob_end_flush();
            }
        }
        
    }
?>