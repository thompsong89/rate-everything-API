$(document).ready(function () {
	$.getJSON("http://re.app/rest/entityList", function(data){
      var options = '';
      for (var i = 0; i < data.length; i++) {
        options += '<option value="' + data[i].entityId + '">' + data[i].name + '</option>';
      }
      $("select#entitylist").html(options);
    });
	
	$('#up-arrow').click(function(){
		
	});
});

