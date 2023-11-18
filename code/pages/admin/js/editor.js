

//Funkce pro auto scalovani textarea
function autoGrow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight) + "px";
}


//funcle pro focus na ot8zku nebo formular
let focusQuestion
function focus(element){
    $(focusQuestion).removeClass("focus")
    $(element).addClass("focus")
    focusQuestion = element;
}

function afterLoad(){
    //nacteni pole z PHP
    questionTypes = JSON.parse(questionTypes)

    //tlacitko close v okne pro typ otazky
    $(".chooseType .close").click(function(){
        $(".chooseTypeContainer").fadeOut(300);
    });
    
     //tlacitko ADD v okne pro typ otazky
    $("body").on("click",".chooseType .add",function(){
        $(".chooseTypeContainer").fadeOut(300);
        type = $(".chooseType select").val()

        $.ajax({
            type: 'POST',
            url: 'action/createQuestion.php',
            data: {"id": formId, "type": type},
            success: function(data) {
                json = JSON.parse(data);
                if(json != 0){
                    saveForm()
                }
            },
        });

    });

    //Delete otazky
    $("body").on("click",".question .deleteButton",function(){
        let questionId = parseInt(this.id.split('deleteForm')[1])

        $.ajax({
            type: 'POST',
            url: 'action/deleteQuestion.php',
            data: {"id": questionId},
            success: function(response) {
                if(response != 0){
                    saveForm()
                }
            },
        });

    });

    //Create odpovedi
    $("body").on("click",".newAnswer",function(){
        let questionId = parseInt(this.id.split('createAnswer')[1])

        console.log(questionId)

        $.ajax({
            type: 'POST',
            url: 'action/createAnswer.php',
            data: {"questionId": questionId},
            success: function(response) {
                console.log(response)
                if(response != 0){
                    saveForm()
                }
            },
        });

    });

    //Delete odpovedi
    $("body").on("click",".answer .delete",function(){
        let answerId = parseInt(this.id.split('deleteAnswer')[1])

        console.log(answerId)

        $.ajax({
            type: 'POST',
            url: 'action/deleteAnswer.php',
            data: {"id": answerId},
            success: function(response) {
                console.log(response)
                if(response != 0){
                    saveForm()
                }
            },
        });

    });

    //vytvorit novou otazku
    $("body").on("click",".addQuestion",function(){
        $(".chooseTypeContainer").fadeIn(300);
        $(".chooseTypeContainer").css("display", "flex");
    });

    //fucus na jednotlive otazky
    $("body").on("click",".question",function(){
        focus(this)
    });

    //Zmnena typu otazky
    $("body").on("change",".typeSelect",function(){
        let questionId = parseInt(this.id.split('typeSelect')[1])
        var type = $(this).val();

        $.ajax({
            type: 'POST',
            url: 'action/changeType.php',
            data: {"id": questionId, "type": type},
            success: function(response) {
                console.log(response)
                if(response != 0){
                    saveForm("correctness")
                }
            },
        });
    });


    //Save formu
    $("body").on("click",".saveForm",function(){
        saveForm()
    });
}

//Save formu
function saveForm(without){
    let aJson = {}

    $(".answer").each(function(index, element) {
        let answerId = parseInt(element.id.split('A')[1])

        aJson[answerId] = {};
        
        if($(element).is(".typeA0")){
            aJson[answerId]["name"] = $("#"+element.id+" p").text()

        }else if($(element).is(".typeA3")){
            aJson[answerId]["name"] = $("#"+element.id+" .p-off label").text()

        }else {
            aJson[answerId]["name"] = $("#"+element.id+" .answerInput").val()
        }

        if(without != "correctness"){
            if($(element).is(".typeA3")){
                aJson[answerId]["correctness"] = $("#"+element.id+" input[type='radio']").prop("checked");
    
            }else if($(element).is(".typeA4")){
                aJson[answerId]["correctness"] = $("#"+element.id+" input[type='checkbox']").prop("checked");
            }
        }

    });

    let qJson = {}

    $(".question").each(function(index, element) {
        let questionId = parseInt(element.id.split('Q')[1])
        
        qJson[questionId] = {
            "heading": $("#"+element.id+" .questionHeading").val(),
            "description": $("#"+element.id+" .description").val(),
            "answers": aJson,
            "settings": [],
        }
    });

    let json = {
        "id": formId,
        "name": $("#formName").val(),
        "user": user,
        "questions": qJson,
        "settings": []
    }

    console.log(json)


    $.ajax({
        type: 'POST',
        url: 'action/updateForm.php',
        data: {"data": JSON.stringify(json)},
        success: function(response) {
            if(response != 0){
                loadForm()
            }
        },
    });
}

function loadForm(){
    let json = ""

    $.ajax({
        type: 'POST',
        url: 'action/readForm.php',
        data: {id: formId},
        success: function(data) {
            json = JSON.parse(data);
            if(json != 0){
                $(".form").html(generateForm(json));
            }
        },
    });
}


//ready funkce
$(document).ready(function(){
    afterLoad();
    loadForm()
});



function generateForm(json){
    let html = ""

    $("#formName").val(json.name);

    for (let key in json.questions){
        value = json.questions[key]
        html += generateQuestion(key,value.heading,value.description,value.type,value.settings,value.answers)
    }

    html += `
    <div class="addQuestion">
        <i class="mdi mdi-plus"></i>
        <h3>Add Question</h3>
    </div>
    `

    return html
}

function generateQuestion(id,heading,description,type,settings,answers){

    questionTypesHtml = ""

    for (var key in questionTypes) {
        if(key == type.number){
            questionTypesHtml += '<option value="'+key+'" selected>'+questionTypes[key]+'</option>'
        }else{
            questionTypesHtml += '<option value="'+key+'">'+questionTypes[key]+'</option>'
        }
    }

    let answersHtml = ""
    for (let key in answers){
        value = answers[key]
        answersHtml += generateAnswer(key,value.name,value.correctness,type.number)
    }

    let newAnswer = ""
    if(type.number != 0 && type.number != 3){
        newAnswer = `
            <div class="newAnswer" id="createAnswer${id}">
                <i class="mdi mdi-plus"></i>
                <h3>Add answer</h3>
            </div>
        `
    }

    let html = `
    <div class="question type${type["number"]}" id="Q${id}">
        <div class="absolute">
            <select id="typeSelect${id}" class='typeSelect'>
                ${questionTypesHtml}
            </select>
            <i class="mdi mdi-trash-can-outline deleteButton" id="deleteForm${id}"></i>
        </div>
        <input type="text" class="questionHeading" placeholder="Enter question" value="${heading}">
        <div class="descriptionContainer">
            <textarea class="description" rows="1" placeholder="Enter description" oninput="autoGrow(this)">${description}</textarea>
        </div>
        <div class="answers">
        ${answersHtml}
        ${newAnswer}
        </div>
    </div>

    `
    if(type["number"] == 4){
        html += `<script> checkboxToRadio(${id},1,Infinity) </script>`
    }
    

    return html 
}

function generateAnswer(id,name,correctness,type){

    let html = ""
    let checked = "";

    if(type == 0){
        html += `
            <div class="answer ${name.toLowerCase()} typeA${type}" id="A${id}">
                <p>${name}</p>
            </div>
        `
    }else if(type == 1){
        html += `
            <div class="answer typeA${type}" id="A${id}">
                <div class="pretty p-icon p-round p-smooth p-bigger p-locked answerBox">
                    <input type="checkbox" />
                    <div class="state p-primary">
                        <i class="icon mdi mdi-check"></i>
                        <label></label>
                    </div>
                </div>
                <input type="text" class="answerInput" placeholder="Enter answer" value='${name}'>
                <i class="mdi mdi-close delete" id="deleteAnswer${id}"></i>
            </div>
        `

    }else if(type == 2){
        html += `
            <div class="answer typeA${type}" id="A${id}">
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
                <input type="text" class="answerInput" placeholder="Enter answer" value="${name}">
                <i class="mdi mdi-close delete" id="deleteAnswer${id}"></i>
            </div>
        `
    }else if(type == 3){

        checked = ""
        if(correctness == 1){
            checked = "checked"
        }

        html += `
            <div class="answer ${name.toLowerCase()} typeA${type}" id="A${id}">
                <div class="pretty p-toggle p-plain">
                    <input type="radio" name="radio1" ${checked}>
                    <div class="state p-off">
                        <label>${name}</label>
                    </div>
                    <div class="state p-on">
                        <label class="color">${name}</label>
                    </div>
                </div>
            </div>
        `
    }else if(type == 4){
        checked = ""
        if(correctness == 1){
            checked = "checked"
        }

        html += `
            <div class="answer typeA${type}" id="A${id}">
                <div class="pretty p-icon p-round p-smooth p-bigger answerBox">
                    <input type="checkbox" ${checked}>
                    <div class="state p-primary">
                        <i class="icon mdi mdi-check"></i>
                        <label></label>
                    </div>
                </div>
                <input type="text" class="answerInput" placeholder="Enter answer" value="${name}">
                <i class="mdi mdi-close delete" id="deleteAnswer${id}"></i>
            </div>
        `
    }   
    
    return html
}