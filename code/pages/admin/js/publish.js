function copyToClipboard(text) {
    var $tempInput = $("<input>");
    $("body").append($tempInput);
  
    $tempInput.val(text).select();

    document.execCommand("copy");

    $tempInput.remove();
}
$(document).ready(function(){
    const queryString = window.location.search;
    const URLParams = new URLSearchParams(queryString);
    var Form = URLParams.get("id");
    var CurrentUser = JSON.parse(user)

    if(CurrentUser && Form !== null){

        $('input[name="user-type"]').change(function(){
            var everyone;  
            if ($('#everyone').is(':checked')) {
                everyone = 1 
            
            } else if ($('#Peoplelink').is(':checked')) {
                everyone = 0
            }
            $.ajax({
                url: "../action/publishFormEveryone.php",
                type:"POST",
                data:{form: Form, everyone: everyone},
                success:function(r){
                }
            })
        });

        var EmptyGuest

        $('#inviteButton').on("click", function(){
            $("#inviteForm").css("display", "flex")
            $("#inviteForm").removeClass("hidden")
            $(this).hide()
            $.ajax({
                url:"../action/publishEmptyGuest.php",
                type:"POST",
                data:{form: Form},
                success:function(guest){
                    EmptyGuest = JSON.parse(guest)
                }
            })
        }) 

        if (typeof unfinishedEmptyGuest !== "undefined") {
            EmptyGuest = JSON.parse(unfinishedEmptyGuest);
        }
 
        $("#delete").on("click", function(){
            $("#name").val("");
            $("#surname").val("") ;
            $("#email").val("");
            $("#copyField").val("");
            $.ajax({
                url:"../action/publishDeleteGuest.php",
                type:"POST",
                data:{form: Form, guest: EmptyGuest},
                success:function(res){
                    if(res){
                        $("#inviteForm").css("display", "none")
                        $("#inviteForm").addClass("hidden")
                        $("#inviteButton").show()
                        $('.editButton').prop('disabled', false);
                        var existingElement = $(`#invited .guest[data-id="${EmptyGuest}"]`);

                        if (existingElement.length > 0) {
                            existingElement.remove();
                        }
                    }
                }
            })
        })

        $('input[name="user-method"]').change(function(){
            var method;  
            if ($('#byLink').is(':checked')) {
                method = 1 
            } else if ($('#byEmail').is(':checked')) {
                method = 0
                $("#copyField").val("");
            }
            $.ajax({
                url: "../action/publishFormMethod.php",
                type:"POST",
                data:{form: Form, method: method, guest: EmptyGuest},
            })
        });

        $("#save").on("click", function(){
            var name = $("#name").val()
            var surname = $("#surname").val() 
            var email = $("#email").val()
       
            if(email){
                $(".ErrorDis").remove()
                $.ajax({
                    url: "../action/publishFormDataCreate.php",
                    type:"POST",
                    data:{name: name, surname: surname, email: email, form: Form, guest: EmptyGuest},
                    success:function(response){
                        data = JSON.parse(response)

                        if(!(data.Duplicate || data.EmailFormat)){
                            $("#copyField").val("");
                            $("#name").val("");
                            $("#surname").val("") ;
                            $("#email").val("");
                            $("#inviteForm").css("display", "none")
                            $("#inviteForm").addClass("hidden")
                            $("#inviteButton").show()
                            $('.editButton').prop('disabled', false);
                            data.forEach(e => {
                                var existingElement = $(`#invited .guest[data-id="${e.id}"]`);

                                if (existingElement.length > 0) {
                                    existingElement.remove();
                                }

                                var method = e.method == 0 ? "via email": "via link"; 
                                var copy = e.method == 1 ? "<span class='mdi mdi-content-copy copybtn'></span>" : "";
                                $("#invited").append(`
                                    <div class='guest' data-email=${e.email} data-id=${e.id}>
                                        <div class='titles'>
                                            <h2>${e.email}</h2>
                                            <p>${e.name}&nbsp;&nbsp;${e.surname}</p>
                                        </div>
                                        <p class='method'>${method}</p>
                                        <div class='btns'>
                                            <button class='editButton'>Edit</button>
                                            <button class='deleteButtonMini'>Delete</button>
                                        </div>
                                        <div class='copy'>${copy}</div>
                                    </div>
                                `)
                            });
                        }else{
                            if(data.EmailFormat){
                                $("#inviteForm").append(`<div class="ErrorDis"><h2 class="Errorh2">*</h2><p>${data.EmailFormat}</p></div>`);
                                setTimeout(function() {
                                    $(".ErrorDis").remove()
                                }, 5000);
                            }
                            if(data.Duplicate){
                                $("#inviteForm").append(`<div class="ErrorDis"><h2 class="Errorh2">*</h2><p>${data.Duplicate}</p></div>`);
                                setTimeout(function() {
                                    $(".ErrorDis").remove()
                                }, 5000);
                            }
                        }
                    } 
                })
            }else{
                if($(".ErrorDis").length === 0) {
                    $("#inviteForm").append('<div class="ErrorDis"><h2 class="Errorh2">*</h2><p>Email is requiered</p></div>');
                    setTimeout(function() {
                        $(".ErrorDis").remove()
                    }, 5000);
                }
            }
        })

        $('#invited').on('click', '.editButton', function() {
            var guestElement = $(this).closest('.guest');
            $('.editButton').not(this).prop('disabled', true);
            var email = guestElement.data('email');
            $.ajax({
                url:"../action/publishUpdateGuest.php",
                type:"POST",
                data:{form: Form, email: email},
                success:function(update){
                    var updateGuest = JSON.parse(update);
                    $("#inviteForm").css("display", "flex");
                    $("#inviteForm").removeClass("hidden");
                    $("#inviteButton").hide()
                    updateGuest.forEach(e => {
                        $("#name").val(e.name);
                        $("#surname").val(e.surname) ;
                        $("#email").val(e.email);
                        EmptyGuest = e.id 
                        if(e.method == 1){
                            $("#copyField").val(`http://project.lukaskrmencik.cz/S/code/pages/user/form.php?id=${Form}&guestId=${e.id}&code=${e.code}`)
                        }
                    })
                }
            })
        });

        $('#invited').on('click', '.deleteButtonMini', function() {
            var guestElement = $(this).closest('.guest');
            var delbtn = this
            var email = guestElement.data('email');

            $.ajax({
                url:"../action/publishDeleteGuest.php",
                type:"POST",
                data:{form: Form, email: email},
                success:function(respon){
                    $(delbtn).closest(".guest").remove()
                }
            })
        });

        $("#sendbtn").on("click", function(){
            $.ajax({
                url:"../action/publishFormEmail.php",
                type:"POST",
                data:{form: Form},
                success:function(e){
                    if(e === "Empty"){
                        var existingElement = $(`#side #emptyEmail`);

                        if (existingElement.length == 0) {
                            $("#side").append("<div id='emptyEmail'><p>No emails to send</p></div>")
                            $("#sendbtn").css("margin-top", "60px")
                            setTimeout(function() {
                                $("#emptyEmail").remove()
                                $("#SuccessEmail").remove()
                                $("#sendbtn").css("margin-top", "0px")
                            }, 3000);
                        }
                       
                    }else{
                        var existingElement = $(`#side #SuccessEmail`);

                        if (existingElement.length == 0) {
                            $("#side").append("<div id='SuccessEmail'><p>Emails successfully sent</p></div>")
                            $("#sendbtn").css("margin-top", "60px")
                            setTimeout(function() {
                                $("#sendbtn").css("margin-top", "0px")
                                $("#SuccessEmail").remove()
                            }, 3000);
                        }
                    }
                }
            })
        })

        $(".copybtn").on("click", function(){
            copyId = $(this).closest('.guest').data("id");
            $.ajax({
                url:"../action/publishGetCopyData.php",
                type: "POST",
                data:{form: Form, copyId: copyId},
                success:function(link){
                    var linkPart = JSON.parse(link)
                    var copyLink = `http://project.lukaskrmencik.cz/S/code/pages/user/form.php?${linkPart}`;
                    copyToClipboard(copyLink)
                }
            })
        })

        $(".copybtnform").on("click", function(){
            if ($('#byLink').is(':checked')) {
               copyToClipboard($("#copyField").val())
            }
        })

        $('input[name="user-method"]').change(function(){
            if($('#byLink').is(':checked')) {
                $.ajax({
                    url:"../action/publishGetCopyData2.php",
                    type: "POST",
                    data:{form: Form, EditGuest: EmptyGuest},
                    success:function(link2){
                        $("#copyField").val(`http://project.lukaskrmencik.cz/S/code/pages/user/form.php?${JSON.parse(link2)}`)
                    }
                })
            }
        });

    }

})