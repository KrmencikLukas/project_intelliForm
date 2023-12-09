//definice promenych
let questions
let questionCtx1
let questionCtx2
let chart1JS
let chart2JS

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
        $.ajax({
            type: 'GET',
            url: "action/"+what+"Statistics.php",
            data: {"id": id},
            success: function(response) {
                appendParamsToUrl({page: what})
                $(".data").html(response)
            },
        });
    }

})






