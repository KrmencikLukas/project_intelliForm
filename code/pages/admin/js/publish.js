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
                    console.log(r)
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

        $("#save").on("click", function(){
            var name = $("#name").val()
            var surname = $("#surname").val() 
            var email = $("#email").val()
            var method
            if ($('#byEmail').prop('checked')) {
                method = 0;
            }else{
                method = 1;
            }

            if(email){
                $(".ErrorDis").remove()
                $.ajax({
                    url: "../action/publishFormDataCreate.php",
                    type:"POST",
                    data:{name: name, surname: surname, email: email, method: method, form: Form, guest: EmptyGuest},
                    success:function(response){
                        data = JSON.parse(response)

                        if(!(data.Duplicate || data.EmailFormat)){
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
            console.log('Edit button clicked for email:', email);
            $.ajax({
                url:"../action/publishUpdateGuest.php",
                type:"POST",
                data:{form: Form, email: email},
                success:function(update){
                    console.log(update)
                    var updateGuest = JSON.parse(update);
                    $("#inviteForm").css("display", "flex");
                    $("#inviteForm").removeClass("hidden");
                    $("#inviteButton").hide()
                    updateGuest.forEach(e => {
                        $("#name").val(e.name);
                        $("#surname").val(e.surname) ;
                        $("#email").val(e.email);
                        EmptyGuest = e.id 
                    })


                }
            })
       
            });
        $('#invited').on('click', '.deleteButtonMini', function() {
            var guestElement = $(this).closest('.guest');
            var delbtn = this
            var email = guestElement.data('email');

            console.log('del button clicked for email:', email);
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
            console.log('earsdb')
            $.ajax({
                url:"../action/publishFormEmail.php",
                type:"POST",
                data:{form: Form},
                success:function(e){
                    console.log(e)
                }
            })
        })
    }

})