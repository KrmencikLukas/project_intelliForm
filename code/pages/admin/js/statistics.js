$(document).ready(function(){
    console.log(id)

    $('#people').change(function() {
        $.ajax({
            type: 'GET',
            url: "action/peopleStatistics.php",
            data: {"id": id},
            success: function(response) {
                $(".data").html(response)
            },
        });
    })
    
})



