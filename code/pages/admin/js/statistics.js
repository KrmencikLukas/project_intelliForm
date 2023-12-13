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

    $('#questions').change(function() {
        write("questions")
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


$("body").on("change", "#selectQuestion", function() {
    alert( $( this ).text() );
});



