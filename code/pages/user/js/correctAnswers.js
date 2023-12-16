
$(document).ready(function(){
    console.log(questions)
    console.log(guest)
    generateQuestion(questions, guest)
})

function generateQuestion(questions,guest){

    $("#customStyles").html("")
    $(".questionInfo").html("")
    questions.forEach(function(element){  
        $.ajax({
        type: 'POST',
        url: "../admin/action/generateQuestion.php",
        data: {"data": element, "guest": guest},
        success: function(html) {
            $(".questionInfo").append(html)
        }
        })
    })
}