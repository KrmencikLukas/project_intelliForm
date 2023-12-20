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
  $(".UserIcon").on("click", function(){
    $("#Preview").removeClass("hidden");
    $(".UserIcon").addClass("hov")
    $("#backgroundOverlay").removeClass("hidden");
    $("#backgroundOverlay, #close, .mdi-close-thick, #Preview").on("click", function(e) {
        if (e.target === this) {
          $("#backgroundOverlay").addClass("hidden");
          $(".UserIcon").removeClass("hov")
        }
      });
  })
    
    
    setMenuPosition(".preview-container",".UserIcon","#backgroundOverlay");


    $(window).resize(function () {
        setMenuPosition(".preview-container",".UserIcon","#backgroundOverlay");
    });
     
  })