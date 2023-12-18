//definice promenych
let lastSave
let needSave
let lastSaveTime = 0

//Funkce pro auto scalovani textarea
function autoGrow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight) + "px";
}


//funcle pro focus na otazku
let focusQuestion = "none"
function focus(element){
    let questionId = element.id.split('Q')[1]
    generateQuestionSettings(questionSettingsJson[questionId],questionId)
    $(focusQuestion).removeClass("focus")
    $(element).addClass("focus")
    focusQuestion = element;

    $('.formSettings').css('transform', 'translate(100%, 0)');
    setTimeout(function() {
        $('.formSettings').css('display', 'none');
        $('.questionSettings').css('display', 'flex');
        setTimeout(function() {
            $('.questionSettings').css('transform', 'translate(0, 0)');
        },5)
    },500)

    
}

//Autosave funkce

let autoSaveInterval
let autoSave

function setAutoSave(){
    let autoSave = $("#autoSave").val();
    clearInterval(autoSaveInterval);
    if(autoSave != "none"){
        autoSave = parseInt(autoSave) * 1000

        autoSaveInterval = setInterval(function(){
            if(needSave){
                saveForm()
            }
        },autoSave)
    }
}



function afterLoad(){

    //cas od posledniho ulozeni
    setInterval(function(){
        lastSaveTime++
        let s
        if(lastSaveTime == 1){
            s = "second"
        }else{
            s = "seconds"
        }
        $("#lastSaveTime").text(" "+lastSaveTime+" "+s+" ago.")
        
    },1000)

    //Voláni autosave funkce
    setAutoSave()

    $("#autoSave").change(function() {
        setAutoSave()
    });

    //Je potreba ulozeni
    setInterval(function(){
        if(JSON.stringify(lastSave) == JSON.stringify(generateJson())){
            $(".save .saveForm").css("background-color", "var(--gray-line)");
            $(".save .saveForm").css("color", "var(--light-gray)");
            $(".save .saveForm").css("pointer-events", "none");
            needSave = false
        }else{
            $(".save .saveForm").css("background-color", "var(--blue)");
            $(".save .saveForm").css("color", "white");
            $(".save .saveForm").css("pointer-events", "auto");
            needSave = true
        }
    },1000)

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
                    console.log(loadForm(true))
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
                    $("#Q"+questionId).remove();
                    delete questionSettingsJson[questionId];
                }
            },
        });

    });

    //Create odpovedi
    $("body").on("click",".newAnswer",function(){
        let questionId = parseInt(this.id.split('createAnswer')[1])

        $.ajax({
            type: 'POST',
            url: 'action/createAnswer.php',
            data: {"questionId": questionId},
            success: function(response) {
                if(response != 0){
                    //muze byt optimalizovano
                    saveForm()
                    loadForm()
                }
            },
        });

    });

    //Delete odpovedi
    $("body").on("click",".answer .delete",function(){
        let answerId = parseInt(this.id.split('deleteAnswer')[1])

        $.ajax({
            type: 'POST',
            url: 'action/deleteAnswer.php',
            data: {"id": answerId},
            success: function(response) {
                if(response != 0){
                    $("#A"+answerId).remove();
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

    //odstarneni focusu
    $(document).on('click', function(event) {
        let clickedElement = $(event.target);
    
        let div1 = $('.question');
        let div2 = $('.questionSettings');
        let div3 = $('.save');
    

        if (
            (!clickedElement.is(div1) && !div1.has(clickedElement).length) &&
            (!clickedElement.is(div2) && !div2.has(clickedElement).length) &&
            (!clickedElement.is(div3) && !div3.has(clickedElement).length)
        ) {
            $(focusQuestion).removeClass("focus")
            focusQuestion = "none"
            $('.questionSettings').css('transform', 'translate(100%, 0)');
            setTimeout(function() {
                $('.questionSettings').css('display', 'none');
                $('.formSettings').css('display', 'flex');
                setTimeout(function() {
                    $('.formSettings').css('transform', 'translate(0, 0)');
                },5)
            },700)
        }
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
                if(response != 0){
                    saveForm("correctness")
                    loadForm()
                }
            },
        });
    });


    //Save formu
    $("body").on("click",".saveForm",function(){
        saveForm()
    });


    //zmena vizualu formu
    $("#formBackgroundColor").on("input", function() {
        $("html").css("--form-background", $(this).val());
    });

    $("#formColor").on("input", function() {
        $("html").css("--form-color", $(this).val());
    });

    $("#formFont").on("input", function() {
        $("html").css("--form-font", $(this).val() + ", sans-serif");
    });

    $(".exportForm").on("click", function(){
        $.ajax({
            url:"action/readForm.php",
            type:"POST",
            data:{id: formId},
            success:function(r){
                var dec = JSON.parse(r)
                var blob = new Blob([r], { type: "application/json" });

                var a = document.createElement("a");
                a.href = window.URL.createObjectURL(blob);

                a.download = `${dec.name}.frd`;
                
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                window.URL.revokeObjectURL(blobUrl);
            }
        })

    })
}


//Save formu
function saveForm(without){

    json = generateJson(without)

    $.ajax({
        type: 'POST',
        url: 'action/updateForm.php',
        data: {"data": JSON.stringify(json)},
        success: function(response) {
            if(response != 0){
                lastSave = generateJson()
                lastSaveTime = 0
                $(".save .saveForm").css("background-color", "var(--gray-line)");
                $(".save .saveForm").css("color", "var(--light-gray)");
                $(".save .saveForm").css("pointer-events", "none");
                needSave = false
            }
        },
    });
}

function generateJson(without){
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
            "settings": JSON.parse(JSON.stringify(questionSettingsJson[questionId])),
        }
    });

    let json = {
        "id": formId,
        "name": $("#formName").val(),
        "user": user,
        "questions": qJson,
        "settings": {}
    }

    settingsArray = [
        ["#anonymous","anonymous",$("#anonymous").prop("checked") ? 1 : 0],
        ["#formBackgroundColor","background color",$("#formBackgroundColor").val()],
        ["#formColor","color",$("#formColor").val()],
        ["#formFont","font",$("#formFont").val()],
    ]

    for(let i = 0; i < settingsArray.length; i++){
        json["settings"][parseInt($(settingsArray[i][0]).attr("class").split('fSet')[1])] = {
            key: settingsArray[i][1],
            value: settingsArray[i][2],
        }
    }
    return json;
}


//nacteni formu
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
                $("html").css("--form-background", $("#formBackgroundColor").val());
                $("html").css("--form-color", $("#formColor").val());
                $("html").css("--form-font", $("#formFont").val() + ", sans-serif");

                if(focusQuestion != "none"){
                    $("#"+focusQuestion.id).addClass("focus")
                }
            }
        },
    });
}


//ready funkce
$(document).ready(function(){
    afterLoad();
    loadForm()
});




//generovani formulare
function generateForm(json){
    let html = ""

    for(let key in json["settings"]){
        value = json["settings"][key]
        if(value["key"] == "anonymous"){
            $("#anonymous").addClass("fSet"+key);
            if(value["value"] == "0"){
                $("#anonymous").prop("checked", false)
            }else{
                $("#anonymous").prop("checked", true)
            }
        }

        if(value["key"] == "background color"){
            $("#formBackgroundColor").addClass("fSet"+key);
            $("#formBackgroundColor").val(value["value"]);
        }

        if(value["key"] == "color"){
            $("#formColor").addClass("fSet"+key);
            $("#formColor").val(value["value"]);
        }

        if(value["key"] == "font"){
            $("#formFont").addClass("fSet"+key);
            $("#formFont").val(value["value"]);
        }
    }

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

let questionSettingsJson = {}

function generateQuestion(id,heading,description,type,settings,answers){

    questionTypesHtml = ""

    for (let key in questionTypes) {
        if(key == type.number){
            questionTypesHtml += '<option value="'+key+'" selected>'+questionTypes[key]+'</option>'
        }else{
            questionTypesHtml += '<option value="'+key+'">'+questionTypes[key]+'</option>'
        }
    }

    questionSettingsJson[id] = settings

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

    let backgroundColor
    let color

    for(key in settings){
        if(settings[key]["key"] == "Background color"){
            backgroundColor = settings[key]["value"]
        }else if(settings[key]["key"] == "Text color"){
            color = settings[key]["value"]
        }
    }

    let html = `
    <div class="question type${type["number"]}" id="Q${id}" style="background-color: ${backgroundColor}; color: ${color};'">
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

function generateQuestionSettings(settings,id){
    
    $(".questionSettingsDiv").html("")

    for (let key in settings) {

        let input = ""
        let checkedCHB = ""

        if(settings[key]["key"] == "Mandatory" || settings[key]["key"] == "Public vote count"){
            
            if(settings[key]["value"] == "1"){
                checkedCHB = "checked"
            }
            input = `
            <div class="pretty p-switch p-fill">
                <input type="checkbox" id="QS${key}" ${checkedCHB}/>
                <div class="state p-primary">
                    <label></label>
                </div>
            </div>
            `
            insertSettings(input,settings[key]["key"])

            $("#QS"+key).on("input", function() {
                questionSettingsJson[id][key]["value"] = $(this).prop("checked") ? 1 : 0
            });


        }else if(settings[key]["key"] == "Background color" || settings[key]["key"] == "Text color"){
            insertSettings(`<input type="color" id="QS${key}" value="${settings[key]["value"]}">`,settings[key]["key"])

            if(settings[key]["key"] == "Background color"){
                $("#QS"+key).on("input", function() {
                    $("#Q"+id).css("background-color", $(this).val())
                    questionSettingsJson[id][key]["value"] = $(this).val()
                });
            }else{
                $("#QS"+key).on("input", function() {
                    $("#Q"+id).css("color", $(this).val());
                    questionSettingsJson[id][key]["value"] = $(this).val()
                });
            }


        }else if(
        settings[key]["key"] == "Min upvotes"
        || settings[key]["key"] == "Max upvotes"
        || settings[key]["key"] == "Min downvotes"
        || settings[key]["key"] == "Max downvotes"
        || settings[key]["key"] == "Min votes"
        || settings[key]["key"] == "Max votes"
        ){
            input = `
            <input type="number" id="QS${key}" value="${settings[key]["value"]}">
            `

            insertSettings(input,settings[key]["key"])

            $("#QS"+key).on("input", function() {
                questionSettingsJson[id][key]["value"] = $(this).val()
            });
        }


    }
}

function insertSettings(input,id){
    $(".questionSettingsDiv").append(`
    <div class="set">
        <p>${id}</p>
        ${input}
    </div> 
    `);
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