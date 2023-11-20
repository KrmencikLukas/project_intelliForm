<?php

include("../../assets/lib/php/db.php");
include("../../assets/lib/php/DBlibrary.php");

$DBlib = new DatabaseFunctions($db);
session_start();

//provizorni nacteni uzivatele
$_SESSION["user"] = 1;

if(isset($_GET["id"])){
    if(is_numeric($_GET["id"])){
        if(isset($_SESSION["user"])){
            if($_SESSION["user"] == $DBlib->fetchDataWithCondition("form", "user_id", "id = :id", [":id" => $_GET["id"]])[0]["user_id"]){
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
    <title>Inteliform - Editor</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
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
                <div class="question type0" id="qId5">

                    <div class="absolute">
                        <select id="mySelect" name="mySelect">
                            <?php echo $questionTypesHtml ?>
                        </select>
                        <i class="mdi mdi-trash-can-outline" id="deleteForm5"></i>
                    </div>

                    
                    <input type="text" class="questionHeading" placeholder="Enter question">
                    <div class="descriptionContainer">
                        <textarea class="description" rows="1" placeholder="Enter description" oninput="autoGrow(this)"></textarea>
                    </div>
                    <div class="answers">
    
                        <div class="answer yes">
                            <p>Yes</p>
                        </div>

                        <div class="answer no">
                            <p>No</p>
                        </div>
                    </div>    
                </div>





                <div class="question" id="qId6">
                    <div class="absolute">
                        <select id="mySelect" name="mySelect">
                            <?php echo $questionTypesHtml ?>
                        </select>
                        <i class="mdi mdi-trash-can-outline" id="deleteForm5"></i>
                    </div>
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
                        <div class="newAnswer">
                            <i class="mdi mdi-plus"></i>
                            <h3>Add answer</h3>
                        </div>
                    </div>
                </div>






                
                <div class="question" id="qId6">
                    <div class="absolute">
                        <select id="mySelect" name="mySelect">
                            <?php echo $questionTypesHtml ?>
                        </select>
                        <i class="mdi mdi-trash-can-outline" id="deleteForm5"></i>
                    </div>
                    <input type="text" class="questionHeading" placeholder="Enter question">
                    <div class="descriptionContainer">
                        <textarea class="description" rows="1" placeholder="Enter description" oninput="autoGrow(this)"></textarea>
                    </div>
                    <div class="answers">
                        <div class="answer">
                            <div class="pretty p-icon p-round p-smooth p-bigger p-locked p-toggle answerBox">
                                <input type="checkbox" />
                                <div class="state p-off">
                                    <i class="icon mdi mdi-arrow-up"></i>
                                    <label></label>
                                </div>
                            </div>
                            <div class="pretty p-icon p-round p-smooth p-bigger p-locked p-toggle answerBox">
                                <input type="checkbox" />
                                <div class="state p-off">
                                    <i class="icon mdi mdi-arrow-down"></i>
                                    <label></label>
                                </div>
                            </div>
                            <input type="text" class="answerInput" placeholder="Enter answer">
                            <i class="mdi mdi-close delete"></i>
                        </div>

                        <div class="answer">
                            <div class="pretty p-icon p-round p-smooth p-bigger p-locked p-toggle answerBox">
                                <input type="checkbox" />
                                <div class="state p-off">
                                    <i class="icon mdi mdi-arrow-up"></i>
                                    <label></label>
                                </div>
                            </div>
                            <div class="pretty p-icon p-round p-smooth p-bigger p-locked p-toggle answerBox">
                                <input type="checkbox" />
                                <div class="state p-off">
                                    <i class="icon mdi mdi-arrow-down"></i>
                                    <label></label>
                                </div>
                            </div>
                            <input type="text" class="answerInput" placeholder="Enter answer">
                            <i class="mdi mdi-close delete"></i>
                        </div>
                        <div class="newAnswer">
                            <i class="mdi mdi-plus"></i>
                            <h3>Add answer</h3>
                        </div>
                    </div>
                </div>






                <div class="question type3" id="qId7">
                    <div class="absolute">
                        <select id="mySelect" name="mySelect">
                            <?php echo $questionTypesHtml ?>
                        </select>
                        <i class="mdi mdi-trash-can-outline" id="deleteForm5"></i>
                    </div>  
                    <input type="text" class="questionHeading" placeholder="Enter question">
                    <div class="descriptionContainer">
                        <textarea class="description" rows="1" placeholder="Enter description" oninput="autoGrow(this)"></textarea>
                    </div>
                    <div class="answers">
    
                        <div class="answer yes">
                            <div class="pretty p-toggle p-plain">
                                <input type="radio" name="radio1">
                                <div class="state p-off">
                                    <label>Yes</label>
                                </div>
                                <div class="state p-on">
                                    <label class="color">Yes</label>
                                </div>
                            </div>
                        </div>

                        <div class="answer no">
                            <div class="pretty p-toggle p-plain">
                                <input type="radio" name="radio1">
                                <div class="state p-off">
                                    <label>No</label>
                                </div>
                                <div class="state p-on">
                                    <label class="color">No</label>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>




                <div class="question" id="qId8">
                    <div class="absolute">
                        <select id="mySelect" name="mySelect">
                            <?php echo $questionTypesHtml ?>
                        </select>
                        <i class="mdi mdi-trash-can-outline" id="deleteForm5"></i>
                    </div>
                    <input type="text" class="questionHeading" placeholder="Enter question">
                    <div class="descriptionContainer">
                        <textarea class="description" rows="1" placeholder="Enter description" oninput="autoGrow(this)"></textarea>
                    </div>
                    <div class="answers">
                        <div class="answer" >
                            <div class="pretty p-icon p-round p-smooth p-bigger answerBox">
                                <input type="checkbox">
                                <div class="state p-primary">
                                    <i class="icon mdi mdi-check"></i>
                                    <label></label>
                                </div>
                            </div>
                            <input type="text" class="answerInput" placeholder="Enter answer">
                            <i class="mdi mdi-close delete"></i>
                        </div>

                        <div class="answer" >
                            <div class="pretty p-icon p-round p-smooth p-bigger answerBox">
                                <input type="checkbox">
                                <div class="state p-primary">
                                    <i class="icon mdi mdi-check"></i>
                                    <label></label>
                                </div>
                            </div>
                            <input type="text" class="answerInput" placeholder="Enter answer">
                            <i class="mdi mdi-close delete"></i>
                        </div>
                        <div class="newAnswer">
                            <i class="mdi mdi-plus"></i>
                            <h3>Add answer</h3>
                        </div>
                    </div>
                </div>
                <div class="addQuestion">
                    <i class="mdi mdi-plus"></i>
                    <h3>Add Question</h3>
                </div>
            </div>
        </div>

        <div class="settings">
            <div class="formSettings">
                <h3>Form settings</h3>
            </div>
            <div class="questionSettings">
                <h3>Question settings</h3>
            </div>
            <div class="save">
                <h3>Save</h3>
                <span class="saveForm">Save All</span>
            </div>
        </div>
    </div>
</body>
</html>
