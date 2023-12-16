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

                    foreach($questions as $key => $value){

                        $selected = "";

                        if($value["id"] == $_GET["question"]){
                            $currentQuestion = $key;
                            $selected = "selected";
                        }

                        $questions[$key]["type"] = $DBlib->fetchDataWithCondition("question_type", "number", "id = :id", [":id" => $value["type_id"]])[0]["number"];

                        
                        $selectOptionsHtml .= "<option value='".$value["id"]."' ".$selected.">";
                        $selectOptionsHtml .= $value["heading"];
                        $selectOptionsHtml .= "</option>";

                        $answers = $DBlib->fetchDataWithCondition("answer", "*", "question_id = :id", [":id" => $value["id"]]);

                        foreach($answers as $key2 => $value2){
                            $answers[$key2]["guests"] = $DBlib->fetchDataWithCondition("guest_answer", "*", "answer_id = :id", [":id" => $value2["id"]]);
                        }

                        $questions[$key]["answers"] = $answers;
                    }

                }
            }
        }
    }
}
?>

<div class="selectQuestion">
    <select id="selectQuestion">
        <?php echo $selectOptionsHtml ?>
    </select>
</div>

<div class="questionInfo">
    
</div>

<div class="charts">
	<div class="chart1">
		<canvas id="chart1"></canvas>
	</div>

	<div class="chart2">
		<canvas id="chart2"></canvas>
	</div>
</div>


<script>

questions = <?php echo json_encode($questions) ?>

guestsArr = <?php echo json_encode($guests) ?>

currentQuestion = <?php  echo $currentQuestion ?? 0 ?>

console.log(currentQuestion)

qSelect = new SlimSelect({
    select: '#selectQuestion',
    events: {
    afterChange: (newVal) => {

        let data = ""
        for(i in questions){
            if(questions[i].id == newVal[0].value){
                data = questions[i]
                break
            }
        }
         
        appendParamsToUrl({"question": data.id})
        generateQuestion(data)
      }
    }
})

console.log(qSelect);

generateQuestion(questions[currentQuestion])


function generateQuestion(data){

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
            $(".questionInfo").html(html)

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
				questionCtx1 = document.getElementById('chart1');

				chart1JS = new Chart(questionCtx1, {
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
				});

                questionCtx2 = document.getElementById('chart2');

                if(data["type"] == 2){
                    chart2JS = new Chart(questionCtx2, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Upvotes',
                                data: guestsPos,
                            },
                            {
                                label: 'Downvotes',
                                data: guestsNeg,
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
				    });
                }else{
                    chart2JS = new Chart(questionCtx2, {
                    type: 'bar',
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
				    });
                }

            }
        },
    });
}
</script>