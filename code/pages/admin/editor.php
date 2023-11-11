<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inteliform - Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/lib/css/pretty-checkbox/dist/pretty-checkbox.min.css">
    <link rel="stylesheet" href="../../assets/global/general.css">
    <link rel="stylesheet" href="css/editor.css">
    <script src="js/editor.js"></script>
</head>
<body>
    <?php 
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
                        <div class="pretty p-icon p-round p-smooth p-bigger anwerBox">
                            <input type="checkbox" />
                            <div class="state p-primary">
                                <i class="icon mdi mdi-check"></i>
                                <label></label>
                            </div>
                        </div>
                        <input type="text" class="answerInput" placeholder="Enter answer">
                    </div>
                    <div class="answer">
                        <div class="pretty p-icon p-round p-smooth p-bigger anwerBox">
                            <input type="checkbox" />
                            <div class="state p-primary">
                                <i class="icon mdi mdi-check"></i>
                                <label></label>
                            </div>
                        </div>
                        <input type="text" class="answerInput" placeholder="Enter answer">
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>