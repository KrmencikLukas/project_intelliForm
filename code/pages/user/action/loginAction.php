<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/RegisterLibrary.php");
    include("../../../assets/lib/php/DBlibrary.php");
    include("../../../assets/lib/php/HashLibrary.php");

    $pdo = new DatabaseFunctions($db);

    session_start();
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){

        $Email = $_POST['Email'] ?? null;
        $Password = $_POST['Password'] ?? null;

        $error = [];
        define("Location", "../login/login.php");

        $params = [
            ":email" => $Email,
            ":password" => HashSalt($Password, $Email)
        ];
        $Exists = $pdo->countByPDOWithCondition("user","*","email = :email AND password = :password", $params);
        if($Exists == 0){
            $error["AccountExists"] = "Account doesnt exist";
        }

        $InputCounter = 0;
        foreach ($_POST as $value) {
            $InputCounter++;
            while($InputCounter > 3){
                $error["InputError"] = "Unexpected amount of Inputs [ 3 Expected ]";
                break;
            }
        }
        if(!filter_var($Email, FILTER_VALIDATE_EMAIL)){
            $error["EmailFormat"] = "Invalid Email format";
        }
        if(empty($Password) || empty($Email)){
            $error["Empty"] = "All fields are requiered";
        }

        SessionLog($error, Location);
        foreach($_POST as $key2 => $value2){
            $HeaderInitiate = true;
            if($key2 == "Email"){
                $_SESSION["Email"] = $_POST['Email'] ?? null;
            }else{
                $HeaderInitiate = false;
            }

            if($HeaderInitiate == true){
                header("location:../login/login.php");
            } 
        }
        $Exists2 = $pdo->countByPDOWithCondition("user","*","email = :email AND password = :password", $params);
        if($Exists2 == 1){
            $res = $pdo->fetchDataWithCondition("user","*","email = :email AND password = :password",$params);
            foreach($res as $val){
                $_SESSION["user"] = $val["id"];
            }
            if( $_SESSION["user"] != null){
                header("location: ../../admin/Dashboard/Dashboard.php");
            }
        }

    }

?>