$(document).ready(function(){
    
    // event listener for current tab URL
    chrome.extension.onRequest.addListener(function(request, sender, sendResponse) {
          if (request.url != null){
                $('#txtMessage').val('Check out this cool link: '+request.url); // put the URL in the text area
                $('#txtMessage').focus();
          } else if (request.apiKey != '') {
                $('#apiKey').val(request.apiKey); // store API key in hidden field
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

		$.post("http://localhost/CS5774/Project5/processLogin", loginInfo, function (data) {
			if (data) {
				$("#logInError").text(data);
			} else {
//				window.location.href = './community';
                resetLoginPanel(); // reset the login panel
                $('#loginPanel').hide(); // hide the login panel
                $('#sharePanel').show(); // show the share panel
                requestCurrentTabURL();
			}
		});
	});
    
    
    // make the initial request for an API key
    chrome.extension.sendRequest({
       'action': 'getAPIKey' 
    });
    
    // are we already logged in?
    if($('#apiKey').val() == '') {
        // not logged in
           $('#loginPanel').show();
    } else {
        // we have an API key, so already logged in
        $('#sharePanel').show();
        requestCurrentTabURL();
    }
    
    
    // event handler for Log In button
   $('#btnLogIn').click(function(e){
       e.preventDefault(); // don't submit form
        $('#errorMessage').text(''); // clear any error messages
       
       var un = $('#txtUsername').val();
       var pw = $('#txtPassword').val();
       
       $.post(
           'http://localhost/cs5774/testapp/api/login',
           { 'username': un, 'password': pw },
           function(data) {
                if(data.success) {
                    resetLoginPanel(); // reset the login panel
                    $('#loginPanel').hide(); // hide the login panel
                    
                    $('#loggedInUsername').text(data.username); // show username
                    $('#apiKey').val(data.api_key); // save API key in hidden input
                    
                    // now save it to the Chrome extension local storage
                    chrome.extension.sendRequest({
                       'action': 'setAPIKey',
                       'apiKey': data.api_key,
                        'username': data.username
                    });
                    
                    $('#sharePanel').show(); // show the share panel
                    requestCurrentTabURL();

                } else if (data.error) {
                    setErrorMessage("Error: "+data.error);   
                }
           },
           "json"
       );
       
   });
    
   // event handler for Share button 
    $('#btnShare').click(function(e) {
        e.preventDefault(); // don't submit form
        $('#errorMessage').text(''); // clear any error messages
        
       var msg = $('#txtMessage').val();
       var apiKey = $('#apiKey').val();
       
       $.post(
           'http://localhost/cs5774/testapp/api/share',
           { 'api_key': apiKey, 'message': msg },
           function(data) {
                if(data.success) {
                    resetSharePanel(); // reset the share panel
                    setErrorMessage("Success: "+data.success); // success message
                } else if (data.error) {
                    setErrorMessage("Error: "+data.error);   
                }
           },
           "json"
       );
    });
    
    // event handler for Log Out button
    $('#btnLogOut').click(function(e) {
        e.preventDefault(); // don't submit form
        
        // reset everything
        $('#apiKey').val('');
        $('#loggedInUsername').text('');
        chrome.extension.sendRequest({
            'action': 'setAPIKey',
            'apiKey': ''
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
    $('#txtUsername').val('');
    $('#txtPassword').val('');
}

function resetSharePanel() {
    $('#txtMessage').val('');   
}

function setErrorMessage(msg) {
    $('#errorMessage').text(msg); 
}

