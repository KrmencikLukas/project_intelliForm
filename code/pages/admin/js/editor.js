
function autoGrow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight) + "px";
}

function divCheckBox() {
    
}


$(document).ready(function(){
    let json = '{"id":8,"name":"New form","user":1,"settings":{"3":{"key":"color","value":"red"},"4":{"key":"bg","value":"black"}},"questions":{"6":{"heading":"Mas rad Babise?","description":"STBaka","type":{"id":5,"number":1,"name":"Yes\/No quiz","description":"A quiz in which the user can only tick yes or no and only one is correct."},"media":[],"settings":{"3":{"key":"color","value":"blue"}},"answers":{"4":{"name":"ne","correctness":"1"}}}}}'

    json = JSON.parse(json);

    console.log(json)

    //$(".form").html(generateForm(json));
});



function generateForm(json){
    let html = ""

    $("#formName").val(json.name);

    for (let key in json.questions){
        value = json.questions[key]
        html += generateQuestion(key,value.heading,value.description,value.type,value.settings,value.answers)
    }

    return html
}

function generateQuestion(id,heading,description,type,settings,answers){

    let answersHtml = ""
    for (let key in answers){
        value = answers[key]
        console.log(value);
        answersHtml += generateAnswer(value.id,value.name,value.correctness,type.number)
    }

    let html = `
    <div class="question" id="Q${id}">
        <input type="text" class="questionHeading" placeholder="Enter question" value="${heading}">
        <div class="descriptionContainer">
            <textarea class="description" rows="1" placeholder="Enter description" oninput="autoGrow(this)">${description}</textarea>
        </div>
        <div class="answers">
        ${answersHtml}
        </div>
    </div>

    `

    return html 
}

function generateAnswer(id,name,correctness,type){

    let html = ""

    if(type == 0){
        
    }else if(type == 1){
        html = `
        <div class="answer" id="A${id}">
            <div class="pretty p-icon p-round p-smooth p-bigger p-locked answerBox">
                <input type="checkbox" />
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