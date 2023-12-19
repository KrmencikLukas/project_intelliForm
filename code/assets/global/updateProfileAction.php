<?php
    include("../lib/php/db.php");
    include("../lib/php/DBlibrary.php");
    include("../lib/php/HashLibrary.php");
    include("../lib/php/RegisterLibrary.php");

    $pdo = new DatabaseFunctions($db);
    session_start();

    $folder = "../img/uploads/"; 
    $endtag = [
        "png","jpeg","jpg","webp","gif",
    ]; 
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){

        $id = $_GET["id"] ?? null;
        $Firstname = htmlspecialchars($_POST['Name'] ?? null);
        $Lastname = htmlspecialchars($_POST['Surname'] ?? null);
        $Email = $_POST['Email'] ?? null;
        $Password = $_POST['Password'] ?? null;
        $PasswordCheck = $_POST['PasswordValid'] ?? null;
        $Default = $_POST["Default"]??null;
        
        $ImageFile = $_FILES['image']['name'];
        $file = $_FILES['image']['tmp_name'];
        $path = $folder.$ImageFile;
        $targetFile = $folder.basename($ImageFile);
        $ImageFileType = pathinfo($targetFile, PATHINFO_EXTENSION);
 

        $error = [];
        define("Location", "updateProfile.php?id=".$id);
        
        $params = [
            ":email" => $Email,
            ":id" => $id
        ];
        $Exists = $pdo->countByPDOWithCondition("user","*","email = :email AND id = :id ", $params);
        if($Exists == 0){
            $error["AccountExists"] = "Account already exists";
        }

        $InputCounter = 0;
        foreach ($_POST as $value) {
            $InputCounter++;
            while($InputCounter > 7){
                $error["InputError"] = "Unexpected amount of Inputs [ 6 Expected ]";
                break;
            }
        }
        if(!filter_var($Email, FILTER_VALIDATE_EMAIL)){
            $error["EmailFormat"] = "Invalid Email format";
        }


        if(!empty($ImageFile)){
            $ImageDetect = false;
            foreach($endtag as $value2){
                if(strtolower($ImageFileType) == $value2){
                    $ImageDetect = true;
                }
    
            }
            if(!$ImageDetect){
                $error["NotImage"] = "Selected file is not an image";
            }
    
        }


        if($_FILES["image"]["size"] > 10408576){
            $error["Size"] = "Selected file is larger than 10Mbs";
        }
        
        if(empty($Password) || empty($PasswordCheck) || empty($Firstname) || empty($Lastname) || empty($Email)){
            $error["Empty"] = "All fields are requiered";
        }

        
        $error = array_merge($error, PasswordValidation($Password, $PasswordCheck));
        $error = array_merge($error, PasswordMatch($Password, $PasswordCheck));

        SessionLog($error, Location);

        if(empty($ImageFile) && $Default == true){
            $ImageFile = "user.svg";
        }
        
        if(!file_exists("../img/uploads/".$ImageFile)){
            move_uploaded_file($file, $targetFile);
        }


        
        if(SessionLog($error, Location) == false){
            if(empty($ImageFile) && $Default == false){
                $pdo->updateDataNormal("user",["name" => $Firstname, "surname" => $Lastname, "email" => $Email, "password" => HashSalt($Password, $Email)],[":id" => $id], "id = :id");
                $_SESSION["Success"] = "Profile updated successfully";
                header("location:".Location);
            }else{
                $pdo->updateDataNormal("user",["name" => $Firstname, "surname" => $Lastname, "email" => $Email, "password" => HashSalt($Password, $Email), "pf_image" => $ImageFile],[":id" => $id], "id = :id");
                $_SESSION["Success"] = "Profile updated successfully";
                header("location:".Location);
            }
        }
    }else{
        header("location:".Location);
    }
    
?>