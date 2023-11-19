<?php
    session_start();
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    if(isset($_POST["questionId"])){
        $QuestionID = $_POST["questionId"];

        $arr = ["Id" => $QuestionID ];

        $id = $DBlib->insertData("answer",$arr);

        echo json_encode($id);
    }else{
        echo json_encode(0);
    }

?>