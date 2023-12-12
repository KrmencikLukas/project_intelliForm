<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    require ("../../../assets/lib/php/PHPMailer-master/src/PHPMailer.php");
    require ("../../../assets/lib/php/PHPMailer-master/src/Exception.php");
    require ("../../../assets/lib/php/PHPMailer-master/src/SMTP.php");
    use PHPMailer\PHPMailer\PHPMailer; 
    use PHPMailer\PHPMailer\SMTP; 
    use PHPMailer\PHPMailer\Exception; 

    $pdo = new DatabaseFunctions($db);

    session_start();

    $form = (int)$_POST["form"] ?? null;

    ob_start();
    if($form){
        $recipients = $pdo->fetchDataWithCondition("guest", "*", "form_id =:form AND method = 0 AND sent = 0",[":form" => $form]);
        $formSpecifications = $pdo->fetchDataWithCondition("form","*","id = :id",[":id" => $form]);
        $formSpecifications2 = $pdo->fetchDataWithCondition("form_settings","value","form_id= :id AND `key` = 'anonymous' ",[":id" => $form] );


        foreach($formSpecifications as $value){
            $private = $value["everyone"];
            $formName = $value["name"];
        }
        foreach($formSpecifications2 as $value3){
            $anonym = $value3["value"];
        }

        $link ="";
        foreach($recipients as $value2){

            if($private == 0){
                $link = "id={$form}&guestId={$value2["id"]}&code={$value2["code"]}";
            }else{
                $link = "id={$form}";
            }
            $font = "Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;";
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
            
            $mail->addAddress($value2["email"]); 
            

            $mail->isHTML(true);
            $mail->Subject = 'Form invitation'; 
            $bodyContent = '<div style=" display:flex; flex-direction:column; font-family:'.$font.'">';
            $bodyContent .= "<h1 style='margin-left: 30px;'>Form invite </h1>";
            $bodyContent .= "<p style='margin-left: 40px;'>You have recevied an invite to fill out {$formName} .</p>";
            $bodyContent .= "<a style='width: fit-content; background-color: black; color: white; font-size: 24px; align-self: center; font-weight: bold; padding: 5px; border-radius: 6px; margin-left:100px;' href='http://project.lukaskrmencik.cz/D/code/pages/user/form.php?{$link}' target='_blank'>Open</a>";
            $bodyContent .= '<p style="margin-left: 40px;">If you are having any problems please contact our support</p></div>';
            $mail->Body  = $bodyContent; 
            if($mail->send()) { 
                echo "yes yes";
            }//this is for testing
        }
    }
?>