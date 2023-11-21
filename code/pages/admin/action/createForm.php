<?php 
    session_start();
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);


    if(isset($_SESSION["user"])){
        
        $name = "New form";
        $name = isNameSet($name,$name,0);

        $insertArr=[
            "name" => $name,
            "user_id" => $_SESSION["user"],
        ];
        
        $id = $DBlib->insertData("form", $insertArr);

        //$id = $DBlib->insertData("form_settings", ["key" => ]);

        header("Location: ../editor.php?id=".$id);

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