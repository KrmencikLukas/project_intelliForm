function appendParamsToUrl(params) {

    var currentUrl = window.location.href;
    var url = new URL(currentUrl);

    for (var key in params) {
        if (params.hasOwnProperty(key)) {
            url.searchParams.set(key, params[key]);
        }
    }
    window.history.replaceState({}, '', url.href);
}
function timeAgo2(timestamp, identifier, keyword) {
    const currentDate = new Date();
    const elements = document.querySelectorAll(identifier);
  
    for (let i = 0; i < elements.length; i++) {
      const targetDate = new Date(timestamp[i]);
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
  var originalHtml = $("#forms").html();
  var isSearchInputEmpty = true;

  $("#more").on("click", function(){
    var count = $("#forms .form").length;
    var DBcount = JSON.parse(countForm);
    var userId = JSON.parse(user);
    if(userId){
      if(count !== DBcount){
        var params = {
          user: userId,
          offset:count, 
        }
        appendParamsToUrl(params)
        $.ajax({
          url: "../action/returnForms.php",
          type: "POST",
          data: { userID: userId, count: count},
          success:function(response){
            let data = JSON.parse(response)
            console.log(data)
            data.forEach(e => {
            $("#forms").append(`                        
            <a href='../editor.php?id=${e.id}' target='_self'>
              <div class='form'>
                <h2>${e.name}</h2>
                <div class='date'></div>
                <div class='actions'>
                    <p><span class='mdi mdi-earth'></span></p>
                    <p><span class='mdi mdi-delete del'></span></p>
                </div>
              </div>
            </a>`
            );
                const identifier = '.form:last-child .date';
                timeAgo2([e.timestamp], identifier, "Last edited")
            });
            count = $("#forms .form").length;
            DBcount = JSON.parse(countForm);
            params = {
                user: userId,
                offset:count, 
            }
            if(count === DBcount){
                $("#more").css("display", "none");
            }
            appendParamsToUrl(params);
          }
        })
      }
    }
  })
  $("#SearchForm").on("input", function(){
    var Search = $(this).val();
    var userId = JSON.parse(user);

    if(userId && Search) {
      isSearchInputEmpty = false;

      $.ajax({
        url: "../action/searchForForms.php",
        type: "POST",
        data:{userID: userId, search: Search},
        success: function(response){
          let searchData = JSON.parse(response);
          $("#forms").empty();
          $("#more").css("display", "none");

          if (searchData.length > 0) {
            searchData.forEach(e => {
              $("#forms").append(`                        
              <a href='../editor.php?id=${e.id}' target='_self'>
                <div class='form'>
                  <h2>${e.name}</h2>
                  <div class='date'></div>
                  <div class='actions'>
                      <p><span class='mdi mdi-earth'></span></p>
                      <p><span class='mdi mdi-delete del'></span></p>
                  </div>
              </div>
            </a>`
          );
              const identifier = '.form:last-child .date';
              timeAgo2([e.timestamp], identifier, "Last edited");
            });
          } else if(searchData.length === 0 && Search !== ""){
            $("#forms").html("<p id='NoSearch'>No search results</p>");
          }
        }
      });
    }

    isSearchInputEmpty = Search === "";

    if(isSearchInputEmpty){
      $("#forms").html(originalHtml);
      $("#more").css("display", "flex");
    }
  });

})