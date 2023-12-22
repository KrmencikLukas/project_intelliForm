<?php

//kontroluje validnost formu - jsetli odpoveděl na všechny povnné otázky a odpoveděl správným počtem odpovědí u daných otázek

function validateForm ($id, $formData, $DBlib) {

    //zjistí všechny otázky které jsou ve formu
    $formID = [":id" => $id];
    $formQuestions= $DBlib->fetchDataWithCondition("question", "id",'form_id = :id', $formID);
    $mandatoryCount= 0;
    $sentCount = [];
    $MinMaxCount = [];

    //zjistí kolik otázek je povinných
    for ($i=0; $i < count($formQuestions); $i++) { 
        $questions = [":id" => $formQuestions[$i]["id"]];
        $questionMandatory= $DBlib->countByPDOWithCondition("question_settings", "id",'question_id = :id AND `key` = "Mandatory" AND `value` = 1', $questions);
        if ($questionMandatory==1) {
            $mandatoryCount= $mandatoryCount+1;
        }
    }

    // tady se kontrolují odevzdané otázky
    //formData je post z formu, key id formuvé otázky např. "*q102*a506*t4", value je hodnota odpovědi
    foreach ($formData as $key => $value) {
        //email a submit je část postu, tedy to nepočítám
        if (($key!="submit")&&($key!="email")) {
            //název dám do arraye, v názvu se totiž ukrývá id otázky, id odpovědi, typ odpovědi
            $answerData = explode("*",$key);
            if ((isset($answerData[0]))&&(isset($answerData[1]))&&(isset($answerData[2]))&&(isset($answerData[3]))) {
                $questionID = strpos($answerData[1], "",1);
                $questionID = substr($answerData[1], $questionID);
                $params = [":id" => $questionID];
                $questionInDB= $DBlib->countByPDOWithCondition("question", "id","id = :id", $params );
                $questionMandatory= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Mandatory"', $params);

                if ($questionInDB==1) {
                    $answerID = strpos($answerData[2], "",1);
                    $answerID = substr($answerData[2], $answerID);
                    $params = [":id" => $answerID];
                    $answerInDB= $DBlib->countByPDOWithCondition("answer", "id","id = :id", $params );
                    //tady kontroluji jestli je otázka v db, jestli je na povinnou otázku, nakonec zapíšu do arraye, že byla zodpovězena, kontroluji tedy ještě jestli náhodnou v arrayi není
                    if (($answerInDB==1)&&(!in_array($questionID,$sentCount))&&(isset($questionMandatory[0]["value"]))&&($questionMandatory[0]["value"]==1)) {
                        $sentCount[]=$questionID;
                    } else {
                        //když otázka není povinná, může ještě mít nasatvený striktní počet odpovědí, musím to tedy zkostrolovat
                        $params = [":id" => $questionID];
                        $minInDB= $DBlib->countByPDOWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min votes"', $params );
                        $maxInDB= $DBlib->countByPDOWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max votes"', $params );
                        $minUInDB= $DBlib->countByPDOWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min upvotes"', $params );
                        $maxUInDB= $DBlib->countByPDOWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max upvotes"', $params );
                        //když otázka má nastavený striktní počet odpovědí, zapíšu si ji pro práci dále
                        if (($minInDB>0)||($maxInDB>0)||($minUInDB>0)||($maxUInDB>0)) {
                            $MinMaxCount[$answerID] = $questionID;
                        }
                    }
                }
            } elseif ((isset($answerData[0]))&&(isset($answerData[1]))&&(isset($answerData[2]))) {
                //toto je pro ano/ne otázky
                $questionID = strpos($answerData[1], "",1);
                $questionID = substr($answerData[1], $questionID);
                $params = [":id" => $questionID];
                $questionInDB= $DBlib->countByPDOWithCondition("question", "id","id = :id", $params );
                $questionMandatory= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Mandatory"', $params);
                if (($questionInDB==1)&&(!in_array($questionID,$sentCount))&&(isset($questionMandatory[0]["value"]))&&($questionMandatory[0]["value"]==1)) {
                    $sentCount[]=$questionID;
                }
            }
        }
    }
    
    //když se zodpovědělo na všechny povinné otázky
    if (count($sentCount)==$mandatoryCount) {
        
        //kontrola strktního počtu odpovědí
        foreach ($MinMaxCount as $answer => $question) {
            //typ 4 je upvote/downvote, který má 2 možné hodnoty odpovědi, navíc jich může být víc v jedné otázce 
            if (isset($formData["*q".$question."*a".$answer."*t4"])) {
                
                if (!isset($downvote)) {
                    $downvote[$question]=0;
                }
                if (!isset($upvote)) {
                    $upvote[$question]=0;
                }
                
                //pokud má odpověd hodnotu 1, je to upvoute, pokud ne, je to downvote (oba mají svůj vlastní maximální/minimální počet odpovědí)
                if ($formData["*q".$question."*a".$answer."*t4"]==1) {
                    $upvote[$question]=$upvote[$question]+1;
                } elseif ($formData["*q".$question."*a".$answer."*t4"]==0) {
                    $downvote[$question]=$downvote[$question]+1;
                }

            } else {
                //pokud je to cokoliv jiného, má to jenom jednu hodnotu a tedy ji nerozeznávám
                if (!isset($count[$question])) {
                    $count[$question]=0;
                }
                $count[$question]=$count[$question]+1;
            }
        }
        $MinMaxDBCount=0;
        $FinalCount=0;
        for ($i=0; $i < count($formQuestions); $i++) { 

            //na každou otázku musím zjistit jejich předpokládané počty odpovědí
            $questions = [":id" => $formQuestions[$i]["id"]];
            $questionMinVote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min votes"', $questions);
            $questionMaxVote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max votes"', $questions);
            $questionMinUpvote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min upvotes"', $questions);
            $questionMaxUpvote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max upvotes"', $questions);
            $questionMinDownvote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Min downvotes"', $questions);
            $questionMaxDownvote= $DBlib->fetchDataWithCondition("question_settings", "`value`",'question_id = :id AND `key` = "Max downvotes"', $questions);
            
            if ((!empty($questionMinVote))&&(!empty($questionMaxVote))) {
                
                //když jsou zadané minvote a maxvote, jde o klasicou otázku a kontroluji, jestli má odpovedí více jak minimum a méně jak maximum
                $MinMaxDBCount=$MinMaxDBCount+1;
                if ((isset($count[$formQuestions[$i]["id"]]))&&($questionMinVote[0]["value"]<=$count[$formQuestions[$i]["id"]])&&($questionMaxVote[0]["value"]>=$count[$formQuestions[$i]["id"]])) {
                    $FinalCount=$FinalCount+1;
                } 
            } elseif ((!empty($questionMinUpvote))&&(!empty($questionMaxUpvote))) {
                
                //v tomto případě jde o upvote downvote otázku - musím kotrolovat jestli je upvote mezi mimimem a maximem, to stejný o downvotu
                $MinMaxDBCount=$MinMaxDBCount+1;
                if ((isset($upvote[$formQuestions[$i]["id"]]))&&(isset($downvote[$formQuestions[$i]["id"]]))&&($questionMinUpvote[0]["value"]<=$upvote[$formQuestions[$i]["id"]])&&($questionMaxUpvote[0]["value"]>=$upvote[$formQuestions[$i]["id"]])&&($questionMinDownvote[0]["value"]<=$downvote[$formQuestions[$i]["id"]])&&($questionMaxDownvote[0]["value"]>=$downvote[$formQuestions[$i]["id"]])) {
                    $FinalCount=$FinalCount+1;
                }
            }

        }
        
        //nakonec kontroluji jestli správně odpověděl na všechny otázky, do return dám 1 pokud dobře, jinak vypíšu co bylo špatně
        if ($MinMaxDBCount==$FinalCount) {
            return 1;
        } else {
            return "minmax";
        }
    } else {
        return "mandatory";
    }

}

?>