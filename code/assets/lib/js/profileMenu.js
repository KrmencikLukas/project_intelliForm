function setMenuPosition(Menu, CenterDiv, container) {
    var fixedContainer = $(container);
    var otherDiv = $(CenterDiv);
    var menu = $(Menu);
  
    var containerOffset = fixedContainer.offset();
    var otherDivOffset = otherDiv.offset();

 
    var centerX = otherDivOffset.left + otherDiv.width() / 2;

    menu.css({
        left: centerX - menu.width() / 2,
    });

  }

$(document).ready(function(){
    var CurrentUserId = JSON.parse(user);
    var UserPic = $(".UserIcon").attr("src")
    if(CurrentUserId){
      $(".UserIcon").on("click", function(){
        $.ajax({
          url: "../../../assets/global/profileMenu.php",
          type: "POST",
          data: {userID:  CurrentUserId},
          success: function(resp){
            data = JSON.parse(resp)
            $("#profile h2").html(data.name +"&nbsp;&nbsp;" + data.surname);
            $("#profile p").html(data.email);
          }
        })
        $("#Preview").removeClass("hidden");
        $(".UserIcon").addClass("hov")
        $("#pfPic").attr("src", UserPic)
        $("#backgroundOverlay").removeClass("hidden");
        $("#backgroundOverlay, #close, #close p, #Preview").on("click", function(e) {
            if (e.target === this) {
              $("#backgroundOverlay").addClass("hidden");
              $(".UserIcon").removeClass("hov")
            }
          });
      })
    }
    
    setMenuPosition(".preview-container",".UserIcon","#backgroundOverlay");


    $(window).resize(function () {
        setMenuPosition(".preview-container",".UserIcon","#backgroundOverlay");
    });
     
  })