function checkboxToRadio(qId,min,max){

    let checkboxes = {}

    numberOfChecked = 0;
    console.log("#qId"+qId+" .answer")

    $("#qId"+qId+" input[type=checkbox]").each(function() {

        checkboxes[qId] = []

        $(this).change(function() {

            if($(this).is(':checked')){
                checkboxes[qId].push(this)
                if(numberOfChecked == max){
                    $(checkboxes[qId][0]).prop('checked', false);
                    checkboxes[qId].shift()
                }else{
                    numberOfChecked++
                }
            }else{
                if(numberOfChecked == min){
                    $(this).prop('checked', true);
                }else{
                    checkboxes[qId].splice(checkboxes[qId].indexOf(this), 1) 
                    numberOfChecked--
                }
            }

            console.log(numberOfChecked)
            console.log(checkboxes)
        })
    })
}