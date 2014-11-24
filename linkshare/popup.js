$(document).ready(function(){
    
    // event listener for current tab URL
    chrome.extension.onRequest.addListener(function(request, sender, sendResponse) {
          if (request.url != null){
                $('#txtMessage').val('Check out this cool link: '+request.url); // put the URL in the text area
                $('#txtMessage').focus();
          } else if (request.username != '') {
                $('#loggedInUsername').text(request.username); // show username

                resetLoginPanel(); // reset the login panel
                $('#loginPanel').hide(); // hide the login panel

                $('#sharePanel').show(); // show the share panel
                requestCurrentTabURL();
          }
    });
    
    $("#login").click(function () {
		var username = $("#username").val();
		var password = $("#password").val();
        
		if (!$("#username") || username.length == 0) {
			setErrorMessage("Please enter username!");
			return;
		}

		if (!$("#password") || password.length == 0) {
			setErrorMessage("Please enter password!");
			return;
		}

		var loginInfo = {
			"username": username,
			"password": password
		};

		$.post("http://localhost/CS5774/Project5/processLogin", loginInfo, function (data) {
			if (data) {
				setErrorMessage(data);
			} else {
                resetLoginPanel(); // reset the login panel
                $('#loginPanel').hide(); // hide the login panel
                
                $('#loggedInUsername').text(username); // show username
                
                chrome.extension.sendRequest({
                        'action': 'setUsername',
                        'username': username
                });
                
                $('#sharePanel').show(); // show the share panel
                requestCurrentTabURL();
			}
		});
	});
    
    
    // make the initial request for an API key
    chrome.extension.sendRequest({
       'action': 'getUsername' 
    });
    
    // are we already logged in?
    if($('#loggedInUsername').text() == '') {
        // not logged in
        $('#loginPanel').show();
    } else {
        // we have a valid username, so already logged in
        $('#sharePanel').show();
        requestCurrentTabURL();
    }
    
   // event handler for Share button 
    $('#btnShare').click(function(e) {
        e.preventDefault(); // don't submit form
        $('#errorMessage').text(''); // clear any error messages
        
       var msg = $('#txtMessage').val();
       var username = $('#loggedInUsername').text();
       
       $.post(
           'http://localhost/CS5774/Project5/comment',
           { 	"album_owner": 'schadha',
				"album_name": 'test2',
				"comment": msg
           },
           function(data) {
               if (data) {
                   $('#errorMessage').text("Not Posted");
               } else {
                   $('#errorMessage').css('color', 'green');
                   $('#errorMessage').text("Posted Successfully");
               }
           });
    });
    
    
    // event handler for Log Out button
    $('#btnLogOut').click(function(e) {
        e.preventDefault(); // don't submit form
        
        // reset everything
        $('#loggedInUsername').text('');
        chrome.extension.sendRequest({
            'action': 'setUsername',
            'username': ''
        }); 
        resetSharePanel();
        resetLoginPanel();
        $('#sharePanel').hide();
        $('#loginPanel').show();
    });
});

// ask the background script for the current tab URL
function requestCurrentTabURL() {
    chrome.extension.sendRequest({
       'action': 'getURL' 
    });   
}

function resetLoginPanel() {
    $('#errorMessage').text(''); // clear any error messages
    $('#username').val('');
    $('#password').val('');
}

function resetSharePanel() {
    $('#txtMessage').val('');   
}

function setErrorMessage(msg) {
    $('#errorMessage').text(msg); 
}

