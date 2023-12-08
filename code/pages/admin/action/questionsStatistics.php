<?php

session_start();

include("../../../assets/lib/php/db.php");
include("../../../assets/lib/php/DBlibrary.php");

$DBlib = new DatabaseFunctions($db);



//funkce od simona


//funkce na css formu
function SetFormCSS($formCSSkey, $formCSSvalue){
  $returnCSS="";
  for ($i=0; $i < count($formCSSkey); $i++) { 
      if ($formCSSkey[$i]["key"]=="color") {
          $returnCSS=$returnCSS.".form {background-color:".$formCSSvalue[$i]["value"]."}";
      }
      if ($formCSSkey[$i]["key"]=="background color") {
          $returnCSS=$returnCSS."body {background-color:".$formCSSvalue[$i]["value"]."}";
      }
      if ($formCSSkey[$i]["key"]=="font") {
          $returnCSS=$returnCSS.".formHeading, p, .questionHeading, label {font-family:".$formCSSvalue[$i]["value"]."}";
      }
  }
  return $returnCSS;
}

//funkce na vypisování formu
function WriteQuestion ($question){
  global $DBlib;
  $echoForm = "";
  $answerIDs="";
  $answers="";
  $questionID = [ "id" => $question["id"]];

  $answerIDs=$DBlib->fetchDataWithCondition("answer", "id", "question_id = :id", $questionID);
  $answers=$DBlib->fetchDataWithCondition("answer", "*", "question_id = :id", $questionID);
  
  //vypisuje divy na otázky (bez odpovědí) a popis otázky (pokud je)
  if (($question["type_id"]==1)||($question["type_id"]==5)) {
      $echoForm=$echoForm.'<div class="question type0 q'.$question["id"].'">';
  } else {
      $echoForm=$echoForm.'<div class="question q'.$question["id"].'">';
  }

  $questionSettings=$DBlib->fetchDataWithCondition("question_settings", "*", "question_id = :id", $questionID);

  if (isset($questionSettings)) {
      foreach ($questionSettings as $key => $value) {
          if (($value["question_id"]==$question["id"])&&($value["key"]=="Mandatory")&&($value["value"]=="1")) {
              $echoForm=$echoForm.'<div class="mandatory"><p>*Mandatory</p></div>';
          }
      }
  }

  $echoForm=$echoForm.'<h2 class="questionHeading">'.$question["heading"].'</h2><div class="descriptionContainer"><p class="description">'.nl2br(str_replace(" ","&nbsp;",$question["description"])).'</p></div><div class="answers">';
  
  //vypisuje odpovedi podle typu otázky a hodnot v db
  for ($x=0; $x < count($answerIDs); $x++) { 
      if (($question["type_id"]==1)||($question["type_id"]==5)||($question["type_id"]==4)) {
          if (($question["type_id"]==1)||($question["type_id"]==5)) {
              if ($x==0) {
                  $echoForm=$echoForm.'<div class="answer yes"><div class="pretty p-toggle p-plain"><input type="radio" name="'.$answers[$x]["id"].'" checked=""><div class="state p-off"><label>Yes</label></div><div class="state p-on"><label class="color">Yes</label></div></div></div>';
              } else {
                  $echoForm=$echoForm.'<div class="answer no"><div class="pretty p-toggle p-plain"><input type="radio" name="'.$answers[$x]["id"].'"><div class="state p-off"><label>No</label></div><div class="state p-on"><label class="color">No</label></div></div></div>';
              }
          } elseif ($question["type_id"]==4) {
              $echoForm=$echoForm.'<div class="answer typeA2"><div class="pretty p-icon p-round p-smooth p-bigger p-toggle up answerBox"><input type="checkbox" name="'.$answers[$x]["id"].'"><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-up"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-up"></i><label></label></div></div><div class="pretty p-icon p-round p-smooth p-bigger p-toggle down answerBox"><input type="checkbox"><div class="state p-on p-success-o"><i class="icon mdi mdi-arrow-down"></i><label></label></div><div class="state p-off"><i class="icon mdi mdi-arrow-down"></i><label></label></div></div><p class="answerDesc">'.$answers[$x]["name"].'</p></div>';
          }
          
      } else {
          $echoForm=$echoForm.'<div class="answer"><div class="pretty p-icon p-round p-smooth p-bigger answerBox"><input type="checkbox" name="'.$answers[$x]["id"].'"><div class="state p-primary"><i class="icon mdi mdi-check"></i><label></label></div></div>';
          $echoForm=$echoForm.'<p class="answerDesc">'.$answers[$x]["name"].'</p></div>';
      }
  }
  $echoForm=$echoForm.'</div></div>';

  return $echoForm;
}

//funkce css otázek
function SetQuestionCSS ($question){
  global $DBlib;
  $returnCSS="";
  $questionID = [ "id" => $question["id"]];
  $questionCSS=$DBlib->fetchDataWithCondition("question_settings", "*", "question_id = :id", $questionID);

  if (isset($questionCSS)) {
      foreach ($questionCSS as $key => $value) {
          if (($value["question_id"]==$question["id"])&&($value["key"]=="Background color")) {
              $returnCSS=$returnCSS.'.q'.$question["id"].' {background-color:'.$value["value"].'}';
          }
          if (($value["question_id"]==$question["id"])&&($value["key"]=="Text color")) {
              $returnCSS=$returnCSS.'.q'.$question["id"].', .q'.$question["id"].' div div div div label {color:'.$value["value"].'}';
          }
      }
      }
  return $returnCSS;
}


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