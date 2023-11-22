$(document).ready(function(){

    $(".mdi-earth").on("click", function(e){        
        e.preventDefault()
        e.stopPropagation() 
        var anchorId = $(this).closest('a').attr('href').match(/(?:[?&])id=([^&]+)/);
        var idValue = anchorId ? anchorId[1] : null;

        console.log('Extracted id:', idValue);


    })
    $(".del").on("click", function(e){        
        e.preventDefault()
        e.stopPropagation()    
        var anchorId = $(this).closest('a').attr('href').match(/(?:[?&])id=([^&]+)/);
        var idValue = anchorId ? anchorId[1] : null;

        $.ajax({
            url:"../action/deleteForm.php",
            type:"POST",
            data:{id: idValue},
            success:() =>{
                $(this).closest('a').empty()
                $(this).closest('a').remove()
            }
        })
    })
})