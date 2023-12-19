<?php

include("../../assets/lib/php/db.php");
include("../../assets/lib/php/DBlibrary.php");

$DBlib = new DatabaseFunctions($db);
session_start();


$user = $_SESSION['user'] ?? null;
if(isset($_GET["id"])){
    if(is_numeric($_GET["id"])){
        if(isset($_SESSION["user"])){
            if($DBlib->countByPDOWithCondition("form", "id","id = :id", [":id" => $_GET["id"]])){
                if(($_SESSION["user"] ?? NULL) == $DBlib->fetchDataWithCondition("form", "user_id", "id = :id", [":id" => $_GET["id"]])[0]["user_id"]){
                    $questionTypesRaw = $DBlib->fetchDataFromDB("question_type","*");
            
                    $questionTypes = [];
                    foreach($questionTypesRaw as $value){
                        $questionTypes[$value["number"]] = $value["name"];
                    };
        
                    $questionTypesHtml = "";
                    foreach($questionTypes as $key => $value) {
                        $questionTypesHtml .= '<option value="'.$key.'">'.$value.'</option>';
                    }
        
                }else{
                    header("Location: ../error.php");
                }
            }else{
                header("Location: ../error.php");
            }
        }else{
            header("Location: ../user/login/login.php");
        }
    }else{
        header("Location: ../error.php");
    }
}else{
    header("Location: ../error.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor | Formative</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        :root{
            --form-background: white;
            --form-color: white;
            --form-font: 'Inter Tight', sans-serif;
        }
    </style>
    <link rel="stylesheet" href="../../assets/lib/css/pretty-checkbox/dist/pretty-checkbox.min.css">
    <link rel="stylesheet" href="../../assets/global/general.css">
    <link rel="stylesheet" href="css/editor.css">
    <script src="../../assets/lib/js/general.js"></script>
    <script src="../../assets/lib/js/profileMenu.js"></script>
    <script>
        let questionTypes = '<?php echo json_encode($questionTypes) ?>';
        let formId = '<?php echo $_GET["id"] ?>';
        let user = '<?php echo $_SESSION["user"] ?>';
    </script>
    <script src="js/editor.js"></script>
    <link rel="icon" type="image/png" href="../../assets/img/logo/favicon.png">
</head>
<body>
    <div class="windowContainer chooseTypeContainer">
        <div class="window chooseType">
            <h3>Choose question type</h3>
            <select>
                <?php echo $questionTypesHtml ?>
            </select>
            <div class="questionPreview">

            </div>
            <div class="buttons">
                <a class="close">Close</a>
                <a class="add">Add</a>
            </div>
        </div>
    </div>

    <?php 
        $PageSpecific = "<input type='text' class='formName' id='formName' placeholder='Enter form name'>";
        $location = "Editor";
        include("../../assets/global/headerSidebar.php") 
    ?>
    <span class="mdi mdi-account-file"></span>
    <div id="content">
        <div class="centerForm">
            <div class="form">
            </div>
        </div>

        <div class="settings">
            <div class="formSettings">
                <h3>Form settings</h3>
                <div class="set">
                    <p>Anonymous</p>
                    <div class="pretty p-switch p-fill">
                        <input type="checkbox" id="anonymous"/>
                        <div class="state p-primary">
                            <label></label>
                        </div>
                    </div>
                </div>
                <div class="set">
                    <p>Background color</p>
                    <input type="color" id="formBackgroundColor" value="#ffffff">
                </div>
                <div class="set">
                    <p>Color</p>
                    <input type="color" id="formColor" value="#ffffff">
                </div>
                <div class="set">
                    <p>Font</p>
                    <select id="formFont">
                        <option value="Inter Tight">Inter Tight</option>
                        <option value="Arial">Arial</option>
                        <option value="Comic Sans MS">Comic Sans</option>
                    </select>
                </div>
            </div>
            <div class="questionSettings">
                <h3>Question settings</h3>
                <div class="questionSettingsDiv">

                </div>
            </div>
            <div class="save">
                <h3>Save</h3>
                <div class="set">
                    <p>Auto save</p>
                    <select id="autoSave">
                        <option value="2">2s</option>
                        <option value="5">5s</option>
                        <option value="10">10s</option>
                        <option value="none">none</option>
                    </select>
                </div>
                <div class="set">
                    <p>Last save was</p>
                    <p id="lastSaveTime">Now</p>
                </div>
                <span class="saveForm">Save All</span>
            </div>
        </div>
    </div>
</body>
</html>
