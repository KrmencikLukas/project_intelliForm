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

                    foreach($questions as $key => $value){

                      $questions[$key]["type"] = $DBlib->fetchDataWithCondition("question_type", "number", "id = :id", [":id" => $value["type_id"]])[0]["number"];

                        $selectOptionsHtml .= "<option value='".$value["id"]."'>";
                        $selectOptionsHtml .= $value["heading"];
                        $selectOptionsHtml .= "</option>";

                        $answers = $DBlib->fetchDataWithCondition("answer", "*", "question_id = :id", [":id" => $value["id"]]);

                        foreach($answers as $key => $value){
                            $answers[$key]["guests"] = $DBlib->fetchDataWithCondition("guest_answer", "*", "answer_id = :id", [":id" => $value["id"]]);
                        }

                        $questions[$key]["answers"] = $answers;

                    }

                }
            }
        }
    }
}
?>
<script>
    let questions = <?php echo json_encode($questions) ?>
</script>

<div class="selectQuestion">
    <select id="selectQuestion">
        <?php echo $selectOptionsHtml ?>
    </select>
</div>

<div class="questionInfo">
    
</div>

<canvas id="myChart"></canvas>

<script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
      datasets: [{
        label: '# of Votes',
        data: [12, 19, 3, 5, 2, 3],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
 

<script>
new SlimSelect({
    select: '#selectQuestion',
    events: {
      afterChange: (newVal) => {
        console.log(newVal)
      }
  }
})
</script>