<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inteliform - Editor</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/lib/css/pretty-checkbox/dist/pretty-checkbox.min.css">
    <link rel="stylesheet" href="../../assets/global/general.css">
    <link rel="stylesheet" href="css/editor.css">
    <script src="js/editor.js"></script>
</head>
<body>
    <?php 
        $PageSpecific = "<input type='text' class='formName' id='formName' placeholder='Enter form name'>";
        $location = "Editor";
        include("../../assets/global/headerSidebar.php") 
    ?>
    <span class="mdi mdi-account-file"></span>
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
                    <div class="answer yes yesNoDiv3">
                        <input type="checkbox" class="dis yesNoCheckbox3" oninput="divCheckbox(3,'yes')">
                        <p class="answerInput">Yes</p>
                    </div>

                    <div class="answer no yesNoDiv3">
                        <input type="checkbox" class="dis yesNoCheckbox3" oninput="divCheckbox(3,'no')">
                        <p class="answerInput">No</p>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</body>
</html>