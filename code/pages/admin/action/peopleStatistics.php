<?php

include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");

$DBlib = new DatabaseFunctions($db);
session_start();

$selectOptionsHtml = "";

if(isset($_GET["id"])){
    if(is_numeric($_GET["id"])){
        if(isset($_SESSION["user"])){
            if($DBlib->countByPDOWithCondition("form", "id","id = :id", [":id" => $_GET["id"]])){
                if(($_SESSION["user"] ?? NULL) == $DBlib->fetchDataWithCondition("form", "user_id", "id = :id", [":id" => $_GET["id"]])[0]["user_id"]){

                    $guests = $DBlib->fetchDataWithCondition("guest", "*", "form_id = :id", [":id" => $_GET["id"]]);

                    foreach($guests as $value){
                        $selectOptionsHtml .= "<option value='".$value["id"]."'>";
                        $selectOptionsHtml .= $value["name"] ." ". $value["surname"] ." - ". $value["email"];
                        $selectOptionsHtml .= "</option>";
                    }
                
                }
            }
        }
    }
}
?>

<div class="selectPeople">
    <select id="selectPeople">
        <?php echo $selectOptionsHtml ?>
    </select>
</div>

<script>
new SlimSelect({
    select: '#selectPeople'
})
</script>