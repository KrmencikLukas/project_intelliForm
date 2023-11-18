

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
    console.log(focusQuestion)
}

function afterEdit(){
    //nacteni pole z PHP
    questionTypes = JSON.parse(questionTypes)

    //tlacitko close v okne pro typ otazky
    $(".chooseType .close").click(function(){
        $(".chooseTypeContainer").fadeOut(300);
    });

    //vytvorit novou otazku
    $(".addQuestion").click(function(){
        $(".chooseTypeContainer").fadeIn(300);
        $(".chooseTypeContainer").css("display", "flex");
    });

    let json = ""

    $.ajax({
        type: 'POST',
        url: 'action/readForm.php',
        data: {id: 25},
        success: function(data) {
            json = JSON.parse(data);
            console.log(json)
            $(".form").html(generateForm(json));
        },
    });

/*
    let json = '{"id":25,"name":"Politicka anketa","user":1,"settings":[],"questions":{"68":{"heading":"Mas rad Fialu","description":"Premiera Ceske republiky","type":{"id":1,"number":0,"name":"Yes\/No poll","description":"Poll where the user can only answer yes, no or abstain."},"media":[],"settings":[],"answers":{"72":{"name":"Yes","correctness":null},"73":{"name":"No","correctness":null}}},"69":{"heading":"Kdo z techto prezidentu je\/byl podle vas nejlepsi?","description":"Je to velice dulezite","type":{"id":3,"number":1,"name":"Own options poll","description":"Poll where you can set an unlimited number of options. And you can allow the user to check the number of options you choose."},"media":[],"settings":[],"answers":{"74":{"name":"Petr Pavel","correctness":null},"75":{"name":"Milos Zeman","correctness":null},"76":{"name":"Vaclav Klaus","correctness":null}}},"70":{"heading":"Zvol podle tebe nejlepsiho kandidata","description":"Kdyby jsi mohl znovu volit","type":{"id":4,"number":2,"name":"Upvote\/Downvote poll","description":"Poll where you can set an unlimited number of options. And you can allow the user to check your chosen number of upvotes and downvotes."},"media":[],"settings":[],"answers":{"77":{"name":"Jaroslav Basta","correctness":null},"78":{"name":"Danuse Nerudova","correctness":null},"79":{"name":"Petr Pavel","correctness":null},"80":{"name":"Andrej Babis","correctness":null}}},"71":{"heading":"Je Petr Pavel aktualni prezident ?R","description":"Je to eazy","type":{"id":5,"number":3,"name":"Yes\/No quiz","description":"A quiz in which the user can only tick yes or no and only one is correct."},"media":[],"settings":[],"answers":{"81":{"name":"Yes","correctness":"1"},"82":{"name":"No","correctness":"0"}}},"72":{"heading":"Vyberte strany co jsou ve vladni koalici","description":"Nejsou tam vsechny","type":{"id":6,"number":4,"name":"Own options quiz","description":"A quiz where you can set how many of your own answers you want."},"media":[],"settings":[],"answers":{"83":{"name":"ODS","correctness":"1"},"84":{"name":"SPD","correctness":"0"},"85":{"name":"STAN","correctness":"1"},"86":{"name":"ANO","correctness":"0"}}}}}'
*/

    //fucus na jednotlive otazky
    $(".question").click(function(){
        focus(this)
    });
}


//ready funkce
$(document).ready(function(){
    afterEdit();
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
        console.log(value);
        answersHtml += generateAnswer(key,value.name,value.correctness,type.number)
    }

    let newAnswer = ""
    if(type.number != 0 && type.number != 3){
        newAnswer = `
            <div class="newAnswer">
                <i class="mdi mdi-plus"></i>
                <h3>Add answer</h3>
            </div>
        `
    }

    let html = `
    <div class="question type${type["number"]}" id="Q${id}">
        <div class="absolute">
            <select id="mySelect" name="mySelect">
                ${questionTypesHtml}
            </select>
            <i class="mdi mdi-trash-can-outline" id="deleteForm${id}"></i>
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

    return html 
}

function generateAnswer(id,name,correctness,type){

    let html = ""
    let checked = "";

    if(type == 0){
        html += `
            <div class="answer ${name.toLowerCase()}" id="A${id}">
                <p>${name}</p>
            </div>
        `
    }else if(type == 1){
        html += `
            <div class="answer" id="A${id}">
                <div class="pretty p-icon p-round p-smooth p-bigger p-locked answerBox">
                    <input type="checkbox" />
                    <div class="state p-primary">
                        <i class="icon mdi mdi-check"></i>
                        <label></label>
                    </div>
                </div>
                <input type="text" class="answerInput" placeholder="Enter answer" value='${name}'>
                <i class="mdi mdi-close delete" id="DA${id}"></i>
            </div>
        `

    }else if(type == 2){
        html += `
            <div class="answer" id="A${id}">
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
                <i class="mdi mdi-close delete" id="DA${id}"></i>
            </div>
        `
    }else if(type == 3){

        checked = ""
        if(correctness == 1){
            checked = "checked"
        }

        html += `
            <div class="answer ${name.toLowerCase()}" id="A${id}">
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
            <div class="answer" id="A${id}">
                <div class="pretty p-icon p-round p-smooth p-bigger answerBox">
                    <input type="checkbox" ${checked}>
                    <div class="state p-primary">
                        <i class="icon mdi mdi-check"></i>
                        <label></label>
                    </div>
                </div>
                <input type="text" class="answerInput" placeholder="Enter answer" value="${name}">
                <i class="mdi mdi-close delete"></i>
            </div>
        `
    }   
    
    return html
}