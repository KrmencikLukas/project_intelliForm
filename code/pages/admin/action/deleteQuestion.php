<?php
    
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $DBlib = new DatabaseFunctions($db);

    $_POST["id"] = 3;

    //Načtení id z POST
    if(isset($_POST["id"])){
        $id = $_POST["id"];
        if(is_numeric($id)){
            //Kontrola jestli je id v DB
            $params = [":id" => $id];
            $countQuestions=$DBlib->countByPDOWithCondition("question", "id","id=:id", $params );

            if ($countQuestions!=0) {
                //delete otázky
                $DBlib->deleteDataWithCondition("question","id=:id", $params );

                //Kontrola jestli jsou média na otázku a následný delete
                $countQuestionMedia=$DBlib->countByPDOWithCondition("question_media", "id","question_id=:id", $params );
                if ($countQuestionMedia!=0) {
                    $DBlib->deleteDataWithCondition("question_media","question_id=:id", $params );
                }

                //Kontrola jestli je nastavení na otázku a následný delete
                $countQuestionSettings=$DBlib->countByPDOWithCondition("question_settings", "id","question_id=:id", $params );
                if ($countQuestionSettings!=0) {
                    $DBlib->deleteDataWithCondition("question_settings","question_id=:id", $params );
                }

                //Kontrola jestli je odpoved na otázku, zjištění id a následný delete
                $countQuestionAnswer=$DBlib->countByPDOWithCondition("answer", "id","question_id=:id", $params );
                if ($countQuestionAnswer!=0) {
                    $AnswerID = $DBlib->fetchDataWithCondition("answer", "id", "question_id=:id", $params);
                    $DBlib->deleteDataWithCondition("answer","question_id=:id", $params );

                    //Kontrola odpovedi guesta a následný delete
                    for ($x=0; $x < count($AnswerID); $x++) { 
                        $AnswerParameter = [":AnswerID" => $AnswerID[$x]["id"]];
                        $countGuestAnswer=$DBlib->countByPDOWithCondition("guest_answer", "id","answer_id=:AnswerID", $AnswerParameter );
                        if ($countGuestAnswer!=0) {
                            $DBlib->deleteDataWithCondition("guest_answer","answer_id=:AnswerID", $AnswerParameter );
                        }
                    }
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