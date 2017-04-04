$(document).ready(function () {
	$('#username').val($.cookie("username"));
	$.getJSON("http://" + $(location).attr('hostname') + "/rest/entityList", function (data) {
		var options = '<option value="">Please Select</option>';
		for (var i = 0; i < data.entities.length; i++) {
			options += '<option value="' + data.entities[i].entityId + '">' + data.entities[i].name + '</option>';
		}
		$("select#entitylist").html(options);
	});

	$('#entitylist').change(function () {
		if ($("#ratiorating").length) {
			$.getJSON("http://" + $(location).attr('hostname') + "/rest/entity/"+$(this).val(), function (data) {
				$("#ratiorating").text(data.entity[0].temp_rating);
				$("#description").text(data.entity[0].description);
			});
			
		}
	});

	$('#up-arrow').click(function () {
		addEntityRating(1);
	});

	$('#circle').click(function () {
		addEntityRating(0);
	});

	$('#down-arrow').click(function () {
		addEntityRating(-1);
	});

	function addEntityRating(rate) {
		if ($('#username').val() != "" && $('#entitylist').val() != "") {
			$.cookie("username", $('#username').val());
			$.post("http://" + $(location).attr('hostname') + "/rest/addEntityRating", {
				entityId: $('#entitylist').val(),
				rating: rate,
				username: $('#username').val()
			}, function (data) {
				status = data.status.toLowerCase().replace(/\b[a-z]/g, function (letter) {
					return letter.toUpperCase();
				});
			});
		}
		if ($('#username').val() == "") {
			alert('Must provide username');
		}
		if ($('#entitylist').val() == "") {
			alert('Must select a restaurant');
		}
	}
});

