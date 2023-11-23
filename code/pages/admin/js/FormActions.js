
function timeAgo2(timestamps, identifier, keyword) {
  const currentDate = new Date();
  const elements = document.querySelectorAll(identifier);

  for (let i = 0; i < elements.length; i++) {
    const targetDate = new Date(timestamps[i]);
    const timeDifference = currentDate - targetDate;

    const seconds = Math.floor(timeDifference / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    const months = Math.floor(days / 30.44);
    const years = Math.floor(months / 12);

    let result;

    if (years > 0) {
      result = ` ${keyword} ${years} year${years > 1 ? 's' : ''} ago`;
    } else if (months > 0) {
      result = `${keyword} ${months} month${months > 1 ? 's' : ''} ago`;
    } else if (days > 0) {
      result = `${keyword} ${days} day${days > 1 ? 's' : ''} ago`;
    } else if (hours > 0) {
      result = `${keyword} ${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else if (minutes > 0) {
      result = `${keyword} ${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else {
      result = `A few seconds ago`;
    }

    elements[i].innerHTML = result;
  }
}

$(document).ready(function(){
  $("#forms").on("click", ".mdi-earth", function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var anchorId = $(this).closest('a').attr('href').match(/(?:[?&])id=([^&]+)/);
    var idValue = anchorId ? anchorId[1] : null;

    console.log('Extracted id:', idValue);
});

$("#forms").on("click", ".mdi-earth-plus", function(e) {
  e.preventDefault();
  e.stopPropagation();
  
  var anchorId = $(this).closest('a').attr('href').match(/(?:[?&])id=([^&]+)/);
  var idValue = anchorId ? anchorId[1] : null;

  console.log('Extracted id:', idValue);
});

  $("#forms").on("click", ".bar", function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var anchorId = $(this).closest('a').attr('href').match(/(?:[?&])id=([^&]+)/);
    var idValue = anchorId ? anchorId[1] : null;

    console.log('Extracted id:', idValue);
  })
  $("#forms").on("click",".del", function(e){        
    e.preventDefault()
 
    var anchorId = $(this).closest('a').attr('href').match(/(?:[?&])id=([^&]+)/);
    var idValue = anchorId ? anchorId[1] : null;

    var DBcount = JSON.parse(countForm);
    var userId = JSON.parse(user);
    var count = $("#forms .form").length;
    
    $.ajax({
      url:"../action/deleteForm.php",
      type:"POST",
      data:{id: idValue},
      success:() =>{
        e.stopPropagation()   
        $(this).closest('a').empty();
        $(this).closest('a').remove();

        if(count == 4 && DBcount > 4){
          if(userId){
            $.ajax({
              url: "../action/returnFormsDel.php",
              type: "POST",
              data: { userID: userId, count: 4},
              success:function(response){

                $("#forms").empty()
                var data = JSON.parse(response)

                data.forEach(e => {
                  $("#forms").append(`                        
                  <a href='../editor.php?id=${e.id}' target='_self'>
                    <div class='form'>
                      <h2>${e.name}</h2>
                      <div class='date'></div>
                      <div class='actions'>
                        ${e.public === 1 ? "<p><span class='mdi mdi-chart-bar bar'></span></p><p><span class='mdi mdi-earth-plus'></span></p>" : "<p><span class='mdi mdi-earth'></span></p>"}
                        <p><span class='mdi mdi-delete del'></span></p>
                      </div>
                      </div>
                    </a>`
                  );
                  const timestamps = data.map(e => e.timestamp);
                  const identifier = '.form .date';
                  timeAgo2(timestamps, identifier, "Last edited");
                  
                }); 
              }
            })
          }
        }
        }
    })
  })
})