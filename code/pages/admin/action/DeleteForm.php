<?php
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");

    $DBlib = new DatabaseFunctions($db);

    $_POST["id"]=1;
    //Načtení id z POST
    if(isset($_POST["id"])){
        $id = $_POST["id"];
        if(is_numeric($id)){
            //Kontrola jestli je id v DB
            $params = [":id" => $id];
            $countForms=$DBlib->countByPDOWithCondition("form", "id","id=:id", $params );

            if ($countForms!=0) {
                //delete formu
                $DBlib->deleteDataWithCondition("form","id=:id", $params );

                //Kontrola jestli je nastavení tabulky
                $countFormSettings=$DBlib->countByPDOWithCondition("form_settings", "id","form_id=:id", $params );
                //delete nastavení tabulky (pokud je)
                if ($countFormSettings!=0) {
                    $DBlib->deleteDataWithCondition("form_settings","form_id=:id", $params );
                }

                //Kontrola jestli jsou otázky
                $countQuestions=$DBlib->countByPDOWithCondition("question", "id","form_id=:id", $params );
                //zjištění id otázek a následný delete (pokud je)
                if ($countQuestions!=0) {
                    $QuestionIDs = $DBlib->fetchDataWithCondition("question", "id", "form_id = :id", $params);
                    $DBlib->deleteDataWithCondition("question","form_id=:id", $params );

                    //loop na všechny otázky ve formu
                    for ($i=0; $i < count($QuestionIDs); $i++) {
                        //Kontrola jestli jsou média na otázky a následný delete
                        $questionParameter = [":QuestionID" => $QuestionIDs[$i]["id"]];
                        $countQuestionMedia=$DBlib->countByPDOWithCondition("question_media", "id","question_id=:QuestionID", $questionParameter );
                        if ($countQuestionMedia!=0) {
                            $DBlib->deleteDataWithCondition("question_media","question_id=:QuestionID", $questionParameter );
                        }

                        //Kontrola jestli je nastavení na otázky a následný delete
                        $countQuestionSettings=$DBlib->countByPDOWithCondition("question_settings", "id","question_id=:QuestionID", $questionParameter );
                        if ($countQuestionSettings!=0) {
                            $DBlib->deleteDataWithCondition("question_settings","question_id=:QuestionID", $questionParameter );
                        }

                        //Kontrola jestli je odpoved na otázku, zjištění id a následný delete
                        $countQuestionAnswer=$DBlib->countByPDOWithCondition("answer", "id","question_id=:QuestionID", $questionParameter );
                        if ($countQuestionAnswer!=0) {
                            $AnswerID = $DBlib->fetchDataWithCondition("answer", "id", "question_id=:QuestionID", $questionParameter);
                            $DBlib->deleteDataWithCondition("answer","question_id=:QuestionID", $questionParameter );

                            //Kontrola odpovedi guesta a následný delete
                            for ($x=0; $x < count($AnswerID); $x++) { 
                                $AnswerParameter = [":AnswerID" => $AnswerID[$x]["id"]];
                                $countGuestAnswer=$DBlib->countByPDOWithCondition("guest_answer", "id","answer_id=:AnswerID", $AnswerParameter );
                                if ($countGuestAnswer!=0) {
                                    $DBlib->deleteDataWithCondition("guest_answer","answer_id=:AnswerID", $AnswerParameter );
                                }
                            }
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