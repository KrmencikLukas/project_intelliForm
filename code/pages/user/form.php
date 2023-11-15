<?php
    include("../../assets/lib/php/db.php");
    include("../../assets/lib/php/DBlibrary.php");
    $DBlib = new DatabaseFunctions($db);

    //http://project.lukaskrmencik.cz/S/code/pages/user/form.php?id=14&guestId=1&code=suprkod


    $id=$_GET["id"] ?? null;
    if ((!empty($id))&&(is_numeric($id))) {
        $formID = [ "id" => $id];
        $public=$DBlib->fetchDataWithCondition("form", "public", "id = :id", $formID);
        
        if (!empty($public)) {
            if ($public[0]["public"]==1) {
                $FormName=$DBlib->fetchDataWithCondition("form", "name", "id = :id", $formID);
            } else {
                if ((!empty($_GET["guestId"]))&&(!empty($_GET["code"]))&&(is_numeric($_GET["guestId"]))) {
                    
                    $guest=$_GET["guestId"];
                    $code=$_GET["code"];

                    $guestCodeID = [ 
                        "id" => $guest,
                        "code" => $code,
                    ];
                    $guestEmail=$DBlib->fetchDataWithCondition("guest", "email", "id = :id AND code = :code", $guestCodeID);
                    
                    if (!empty($guestEmail)) {
                        $FormName=$DBlib->fetchDataWithCondition("form", "name", "id = :id", $formID);
                    } else {
                        header("Location: ../error.php");
                    }
                } else {
                    header("Location: ../error.php");
                }
            }
            
        } else {
            header("Location: ../error.php");
        }
    } else {
        header("Location: ../error.php");
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$FormName[0]["name"]?> | Form</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/lib/css/pretty-checkbox/dist/pretty-checkbox.min.css">
    <link rel="stylesheet" href="../assets/global/general.css">
    <link rel="stylesheet" href="css/form.css">
</head>
<body>
    <div id="content">
        <div class="form">
            <div class="question">
                <input type="text" class="questionHeading" placeholder="Enter question">
                <div class="descriptionContainer">
                    <textarea class="description" rows="1" placeholder="Enter description" oninput="autoGrow(this)"></textarea>
                </div>
                <div class="answers">
                    <div class="answer">
                        <div class="pretty p-icon p-round p-smooth p-bigger p-locked answerBox">
                            <input type="checkbox" />
                            <div class="state p-primary">
                                <i class="icon mdi mdi-check"></i>
                                <label></label>
                            </div>
                        </div>
                        <input type="text" class="answerInput" placeholder="Enter answer">
                        <i class="mdi mdi-close delete"></i>
                    </div>

                    <div class="answer">
                        <div class="pretty p-icon p-round p-smooth p-bigger p-locked answerBox">
                            <input type="checkbox" />
                            <div class="state p-primary">
                                <i class="icon mdi mdi-check"></i>
                                <label></label>
                            </div>
                        </div>
                        <input type="text" class="answerInput" placeholder="Enter answer">
                        <i class="mdi mdi-close delete"></i>
                    </div>
                </div>
            </div>
            <div class="question type0">
                <input type="text" class="questionHeading" placeholder="Enter question">
                <div class="descriptionContainer">
                    <textarea class="description" rows="1" placeholder="Enter description" oninput="autoGrow(this)"></textarea>
                </div>
                <div class="answers">
                    <div class="answer yes">
                        <input type="checkbox" class="dis">
                        <p class="answerInput">Yes</p>
                    </div>

                    <div class="answer no">
                        <input type="checkbox" class="dis">
                        <p class="answerInput">No</p>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</body>
</html>