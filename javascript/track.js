$(document).ready(function() {
	//Reveals and populates track modal
	$("#trackModalButton").click(function() {
		var location = window.location.pathname.split("/");
		$("#track_album").val(decodeURI(location[location.length - 1]));
		$("#album_owner").val(decodeURI(location[location.length - 2]));
		$("#track_name").val("");
		$("#track_data").val("");
	});

	//Creates a new track for that album
	$("#createtrack").click(function(e) {
		e.preventDefault();
		var track_name = $("#track_name").val();
		var track_path = $("#track_data").val();

		if (track_name.length == 0 || !track_path) {
			$("#trackCreateError").text("Please fill the entire form!");
			return;
		} else {
			$("#trackForm").submit(function(e) {
				var formData = new FormData(this);

				$.ajax({
					url: "../track/create",
					type: 'POST',
					data:  formData,
					mimeType:"multipart/form-data",
					contentType: false,
					cache: false,
					processData:false,
					success: function(data, textStatus, jqXHR)
					{
						console.log(data);
						var data = JSON.parse(data);
						if (data.Error) {
							$("#trackCreateError").text(data.Error);
						} else {
							$("#trackModal").modal("hide");
							reloadPage();
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						$("#trackCreateError").text(jqXHR);
					}          
				});
				e.preventDefault(); 
			});
			$("#trackForm").submit();
		}
	});

	//Reveals and populates the delete track modal	
	$(".deletetrack").click(function (e) {
		e.preventDefault();
		var data = $(this).val();
		$("#deletetrackbutton").val(data);
		$("#deleteTrackLabel").text("Are you sure you want to delete the track \""+data+"\"?");
	});

	//Deletes track for that album
	$("#deletetrackbutton").click(function(e) {
		e.preventDefault();
		var location = window.location.pathname.split("/");

		var trackName = $(this).val();
		var trackAlbum = decodeURI(location[location.length - 1]);
		var albumOwner = decodeURI(location[location.length - 2]);

		$.post("../track/delete", {
			"track_name": trackName,
			"track_album": trackAlbum,
			"album_owner": albumOwner
		}, function (data) {
			console.log(data);
			var data = JSON.parse(data);
			if (data.Error) {trackDeleteError
				$("#trackDeleteError").text(data.Error);
			}
			else {
				reloadPage();
			}
		});
	});

	//Reveals and populates the edit track modal	
	$(".edittrack").click(function (e) {
		e.preventDefault();
		var data = $(this).val();
		$("#old_track").val(data);
	});

	//Used to change the name of a track
	$("#track_edit_submit").click(function (e) {
		e.preventDefault();
		var location = window.location.pathname.split("/");
		var trackAlbum = decodeURI(location[location.length - 1]);
		var albumOwner = decodeURI(location[location.length - 2]);
		var trackName = $("#new_track").val();
		var oldTrackName = $("#old_track").val();

		if ($("#new_track").val().length == 0) {
			$("#trackEditError").text("Please enter a valid new track name!");
			return;
		}
		$.post("../track/edit", {
			"track_name": trackName,
			"track_album": trackAlbum,
			"old_track_name": oldTrackName,
			"album_owner": albumOwner
		}, function (data) {
			console.log(data);
			var data = JSON.parse(data);

			if (data.Error) {
				$("#trackEditError").text(data.Error);
			} else {
				reloadPage();
			}
		});		
	});

	//Downloads the specified track
	$(".downloadtrack").click(function(e) {
		e.preventDefault();
		var path = $(this).val().split("/");
		window.location.href = "../track/download/"+path[path.length - 1];
	});

	//Reloads page, here because I used location as a local variable a lot
	function reloadPage() {
		location.reload();
	}
	
});