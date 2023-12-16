<?php

session_start();

include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");

$DBlib = new DatabaseFunctions($db);

$selectOptionsHtml = "";

if(isset($_GET["id"])){
    if(is_numeric($_GET["id"])){
        if(isset($_SESSION["user"])){
            if($DBlib->countByPDOWithCondition("form", "id","id = :id", [":id" => $_GET["id"]])){
                if(($_SESSION["user"] ?? NULL) == $DBlib->fetchDataWithCondition("form", "user_id", "id = :id", [":id" => $_GET["id"]])[0]["user_id"]){

                    $guestsDB = $DBlib->fetchDataWithCondition("guest", "*", "form_id = :id", [":id" => $_GET["id"]]);
                    $guests = [];

                    foreach($guestsDB as $key => $value){
                        $guests[$value["id"]] = [
                            "name" => $value["name"],
                            "surname" => $value["surname"],
                            "email" => $value["email"],
                        ];
                    }

                    $questions = $DBlib->fetchDataWithCondition("question", "*", "form_id = :id", [":id" => $_GET["id"]]);

                    $questionsHTML = "";

                    foreach($questions as $key => $value){

                        $selected = "";

                        $questions[$key]["type"] = $DBlib->fetchDataWithCondition("question_type", "number", "id = :id", [":id" => $value["type_id"]])[0]["number"];


                        $answers = $DBlib->fetchDataWithCondition("answer", "*", "question_id = :id", [":id" => $value["id"]]);

                        foreach($answers as $key2 => $value2){
                            $answers[$key2]["guests"] = $DBlib->fetchDataWithCondition("guest_answer", "*", "answer_id = :id", [":id" => $value2["id"]]);
                        }

                        $questions[$key]["answers"] = $answers;

                        $questionsHTML .= '<div class="questionDiv"><div class="questionInfo questionInfo'.$key.'"></div><div class="chartX"><canvas id="chart'.$key.'"></canvas></div></div>';
                    }

                }
            }
        }
    }
}

echo $questionsHTML;
?>

<script>

questions = <?php echo json_encode($questions) ?>

guestsArr = <?php echo json_encode($guests) ?>

for(i in questions){
    generateQuestion(questions[i],i)
}

function generateQuestion(data,index){

    console.log(data)

    $("#customStyles").html(".peopleCount{display: flex}")

    if(chart1JS != undefined){
		chart1JS.destroy();
    }
    if(chart2JS != undefined){
		chart2JS.destroy();
    }

    let labels = []
    let guests = []
    let guestsPos = []
    let guestsNeg = []
    questionCtxArr = []
    chartArrJS = []

    let totalGuests = 0;
    data["answers"].forEach(function(element){  
        labels.push(element["name"])
        if(data["type"] == 2){
            guestPosCount = 0
            guestNegCount = 0
            element["guests"].forEach(function(guest){
                if(guest["value"] == 1){
                    guestPosCount++
                }else{
                    guestNegCount++
                }
            })
            guestsPos.push(guestPosCount)
            guestsNeg.push(guestNegCount)
            guests.push(((guestPosCount-guestNegCount) < 0) ? 0 : guestPosCount-guestNegCount)
            totalGuests += guestPosCount + guestNegCount;
            totalGuests += guestNegCount

        }else{
            guestCount = element["guests"].length
            guests.push(guestCount)
            totalGuests += guestCount
        }
    });

    $.ajax({
        type: 'POST',
        url: "action/generateQuestion.php",
        data: {"data": data},
        success: function(html) {
            $(".questionInfo"+index).html(html)

            let i = 0
            data["answers"].forEach(function(element){  
                if(data["type"] == 2){

                    guestListPos = ""
                    guestListNeg = ""
                    element["guests"].forEach(function(guestID){
                        guest = guestsArr[guestID["guest_id"]]
                        if(guest != undefined){
                            if(guest["email"] != null && guest["email"] != ""){
                                if(guest["name"] == null){
                                    guest["name"] = ""
                                }
                                if(guest["surname"] == null){
                                    guest["surname"] = ""
                                }
                                let addList = "<div class='guest'>"+guest["name"]+" "+guest["surname"]+" - "+guest["email"]+"</div>"
                                if(guestID["value"] == 1){
                                    guestListPos = addList;
                                }else{
                                    guestListNeg = addList;
                                }  
                            }         
                        }
                    })

                    $(".peopleCount"+element["id"]).html("<div class='up'><span>"+guestsPos[i]+"</span>"+"<i class='mdi mdi-arrow-up'></i><div class='guestList'>"+guestListPos+"</div></div>"+"<div class='down'><span>"+guestsNeg[i]+"</span>"+"<i class='mdi mdi-arrow-down'></i><div class='guestList'>"+guestListNeg+"</div></div>")
                }else{
                    guestList = ""
                    element["guests"].forEach(function(guestID){
                        guest = guestsArr[guestID["guest_id"]]
                        if(guest != undefined){
                            if(guest["email"] != null && guest["email"] != ""){
                                if(guest["name"] == null){
                                    guest["name"] = ""
                                }
                                if(guest["surname"] == null){
                                    guest["surname"] = ""
                                }
                                guestList +=  "<div class='guest'>"+guest["name"]+" "+guest["surname"]+" - "+guest["email"]+"</div>"
                            }         
                        }

                    })

                    $(".peopleCount"+element["id"]).html("<span>"+element["guests"].length+"</span>"+"<i class='mdi mdi-account-multiple'></i><div class='guestList'>"+guestList+"</div>")
                }
                i++
            });

            if(totalGuests > 0){
				questionCtx1 = document.getElementById('chart'+index)

                console.log(id)

				new Chart(questionCtx1, {
					type: 'pie',
					data: {
					labels: labels,
					datasets: [
						{
						label: 'Votes',
						data: guests,
						},
					]
					},
					options: {
					responsive: true,
					plugins: {
						legend: {
						position: 'top',
						},
					}
					},
				})
            }
        },
    });
}
</script>