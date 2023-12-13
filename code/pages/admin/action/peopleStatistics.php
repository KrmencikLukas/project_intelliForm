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

                    $questions = $DBlib->fetchDataWithCondition("question", "*", "form_id = :id", [":id" => $_GET["id"]]);

                    $guestsArr = [];
                    $guestsIDs = [];

                    foreach($questions as $key => $value){

                        $answers = $DBlib->fetchDataWithCondition("answer", "*", "question_id = :id", [":id" => $value["id"]]);

                        foreach($answers as $key2 => $value2){
                            $guests = $DBlib->fetchDataWithCondition("guest_answer", "*", "answer_id = :id", [":id" => $value2["id"]]);

                            foreach($guests as $key3 => $value3){
                                if(!in_array($value3["guest_id"], $guestsIDs)){
                                    $guestsIDs[] = $value3["guest_id"];
                                    $guestsArr[] = $DBlib->fetchDataWithCondition("guest", "*", "id = :id", [":id" => $value3["guest_id"]])[0];
                                } 
                                
                            }

                            $answers[$key2]["guests"] = $guests;
                        }

                        $questions[$key]["answers"] = $answers;
                    }

                    foreach($guestsArr as $value){

                        $selected = "";

                        if($value["id"] == $_GET["guest"]){
                            $currentGuest = $_GET["guest"];
                            $selected = "selected";
                        }

                        $selectOptionsHtml .= "<option value='".$value["id"]."' ".$selected.">";

                        if($value["name"] == NULL && $value["surname"] == NULL){
                            $gap = "";
                        }else{
                            $gap = " - ";
                        }

                        $selectOptionsHtml .= $value["name"] ." ". $value["surname"] .$gap. $value["email"];
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
<div class="questionInfo"></div>

<script>
questions = <?php echo json_encode($questions) ?? NULL ?>

generateQuestion(questions, <?php echo $currentGuest ?? $guestsArr[0]["id"] ?>)

new SlimSelect({
    select: '#selectPeople',
    events: {
      afterChange: (newVal) => {

        guestID = newVal[0]["value"]
        generateQuestion(questions,guestID)
        appendParamsToUrl({"guest": guestID})
      }
    }
})

function generateQuestion(questions,guest){

    $("#customStyles").html("")
    $(".questionInfo").html("")
    questions.forEach(function(element){  
        $.ajax({
        type: 'POST',
        url: "action/generateQuestion.php",
        data: {"data": element, "guest": guest},
        success: function(html) {
            $(".questionInfo").append(html)
        }
        })
    })
}

</script>