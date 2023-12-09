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

                    $questions = $DBlib->fetchDataWithCondition("question", "*", "form_id = :id", [":id" => $_GET["id"]]);

                    foreach($questions as $key => $value){

                      $questions[$key]["type"] = $DBlib->fetchDataWithCondition("question_type", "number", "id = :id", [":id" => $value["type_id"]])[0]["number"];

                        $selectOptionsHtml .= "<option value='".$value["id"]."'>";
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

generateQuestion(questions[0])

new SlimSelect({
    select: '#selectQuestion',
    events: {
      afterChange: (newVal) => {
        console.log(questions);

        let data = ""
        for(i in questions){
            if(questions[i].id == newVal[0].value){
                data = questions[i]
                break
            }
        }
         
        generateQuestion(data)
      }
    }
})


function generateQuestion(data){

    $("#customStyles").html("")

    if(chart1JS != undefined){
		chart1JS.destroy();
    }
    if(chart2JS != undefined){
		chart2JS.destroy();
    }

    let labels = []
    let guests = []

    let totalGuests = 0;
    data["answers"].forEach(function(element){  
		labels.push(element["name"])
		guestCount = element["guests"].length
		guests.push(guestCount)
		totalGuests += guestCount
    });

    $.ajax({
        type: 'POST',
        url: "action/generateQuestion.php",
        data: {"data": data},
        success: function(html) {
            $(".questionInfo").html(html)

            data["answers"].forEach(function(element){  
                console.log(element["id"])
                $(".peopleCount"+element["id"]).html("<span>"+element["guests"].length+"</span>"+"<i class='mdi mdi-account-multiple'></i>")
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

            }else{

            }
        },
    });
}
</script>