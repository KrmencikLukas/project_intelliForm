<?php 
    session_start();
    include("../lib/php/db.php");
    include("../lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);


    if(isset($_SESSION["user"])){
        if(isset($_POST["data"])){
            $data = json_decode($_POST["data"],true);
            $name = $data["name"];
            $name = isNameSet($name,$name,0);
    
            $insertArr=[
                "name" => $name,
                "user_id" => $_SESSION["user"],
            ];
            
            $id = $DBlib->insertData("form", $insertArr);

            echo $id;

            foreach($data["settings"] as $value){
                $DBlib->insertData("form_settings", ["key" => $value["key"], "value" => $value["value"], "form_id" => $id]);
            }

            foreach($data["questions"] as $value){
                $qid = $DBlib->insertData("question", ["heading" => $value["heading"], "description" => $value["description"], 
                "type_id" => $DBlib->fetchDataWithCondition("question_type", "id", "number = :number", [":number" => $value["type"]["number"]])[0]["id"], "form_id" => $id]);
                foreach($value["settings"] as $value1){
                    $DBlib->insertData("question_settings", ["question_id" => $qid, "key" => $value1["key"], "value" => $value1["value"]]);
                }
                foreach($value["answers"] as $value1){
                    $DBlib->insertData("answer", ["question_id" => $qid, "name" => $value1["name"], "correctness" => $value1["correctness"]]);
                }
            }
        }
        
    }else{
        header("Location: ../../error.php");
    }

    //rekurzivni funkce co pridava za nazev cislo
    function isNameSet($firstName,$name,$number){
        global $DBlib;
        $count = $DBlib->countByPDOWithCondition("form","*","name = :name AND user_id = :user_id", [":name" => $name,":user_id" => $_SESSION["user"]]);

        if($count > 0){
            $number++;
            return isNameSet($firstName,$firstName." (".$number.")",$number);
        }else{
            return $name;
        }
    }
?>