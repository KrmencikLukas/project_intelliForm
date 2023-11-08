<?php
    
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $DBlib = new DatabaseFunctions($db);

    //Načtení id z POST
    if(isset($_POST["id"])){
        $id = $_POST["id"];
        if(is_numeric($id)){
            //Kontrola jestli je id v DB
            $params = [":id" => $id];
            $countAnswer=$DBlib->countByPDOWithCondition("answer", "id","id=:id", $params );
            if ($countAnswer!=0) {
                $DBlib->deleteDataWithCondition("answer","id=:id", $params );

                //Kontrola odpovedi guesta a následný delete
                $AnswerParameter = [":AnswerID" => $id];
                $countGuestAnswer=$DBlib->countByPDOWithCondition("guest_answer", "id","answer_id=:AnswerID", $AnswerParameter );
                if ($countGuestAnswer!=0) {
                    $DBlib->deleteDataWithCondition("guest_answer","answer_id=:AnswerID", $AnswerParameter );
                }

                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 0;
        }
    }else{
        echo 0;
    }
?>