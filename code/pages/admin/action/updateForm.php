<?php
    session_start();
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    //$_SESSION["user"] = 1;


    if(isset($_POST["data"])){
        $json = $_POST["data"];
        /*
        $json = '{"id":8,"name":"New form","user":1,"settings":{"3":{"key":"color","value":"red"},"4":{"key":"bg","value":"black"}},"questions":{"3":{"heading":"Mas rad Babise?","description":"STBaka","type":{"id":1,"number":0,"name":"Yes\/No poll","description":"Poll where the user can only answer yes, no or abstain."},"media":{"1":"\/mujobrazek.png"},"settings":{"2":{"key":"color","value":"blue"}},"answers":{"2":{"name":"ne je to curak","correctness":"0"}}}}}';
        */
    
        $json = json_decode($json, true);
    
        //var_dump($json);
    
        if(isset($_SESSION["user"])){
            if($json["user"] == $_SESSION["user"]){
                //print_r($json);
    
                $DBlib->updateData("form",["name" => $json["name"],"id" => $json["id"]],"id = :id");
    
                foreach($json["settings"] as $key => $value){
                    $DBlib->updateData("form_settings",["value" => $value["value"],"id" => $key],"id = :id");
                }
    
                foreach($json["questions"] as $key => $value){
                    $DBlib->updateData("question",["heading" => $value["heading"],"description" => $value["description"],"id" => $key],"id = :id");
    
                    foreach($value["settings"] as $key2 => $value2){
                        var_dump($value["settings"]);
                        $DBlib->updateData("question_settings",["value" => $value2["value"],"id" => $key2],"id = :id");
                    }
    
                    foreach($value["answers"] as $key2 => $value2){
                        if(isset($value2["correctness"])){
                            $DBlib->updateData("answer",["name" => $value2["name"],"correctness" => $value2["correctness"],"id" => $key2],"id = :id");
                        }else{
                            $DBlib->updateData("answer",["name" => $value2["name"],"id" => $key2],"id = :id");
                        }

                    }
                }
                echo 1;
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