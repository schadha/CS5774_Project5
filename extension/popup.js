var URL = 'http://localhost/CS5774/Project5/';

$(document).ready(function() {

    // make the initial request for an API key
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
            if (request.url.indexOf("youtube.com/watch") > -1 && request.url.indexOf("youtubeinmp3") == -1) {
                $('#btnDownload').val(request.url);
                $('#btnDownload').prop('disabled', false);
            }
        } else if (request.username) {
            initializePage(request.username);
            checkCollaboratorRequests(request.username);
        }
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

    $('#homeTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        $("#homePanel").show();
        $("#commentPanel").hide();
        $("#editTrackPanel").hide();
        $("#uploadTrackPanel").hide();
        $("#deleteTrackPanel").hide();
    });

    $('#albumTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        $("#homePanel").hide();
        $("#commentPanel").show();
        $("#editTrackPanel").hide();
        $("#uploadTrackPanel").hide();
        $("#deleteTrackPanel").hide();
    });

    $('#editTrack a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        $("#homePanel").hide();
        $("#commentPanel").hide();
        $("#editTrackPanel").show();
        $("#uploadTrackPanel").hide();
        $("#deleteTrackPanel").hide();
        getTracks("edit");
    });

    $('#uploadTrack a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        $("#homePanel").hide();
        $("#commentPanel").hide();
        $("#editTrackPanel").hide();
        $("#uploadTrackPanel").show();
        $("#deleteTrackPanel").hide();
    });


    $('#deleteTrack a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        $("#homePanel").hide();
        $("#commentPanel").hide();
        $("#editTrackPanel").hide();
        $("#uploadTrackPanel").hide();
        $("#deleteTrackPanel").show();
        getTracks("delete");
    });

    //Creates a new track for that album
    $("#createtrack").click(function(e) {
        e.preventDefault();
        var track_album = $('#track_album2 option:selected').text();
        var track_path = $("#track_data").val();

        if (track_name.length == 0 || !track_path) {
            setErrorMessage("Please fill the entire form!");
            return;
        } else {
            $("#trackForm").submit(function(e) {
                var formData = new FormData(this);
                formData.append("track_album", track_album);

                $.ajax({
                    url: URL + "track/create",
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

    $('#track_edit_submit').click(function (e) {
        e.preventDefault();

        var trackAlbum = $('#track_album3 option:selected').text();
        var albumOwner = $('#loggedInUsername').text();
        var trackName = $("#new_track").val();
        var oldTrackName = $('#track_name_select1 option:selected').text();

        if ($("#new_track").val().length == 0) {
            $("#trackEditError").text("Please enter a valid new track name!");
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
                $("#trackEditError").text(data.Error);
            } else {
                $('#errorMessage').css('color', 'green');
                setErrorMessage("Edited Successfully");
                $('#new_track').val('');
                $('#editTrack a').click();
            }
        });		
    });

    $('#track_delete_submit').click(function (e) {
        e.preventDefault();

        var trackAlbum = $('#track_album4 option:selected').text();
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
                $("#trackDeleteError").text(data.Error);
            }
            else {
                $('#errorMessage').css('color', 'green');
                setErrorMessage("Deleted Successfully");
                $('#deleteTrack a').click();
            }
        });
    });

    $('#track_album3').on('change', function() {
        getTracks("edit");
    });

    $('#track_album4').on('change', function() {
        getTracks("delete");
    });

    $('#logoutTab a').click(function (e) {
        e.preventDefault();

        // reset everything
        $('#loggedInUsername').text('');
        chrome.extension.sendRequest({
            'action': 'setUsername',
            'username': ''
        }); 
        resetCommentPanel();
        resetLoginPanel();
        $('#homePanel').hide();
        $('#commentPanel').hide();
        $('#uploadTrackPanel').hide();
        $("#editTrackPanel").hide();
        $('#navPanel').hide();
        chrome.browserAction.setBadgeText({text:""});
        $('#loginPanel').show();

    });

    //Press enter to login
    $("#password").keyup(function(event){
        if(event.keyCode == 13){
            $("#login").click();
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

        $.post(URL + "extensionProcessLogin", loginInfo, function (data) {
            if (data) {
                setErrorMessage(data);
            } else {
                chrome.extension.sendRequest({
                    'action': 'setUsername',
                    'username': username
                });

                initializePage(username);
                checkCollaboratorRequests(username);
            }
        });
    });

    // event handler for comment button 
    $('#btnComment').click(function(e) {
        e.preventDefault(); // don't submit form
        setErrorMessage(''); // clear any error messages

        var msg = $('#txtMessage').val();
        var username = $('#loggedInUsername').text();
        var albumname = $('#track_album1 option:selected').text();

        $.post(
            URL + 'extensionComment',
            { 	
                "album_owner": username,
                "album_name": albumname,
                "comment": msg
            },
            function(data) {
                if (data) {
                    setErrorMessage("Not Posted");
                } else {
                    $('#errorMessage').css('color', 'green');
                    setErrorMessage("Posted Successfully");
                    resetCommentPanel();
                }
            });
    });

    $('#btnDownload').click(function(e) {
        e.preventDefault(); // don't submit form
        setErrorMessage(''); // clear any error messages

        var url = $('#btnDownload').val();

        chrome.downloads.download({url: 'http://youtubeinmp3.com/fetch/?video=' + url}, function(id) {
            chrome.downloads.search({id: id}, function(itemArr) {
                var item = itemArr[0];
                if (item.mime.indexOf("html") > -1) {
                    chrome.downloads.cancel(item.id);
                    chrome.tabs.update(null, {url: 'http://youtubeinmp3.com/download/?video=' + url}, null);
                    alert("Convert video to MP3 using this website.");
                }
            });
        });
    });
});

function initializePage(username) {
    resetLoginPanel(); // reset the login panel
    $('#loginPanel').hide(); // hide the login panel

    $('#loggedInUsername').text(username); // show username

    $("#homePanel").show();
    $('#navPanel').show();
    $('#homeTab a').click();

    $('#album_owner').val(username);

    requestCurrentTabURL();

    $.post(URL + "extensionGetAlbums", {"album_owner": username}, function (data) {
        var jsonData = JSON.parse(data);
        createOptions(jsonData);
    });
}

function createOptions( jsonData ) {
    for (var i = 1; i <= 4; i++) {
        $('#track_album'+ i +' option').remove(); // first remove all options

        for (var fieldIndex in jsonData) { // then populatem them
            $('#track_album' + i).append($("<option></option>").attr("value", fieldIndex).text(jsonData[fieldIndex].album_name));
        }
    }
}

function getTracks(panel) {
    var album_name = "";

    if (panel == "edit") {
        album_name = $('#track_album3 option:selected').text();   
    } else {
        album_name = $('#track_album4 option:selected').text();   
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

function createTrackOptions(jsonData, panel) {
    for (var i = 1; i <= 2; i++) {
        $('#track_name_select' + i + ' option').remove(); // first remove all options

        if (!jsonData) {
            $('#track_name_select' + i).append($("<option></option>").text("No Tracks in this Album"));
            $('#track_name_select' + i).attr('disabled', 'disabled');

            if (panel == "edit") {
                $('#new_track').attr('disabled', 'disabled');
                $('#track_edit_submit').attr('disabled', 'disabled');
            } else {
                $('#track_delete_submit').attr('disabled', 'disabled');
            }
        } else {
            $('#track_name_select' + i).removeAttr('disabled');
            if (panel == "edit") {    
                $('#new_track').removeAttr('disabled');
                $('#track_edit_submit').removeAttr('disabled');
            } else {
                $('#track_delete_submit').removeAttr('disabled');
            }
            for (var fieldIndex in jsonData) { // then populatem them
                $('#track_name_select' + i).append($("<option></option>").attr("value", fieldIndex).text(jsonData[fieldIndex].track_name));
            }
        }
    }
}

// ask the background script for the current tab URL
function requestCurrentTabURL() {
    chrome.extension.sendRequest({
        'action': 'getURL' 
    });   
}

function checkCollaboratorRequests(username) {
    $('#collabs').empty();

    var counter = 0;

    $.post(URL + "extensionGetCollaborators", {"user": username}, function (data) {
        var jsonData = JSON.parse(data);

        for (var fieldIndex in jsonData) { // then populate them
            var obj = jsonData[fieldIndex];


            if (obj['sent_by'] !== username) {
                $('#collabPanel').show();

                var date = new Date(obj['modified']);
                var dateString = $.datepicker.formatDate('M dd', date) + " at " + date.toLocaleTimeString();

                $('#collabs').append($("<h4></h4>").text(obj['sent_by'] +' - Request sent on: ' + dateString));
                $('#collabs').append($("<button></button>").attr('class','btn btn-success accept_request').attr('value', obj['sent_by']).text("Accept"));
                $('#collabs').append($("<button></button>").attr('class','btn btn-danger deny_request').attr('value', obj['sent_by']).text("Deny"));

                counter+=1;
            }
        }
        if (counter == 0) {
            chrome.browserAction.setBadgeText({text:""});
        } else {
            chrome.browserAction.setBadgeText({text:""+counter});
        }
    });
}

function resetLoginPanel() {
    setErrorMessage(''); // clear any error messages
    $('#username').val('');
    $('#password').val('');
}

function resetCommentPanel() {
    $('#txtMessage').val('');
}

function setErrorMessage(msg) {
    $('#errorMessage').text(msg);
}

