//definice promenych
let questions
let questionCtx1
let questionCtxArr
let questionCtx2
let chart1JS
let chartArrJS
let chart2JS
let qSelect

function appendParamsToUrl(params) {

    var currentUrl = window.location.href;
    var url = new URL(currentUrl);
  
    for (var key in params) {
      if(params.hasOwnProperty(key)) {
        url.searchParams.set(key, params[key]);
      }
    }
    window.history.replaceState({}, '', url.href);
}

$(document).ready(function(){

    write(page)

    $('#people').change(function() {
        write("people")
    })

    $('#question').change(function() {
        write("question")
    })

    $('#summary').change(function() {
        write("summary")
    })
    
    function write(what){
        let urlParams = new URLSearchParams(window.location.href);
        let guest = urlParams.get("guest");
        let question = urlParams.get("question");

        $.ajax({
            type: 'GET',
            url: "action/"+what+"Statistics.php",
            data: {"id": id, "question":question,"guest":guest},
            success: function(response) {
                appendParamsToUrl({page: what})
                $(".data").html(response)
            },
        });
    }

})






