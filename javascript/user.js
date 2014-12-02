$(document).ready(function() {

	 //Press enter to login
	 $("#password").keyup(function(event){
	 	if(event.keyCode == 13){
	 		$("#login").click();
	 	}
	 });

	//Log In functionality
	$("#login").click(function () {
		var username = $("#username").val();
		var password = $("#password").val();

		if (!$("#username") || username.length == 0) {
			$("#logInError").text("Please enter username!");
			return;
		}

		if (!$("#password") || password.length == 0) {
			$("#logInError").text("Please enter password!");
			return;
		}

		var loginInfo = {
			"username": username,
			"password": password
		};

		$.post("./processLogin", loginInfo, function (data) {
			if (data) {
				$("#logInError").text(data);
			} else {
				window.location.href = './community';
			}
		});
	});

	//Create Account functionality
	$(".createAccount").click(function (event) {
		var fName = $(".firstname").val();
		var lName = $(".lastname").val();
		var email = $(".email").val();
		var uName = $(".user").val();
		var pWord = $(".user_password").val();
		var vWord = $(".verifypassword").val();
		var modCode = $("#mod_code").val();
		var favoriteGenre = $("#favoriteGenre").val();

		console.log(favoriteGenre);
		if (!fName || !lName || !email || !uName || !pWord || !vWord || fName.length == 0 ||
			lName.length == 0 || email.length == 0 || uName.length == 0 ||
			pWord.length == 0 || vWord.length == 0 || favoriteGenre == "Select a genre...") {
			
				$("#createAccountError").text("Please fill out the entire form");
			return;
		}

		if (uName == "community") {
			$("#createAccountError").text("Can't have an account with that username!");
			return;
		}

		var accountInfo = {
			"firstname": fName,
			"lastname": lName,
			"email": email,
			"username": uName,
			"password1": pWord,
			"password2": vWord,
			"user_type": 0,
			"favorite_genre": favoriteGenre
		};

		if (modCode.length != 0 && modCode !== 'test') {
			$("#createAccountError").text("Invalid Moderator code");
			return;
		} else if (modCode == 'test') {
			accountInfo['user_type'] = 1;
		}

		$.post("./processRegistration", accountInfo, function (data) {
			if (data) {
				$("#createAccountError").text(data);
			} else {
				$("#createModal").modal('hide');
				window.location.href = './';
			}
		});
	});

	//Sends information about the logged-in user
	$(".profile").click(function() {
		$.post("me", function (data) {
			if (data) {
				curUser = JSON.parse(data);
				$("#firstname").val(curUser["first_name"]);
				$("#lastname").val(curUser["last_name"]);
				$("#email").val(curUser["email"]);
				$("#user").val(curUser["username"]);
                $("#favoriteGenre").val(curUser["favorite_genre"]);
			}
		});		
	});

	//Deletes account from database
	$("#deleteaccountbutton").click(function(e) {
		e.preventDefault();

		$.post("./delete", {
			'delete': $(this).val(),
			'admin': 0
		},
		function (data) {
			if (data) {
				window.location.href = "./";
			}
		});
	});

	//Admin account deletion
	$("#admindeleteaccount").click(function (e) {
		e.preventDefault();

		$.post("./delete", {
			'delete': $(this).val(),
			'admin': 1
		},
		function (data) {
			if (data) {
				window.location.href = "./featured";
			}
		});
	});

	//Updates the account information for the user
	$("#updateaccount").click(function(e) {
		e.preventDefault();
		var fName = $("#firstname").val();
		var lName = $("#lastname").val();
		var email = $("#email").val();
		var user = $("#user").val();
		var pW = $("#user_password").val();
		var verPW = $("#verify_password").val();
        var favoriteGenre = $("#favoriteGenre").val();
        
		if (fName.length == 0 || lName.length == 0 || email.length == 0 || user.length == 0 || favoriteGenre == "Select a genre...") {
			$("#editProfileError").text("All values except Password and Verify Password are mandatory!");
			return;
		}

		if (pW.length > 0 && pW !== verPW) {
			$("#editProfileError").text("Please make sure passwords match before updating!");
			$("#user_password").val("");
			$("#verify_password").val("");
			return;
		} else {
			var updatedInformation = {
				"first_name": fName,
				"last_name": lName,
                "email": email,
                "favorite_genre": favoriteGenre
			};

			if (pW) {
				updatedInformation["password"] = pW;
			}
            
			$.post("./updateUser", updatedInformation, function (data) {
				if (data) {
					$("#editProfileError").text(data);
				} else {
					$("#editModal").modal('hide');
					window.location.href = "./" + user;
				}
			});
		}
	});

	//Logs user off
	$("#logoff").click(function() {
		window.location.href='./index.html';
	});

	//Send a collaborate request to a user
	$("#collaborate").click(function (e) {
		var path = location.pathname.split("/");
		var collabWith = path[path.length - 1];

		$.post("./collaborate", {
			"collaborator": collabWith
		}, function (data) {
			location.reload();
		});
	});

	//Accepts request from the user's profile page
	$("#accept").click(function (e) {
		var path = location.pathname.split("/");
		var collabWith = path[path.length - 1];

		$.post("./collaborate", {
			"collaborator": collabWith
		}, function (data) {
			location.reload();
		});
	});

	//Uncollaborate with a user
	$("#uncollaborate").click(function (e) {
		var path = location.pathname.split("/");
		var collabWith = path[path.length - 1];

		$.post("./uncollaborate", {
			"collaborator": collabWith
		}, function (data) {
			location.reload();
		});
	});

	//Accepts a collaboration request
	$(".accept_request").click(function (e) {
		var userCollab = $(this).val();

		$.post("./collaborate", {
			"collaborator": userCollab
		}, function (data) {
			location.reload();
		});
	});

	//Denies collaboration request
	$(".deny_request").click(function (e) {
		var cancelCollab = $(this).val();

		$.post("./uncollaborate", {
			"collaborator": cancelCollab
		}, function (data) {
			location.reload();
		});
	});

	//promotes regular user to moderator
	$("#admin_promote").click(function (e) {
		var promoteUser = $(this).val();

		$.post("./promote", {
			"promote": promoteUser
		}, function (data) {
			location.reload();
		});
	});

	//demotes moderator to regular user
	$("#admin_demote").click(function (e) {
		var demoteUser = $(this).val();

		$.post("./demote", {
			"demote": demoteUser
		}, function (data) {
			location.reload();
		});
	});

	//Sorts the list view by genre
	$("#genre_select").change(function() {
		var selected =  $(this).children(":selected").text();

		if (window.location.href.indexOf("community/") > -1) {
			window.location.href = encodeURIComponent(selected);
		}
		else {
			window.location.href = "./community/" + encodeURIComponent(selected);
		}
	});

});