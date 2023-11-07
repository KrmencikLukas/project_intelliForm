<?php
    session_start();
    include("../../../assets/lib/php/db.php");
    include("../../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    $_SESSION["user"] = 1;

    //$json = $_POST["data"]
    $json = '{"id":1,"name":"New form","user":1,"settings":{"1":{"key":"color","value":"red"}},"questions":{"1":{"heading":"Mas rad Babise?","description":"STBaka","type":{"number":0,"name":"Yes\/No poll","description":"Poll where the user can only answer yes, no or abstain."},"media":{"1":"\/mujobrazek.png"},"settings":{"1":{"key":"color","value":"blue"}},"answers":{"1":{"name":"ne je to curak","correctness":"1"}}}}}';

    $json = json_decode($json, true);

    //var_dump($json);

    if(isset($_SESSION["user"])){
        if($json["user"] == $_SESSION["user"]){
            print_r($json);
            
        }else{
            echo 0;
        }
    }else{
        echo 0;
    }
?>