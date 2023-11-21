<?php
    session_start();
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    if(isset($_POST["questionId"])){
        if(is_numeric($_POST["questionId"])){
            $QuestionID = $_POST["questionId"];

            $arr = ["question_id" => $QuestionID, "name" => ""];
    
            $id = $DBlib->insertData("answer",$arr);
    
            echo $id;
        }else{
            echo 0;
        }
    }else{
        echo 0;
    }

?>