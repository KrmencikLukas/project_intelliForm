<?php
    function notify($key) {
        $check = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        $return = "";
        if($check != null){
            $return .= "<div class='ErrorDis'><p>".$check."</p></div>";
        }
        
        return $return;
    }
    
    function KeepValue($value){
        $keep = $_SESSION[$value] ?? null;
        return $keep;
    }
    function PasswordValidation($arg1, $arg2) {
        $errors = [];
    
        // Check minimum length
        if (strlen($arg1) < 8 || strlen($arg2) < 8) {
            $errors[] = "8 letters";
        }
    
        // Check for at least one capital letter
        if (!($arg1 !== mb_strtolower($arg1) || $arg2 !== mb_strtolower($arg2))) {
            $errors[] = "1 capital letter";
        }
    
        // Check for at least one number
        if (!preg_match('/\d/', $arg1) && !preg_match('/\d/', $arg2)) {
            $errors[] = "1 number";
        }
    
        // Check for at least one special character
        if (!preg_match('/[!@#$%^&*()_+{}:;<>,.?~]/', $arg1) && !preg_match('/[!@#$%^&*()_+{}:;<>,.?~]/', $arg2)) {
            $errors[] = "1 special character";
        }
    
        if (count($errors) > 0) {
            return ["PasswordValid" => "Password must contain a minimum of: " . implode(', ', $errors)];
        } else {
            return false;
        }
    }
    function PasswordMatch($arg1, $arg2){
        $return = [];
        
        if($arg1 != $arg2){
            $return["PasswordMatch"] = "Passwords do not match";
        }
        return $return;
    }
    /* Pro pouziti mergenout funcki s error polem
         PasswordValidation($Password, $PasswordCheck);
            $error = array_merge($error, PasswordValidation($Password, $PasswordCheck));
    */
    
    function SessionLog($array = [], $headerURL){
        $HeaderActivate = true;
        $log = "";
        foreach($array as $key => $value){
            if($key){
                $_SESSION[$key] = $value;
            }else{
                $HeaderActivate = false;
            }
            if($HeaderActivate == true){
                header("location:".$headerURL);
            }
        }
        if(empty($array)){
            $log = false;
        }else{
            $log = true;
        }
        return $log;
    } 

?>