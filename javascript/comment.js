$(document).ready(function() {
	//Adds comment to page
	$("#comment").click(function (e) {
		e.preventDefault();
		var commentText = $("#comment_area").val();

		if (commentText.length > 0) {
			var path = location.pathname.split("/");
			var albumOwner = path[path.length - 2];
			var albumName = path[path.length - 1];
            
			$.post("../comment", {
				"album_owner": albumOwner,
				"album_name": albumName,
				"comment": commentText
			}, function (data) {
				location.reload();
			});
		}
	});

	//Delete comments from page
	$(".delete_comment").click(function (e) {
		var idToDelete = $(this).val().split("_")[2];
		var commentOwner = $(this).val().split("_")[3];

		$("#" + $(this).val()).remove();
		$(this).remove();
		

		$.post("../comment/delete", {
			id: idToDelete,
			commenter: commentOwner
		}, function (data) {
		    location.reload();
        });
	});
});