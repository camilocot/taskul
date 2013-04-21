$(document).ready(function(){
  var generateNotification = true;
  var notificationUpdate = function(){
    route = Routing.generate('notification', { "context": "TASK" });
    $.ajax({
      method:'get',
      url: route,
      success:function(data){
        console.log(data);
        if(data.success)
          $("#notification-number").text(data.total);
      }
    });
  }
  $('#task_notification').click(function() {
        // Only call notifications when opening the dropdown
        if(generateNotification && !$(this).parent('li').hasClass('open')) {
          route = Routing.generate('get_notification', { "context": "TASK" });
           $.ajax({
              type: "GET",
              url: route,
              async: false,
              dataType: "json",
              success: function(data) {
                $('#task-notification').mustache('notification-html', data);
                generateNotification = false;
              }
           });
        }
  });
  $.Mustache.add('notification-html', $('#notification-html').html());
  setInterval(notificationUpdate,50000);
  notificationUpdate();
});