//Project URL
var URL = 'http://localhost/CS5774/Project5/';

$(document).ready(function() {

    // make the initial request for a username
    chrome.extension.sendRequest({
        'action': 'getUsername' 
    });

    // are we already logged in?
    if($('#loggedInUsername').text() == '') {
        // not logged in
        $('#loginPanel').show();
        $('#navPanel').hide();
    }

    // event listener for current tab URL
    chrome.extension.onRequest.addListener(function(request, sender, sendResponse) {
        if (request.url != null) {
            //Check if the url is of a YouTube video
            if (request.url.indexOf("youtube.com/watch") > -1 && request.url.indexOf("youtubeinmp3") == -1) {
                $('#btnDownload').val(request.url); //set the value of the button to the video URL
                $('#btnDownload').prop('disabled', false); //Enable the button
            }
        } else if (request.username) {
            initializePage(request.username); //Initialize the main page after opening the extension
        }
    });

    //Press enter to login
    $("#password").keyup(function(event){
        if(event.keyCode == 13){
            $("#login").click();
        }
    });

    //Handles logging in
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

        $.post(URL + "extensionProcessLogin", loginInfo, function (data) {
            if (data) {
                setErrorMessage(data);
            } else {
                chrome.extension.sendRequest({
                    'action': 'setUsername',
                    'username': username
                });

                initializePage(username); //Initialize the main page after logging in
            }
        });
    });

    //Accepts a collaboration request
    $(document).on('click', '.accept_request', function (e) {
        var userCollab = $(this).val();

        $.post(URL + "extensionCollaborate", {
            "username": $('#loggedInUsername').text(),
            "collaborator": userCollab
        }, function (data) {
            location.reload();
        });
    });

    //Denies collaboration request
    $(document).on('click', '.deny_request', function (e) {
        var cancelCollab = $(this).val();

        $.post(URL + "extensionUncollaborate", {
            "username": $('#loggedInUsername').text(),
            "collaborator": cancelCollab
        }, function (data) {
            location.reload();
        });
    });

    //Efficient tab changing function.
    var lastTab = 0;
    $(".tab").click(function() {
        var num = this.id.match(/\d+/)[0];

        if (lastTab != num) {
            lastTab = num;
            resetInputs(); //Removes all error messages

        }

        $("#tab_" + num).tab('show'); //Highlights the clicked tab
        $(".panel").hide(); //Hides all panels
        $("#panel_" + num).show(); //Shows the panel associated with the tab

        if (num == 4) {
            getTracks("edit"); //Tracks for edit panel
        } else if (num == 5) {
            getTracks("delete"); //Tracks for delete panel
        }

    });

    //Handles logging out
    $('#logoutTab a').click(function (e) {
        e.preventDefault();

        // reset everything
        $('#loggedInUsername').text('');
        chrome.extension.sendRequest({
            'action': 'setUsername',
            'username': ''
        }); 
        resetInputs();

        for (var i = 1; i <= 5; i++) {
            $('#panel_'+i).hide(); //Hides all panels
        }

        $('#navPanel').hide();
        chrome.browserAction.setBadgeText({text:""}); //Resets the extension badge
        $('#loginPanel').show(); //Shows the login panel

    });

    //Creates a new album for the user
    $("#createalbum").click(function(e) {
        e.preventDefault();
        var albumName = $("#album_name").val();
        var albumGenre = $("#album_genre").val();
        var albumSummary = $("#album_summary").val();
        var albumImage = $("#album_image").val();
        var username = $('#loggedInUsername').text();


        if (albumName.length == 0 || albumGenre.length == 0 || albumSummary.length == 0 || albumImage.length == 0) {
            setErrorMessage("Please enter information for all the fields!");
            return;
        }

        $("#albumForm").submit(function(e) {
            var formObj = $(this);
            var formData = new FormData(this);
            formData.append("username", username);

            $.ajax({
                url: URL + "extensionCreateAlbum",
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
                        setErrorMessage(data.Error);
                    } else {
                        $('#errorMessage').css('color', 'green');
                        setErrorMessage("Album Created Successfully");
                        getAlbums(username); //Fetches new album list
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) 
                {
                    setErrorMessage(jqXHR);
                }          
            });
            e.preventDefault(); 
        });
        $("#albumForm").submit();
    });

    //Creates a new track for an album
    $("#createtrack").click(function(e) {
        e.preventDefault();
        var track_album = $('#track_album1 option:selected').text(); //Album name selected from a dropdown menu
        var track_path = $("#track_data").val();

        if (track_name.length == 0 || !track_path) {
            setErrorMessage("Please fill the entire form!");
            return;
        } else {
            $("#trackForm").submit(function(e) {
                var formData = new FormData(this);
                formData.append("track_album", track_album);

                $.ajax({
                    url: URL + "extensionCreateTrack",
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
                            setErrorMessage(data.Error);
                        } else {
                            $('#errorMessage').css('color', 'green');
                            setErrorMessage("Track Uploaded Successfully");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) 
                    {
                        setErrorMessage(jqXHR);
                    }          
                });
                e.preventDefault(); 
            });

            $("#trackForm").submit();
        }
    });

    //Edits a track for an album
    $('#track_edit_submit').click(function (e) {
        e.preventDefault();

        var trackAlbum = $('#track_album2 option:selected').text(); //Album selected from the dropdown menu
        var albumOwner = $('#loggedInUsername').text();
        var trackName = $("#new_track").val();
        var oldTrackName = $('#track_name_select1 option:selected').text(); //Track name selected from the dropdown menu

        if ($("#new_track").val().length == 0) {
            setErrorMessage("Please enter a valid new track name!");
            return;
        }

        $.post(URL + "track/edit", {
            "track_name": trackName,
            "track_album": trackAlbum,
            "old_track_name": oldTrackName,
            "album_owner": albumOwner
        }, function (data) {
            console.log(data);
            var data = JSON.parse(data);

            if (data.Error) {
                setErrorMessage(data.Error);
            } else {
                $('#errorMessage').css('color', 'green');
                setErrorMessage("Edited Successfully");
                $('#new_track').val('');
                $('#tab_4 a').click(); //Refresh page to show track name change
            }
        });		
    });

    //Delete a track for an album
    $('#track_delete_submit').click(function (e) {
        e.preventDefault();

        var trackAlbum = $('#track_album3 option:selected').text();
        var albumOwner = $('#loggedInUsername').text();
        var trackName = $("#track_name_select2 option:selected").text();

        $.post(URL + "track/delete", {
            "track_name": trackName,
            "track_album": trackAlbum,
            "album_owner": albumOwner
        }, function (data) {
            console.log(data);
            var data = JSON.parse(data);

            if (data.Error) {
                setErrorMessage(data.Error);
            }
            else {
                $('#errorMessage').css('color', 'green');
                setErrorMessage("Deleted Successfully");
                $('#tab_5 a').click(); //Refresh page to show track has been deleted successfully
            }
        });
    });

    // If album selection is changed, fetch tracks associated with that album
    $('#track_album2').on('change', function() {
        getTracks("edit");
    });

    // If album selection is changed, fetch tracks associated with that album
    $('#track_album3').on('change', function() {
        getTracks("delete");
    });

    //Handle downloading/converting the YouTube video based on the current URL
    $('#btnDownload').click(function(e) {
        e.preventDefault(); // don't submit form
        setErrorMessage(''); // clear any error messages

        var url = $('#btnDownload').val();

        //Download the file with the given URL
        chrome.downloads.download({url: 'http://youtubeinmp3.com/fetch/?video=' + url}, function(id) {
            chrome.downloads.search({id: id}, function(itemArr) {
                var item = itemArr[0];
                //If file type is of html, that means the video hasn't been converted on youtubeinmp3's database.
                if (item.mime.indexOf("html") > -1) { 
                    chrome.downloads.cancel(item.id); //Cancel the download
                    //Change tab to the conversion page
                    chrome.tabs.update(null, {url: 'http://youtubeinmp3.com/download/?video=' + url}, null);
                    //Show an alert to redirect the user to the conversion website
                    alert("Convert video to MP3 using this website.");
                }
            });
        });
    });
});

//Initialize the main page after logging in or opening the extension
function initializePage(username) {
    resetInputs(); // reset the login panel
    $('#loginPanel').hide(); // hide the login panel

    checkCollaboratorRequests(username); //Update collaborator requests check

    $('#loggedInUsername').text(username); // show username

    $("#homePanel").show(); //Show the home panel
    $('#navPanel').show(); //Show the nav bar
    $('#tab_1 a').click(); //Make sure the first tab is clicked

    $('#album_owner').val(username); //Set the album owner to the logged in user

    requestCurrentTabURL(); //Get the URL of the current page to check for YouTube video

    getAlbums(username); //Fetch the albums associated with the user
}


// Ask the background script for the current tab URL
function requestCurrentTabURL() {
    chrome.extension.sendRequest({
        'action': 'getURL' 
    });   
}

//Fetch the albums and create dropdown menu options for album selection
function getAlbums(username) {
    $.post(URL + "extensionGetAlbums", {"album_owner": username}, function (data) {
        var jsonData = JSON.parse(data);
        createAlbumOptions(jsonData);
    });
}

//Create all dropdown menu options for album selection
function createAlbumOptions( jsonData ) {
    $('.album_dropdown option').remove(); // first remove all options

    for (var fieldIndex in jsonData) { // then populate them
        $('.album_dropdown').append($("<option></option>").attr("value", fieldIndex).text(jsonData[fieldIndex].album_name));
    }
}

//Fetch the tracks for the selected album and create dropdown menu options for track selection
function getTracks(panel) {
    var album_name = "";

    if (panel == "edit") {
        album_name = $('#track_album2 option:selected').text();
    } else {
        album_name = $('#track_album3 option:selected').text();   
    }

    var album_owner = $('#loggedInUsername').text();

    $.post(URL + "extensionGetTracks", {
        "album_name": album_name,
        "album_owner": album_owner
    }, function (data) {
        var jsonData = JSON.parse(data);
        createTrackOptions(jsonData, panel);
    });
}

//Create all dropdown menu options for track selection
function createTrackOptions(jsonData, panel) {
    $('.track_dropdown option').remove(); // first remove all options

    if (!jsonData) { //If there are no tracks, say so in the dropdown menu
        $('.track_dropdown').append($("<option></option>").text("No Tracks in this Album"));
        $('.track_dropdown').attr('disabled', 'disabled');

        //Disable different elements when in different panels.
        if (panel == "edit") {
            $('#new_track').attr('disabled', 'disabled');
            $('#track_edit_submit').attr('disabled', 'disabled');
        } else {
            $('#track_delete_submit').attr('disabled', 'disabled');
        }
    } else {
        $('.track_dropdown').removeAttr('disabled');
        //Enable different elements when in different panels.
        if (panel == "edit") {    
            $('#new_track').removeAttr('disabled');
            $('#track_edit_submit').removeAttr('disabled');
        } else {
            $('#track_delete_submit').removeAttr('disabled');
        }
        for (var fieldIndex in jsonData) { // then populate them
            $('.track_dropdown').append($("<option></option>").attr("value", fieldIndex).text(jsonData[fieldIndex].track_name));
        }
    }
}

//Check if there are pending collaborator requests
function checkCollaboratorRequests(username) {
    $('#collabPanel').hide();
    $('#collabs').empty(); //Reset and empty the collaboration panel

    var counter = 0; //Counter for extension badge

    $.post(URL + "extensionGetCollaborators", {"user": username}, function (data) {
        var jsonData = JSON.parse(data);

        for (var fieldIndex in jsonData) { // then populate them
            var obj = jsonData[fieldIndex];

            if (obj['sent_by'] !== username) { //Show only if the request is not sent by logged in user
                $('#collabPanel').show();

                var date = new Date(obj['modified']);
                var dateString = $.datepicker.formatDate('M dd', date) + " at " + date.toLocaleTimeString();

                $('#collabs').append($("<h4></h4>").text(obj['sent_by'] +' - Request sent on: ' + dateString));
                $('#collabs').append($("<button></button>").attr('class','btn btn-success accept_request').attr('value', obj['sent_by']).text("Accept"));
                $('#collabs').append($("<button></button>").attr('class','btn btn-danger deny_request').attr('value', obj['sent_by']).text("Deny"));

                counter+=1; //Increment badge counter
            }
        }


        if (counter == 0) { //Show blank badge when no notifications
            chrome.browserAction.setBadgeText({text:""});
        } else { //Set the extension badge if there are notifications
            chrome.browserAction.setBadgeText({text:""+counter});
        }
    });
}

//Resets all error messages and input fields
function resetInputs() {
    setErrorMessage(''); // clear any error messages
    $('.form-control').not("#album_owner").val(''); // clear any input form sections (except album_owner)
    $('select option:first-child').attr("selected", "selected"); //Resets dropdown selection
}

//Sets the error message with the given message
function setErrorMessage(msg) {
    $('#errorMessage').text(msg);
}

