<?php
    session_start();
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    if(isset($_POST["questionId"])){
        if(is_numeric($_POST["questionId"])){
            $QuestionID = $_POST["questionId"];

            if(($_SESSION["user"] ?? NULL) == $DBlib->fetchDataWithCondition("form", "user_id", "id = :id", [
                ":id" => $DBlib->fetchDataWithCondition("question", "form_id", "id = :id", [":id" => $QuestionID])[0]["form_id"],
            ])[0]["user_id"]){
                $arr = ["question_id" => $QuestionID, "name" => ""];
    
                $id = $DBlib->insertData("answer",$arr);
        
                echo $id;
            }else{
                echo 0;
            }
        }else{
            echo 0;
        }
    }else{
        echo 0;
    }

?>