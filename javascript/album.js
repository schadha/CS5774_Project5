$(document).ready(function() {
	//Creates a new album for the user
	$("#createalbum").click(function(e) {
		e.preventDefault();
		var albumName = $("#album_name").val();
		var albumGenre = $("#album_genre").val();
		var albumSummary = $("#album_summary").val();
		var albumImage = $("#album_image").val();
		var location = window.location.pathname.split("/");
		var owner = location[location.length - 1];

		if (albumName.length == 0 || albumGenre.length == 0 || albumSummary.length == 0 || albumImage.length == 0) {
			$("#createAlbumError").text("Please enter information for all the fields!");
			return;
		}

		$("#albumForm").submit(function(e) {
			var formObj = $(this);
			var formData = new FormData(this);

			$.ajax({
				url: "./album/create",
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
						$("#createAlbumError").text(data.Error);
					} else {
						$("#trackModal").modal("hide");
						reloadPage();
					}
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					$("#createAlbumError").text(jqXHR);
				}          
			});
			e.preventDefault(); 
		});
		$("#albumForm").submit();
	});

	//Gets and populates the album information in the modal
	$("#editAlbum").click(function(e) {
		e.preventDefault();
		var location = window.location.pathname.split("/");
		
		$.post("./album/get", {
			"album_owner": location[location.length - 2],
			"album_name": decodeURI(location[location.length - 1])
		}, function (data) {
			var album = JSON.parse(data);
			var albumName = $("#edit_album_name").val(album['album_name']);
			var albumGenre = $("#edit_album_genre").val(album['album_genre']);
			var albumSummary = $("#edit_album_summary").val(album['album_summary']);
		});
	});

	//Submits modified information for the album
	$("#editalbumbutton").click(function(e) {
		if ($("#edit_album_genre").val().length > 0 && $("#edit_album_summary").val().length > 0) {
			$("#albumFormEdit").submit(function(e) {

				var formObj = $(this);
				var formData = new FormData(this);
				
				$.ajax({
					url: "./album/update",
					type: 'POST',
					data:  formData,
					mimeType:"multipart/form-data",
					contentType: false,
					cache: false,
					processData:false,
					success: function(data, textStatus, jqXHR)
					{
						var data = JSON.parse(data);

						if (data["Error"]) {
							$("#albumEditError").text(data["Error"]);
						} else {
							$("#editAlbumModal").modal("hide");
							reloadPage();
						}
					}
				});
				e.preventDefault(); 
			});
			$("#albumFormEdit").submit();

		} else {
			$("#albumEditError").text("Please enter a value for the fields!");
			return;
		}
	});

	//Deletes the album from the database
	$("#deletealbumbutton").click(function(e) {
		e.preventDefault();
		var location = window.location.pathname.split("/");
		var album_owner = location[location.length - 2];
		var album_name = decodeURI(location[location.length - 1]);

		$.post("./album/delete", {
			"album_owner": album_owner,
			"album_name": album_name
		}, function (data) {
			if (data) {
				$("#albumDeleteError").text(data);
				return;
			} else {
				window.location.href = '../' + album_owner;
			}
		});
	});

	//Reloads page, here because I used location as a local variable a lot
	function reloadPage() {
		location.reload();
	}
});