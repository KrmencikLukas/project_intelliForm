
let checkboxes = {}
let numberOfChecked = []

function checkboxToRadio(qId,min,max){

    checkboxes[qId] = []

    numberOfChecked[qId] = 0;

    $("#Q"+qId+" input[type=checkbox]").each(function() {

        if($(this).is(':checked')){
            numberOfChecked[qId]++
        }

        $(this).change(function() {

            if($(this).is(':checked')){
                checkboxes[qId].push(this)
                if(numberOfChecked[qId] == max){
                    $(checkboxes[qId][0]).prop('checked', false);
                    checkboxes[qId].shift()
                }else{
                    numberOfChecked[qId]++
                }
            }else{
                if(numberOfChecked[qId] == min){
                    $(this).prop('checked', true);
                }else{
                    checkboxes[qId].splice(checkboxes[qId].indexOf(this), 1) 
                    numberOfChecked[qId]--
                }
            }

        })
    })
}