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
            initializePage(request.username); //Initialize the main page after logging in or opening the extension
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

    //
    var lastTab = 0;
    $(".tab").click(function() {
        var num = this.id.match(/\d+/)[0];

        if (lastTab != num) {
            lastTab = num;
            setErrorMessage('');
        }

        $("#tab_" + num).tab('show');
        $(".panel").hide();
        $("#panel_" + num).show();

        if (num == 4) {
            getTracks("edit");
        } else if (num == 5) {
            getTracks("delete");
        }

    });

    $('#logoutTab a').click(function (e) {
        e.preventDefault();

        // reset everything
        $('#loggedInUsername').text('');
        chrome.extension.sendRequest({
            'action': 'setUsername',
            'username': ''
        }); 

        resetLoginPanel();

        for (var i = 1; i <= 5; i++) {
            $('#panel_'+i).hide();
        }

        $('#navPanel').hide();
        chrome.browserAction.setBadgeText({text:""});
        $('#loginPanel').show();

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
                        getAlbums(username);
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

    //Creates a new track for that album
    $("#createtrack").click(function(e) {
        e.preventDefault();
        var track_album = $('#track_album1 option:selected').text();
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

        var trackAlbum = $('#track_album2 option:selected').text();
        var albumOwner = $('#loggedInUsername').text();
        var trackName = $("#new_track").val();
        var oldTrackName = $('#track_name_select1 option:selected').text();

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
                $('#tab_4 a').click();
            }
        });		
    });

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
                $('#tab_5 a').click();
            }
        });
    });

    $('#track_album2').on('change', function() {
        getTracks("edit");
    });

    $('#track_album3').on('change', function() {
        getTracks("delete");
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

    checkCollaboratorRequests(username);

    $('#loggedInUsername').text(username); // show username

    $("#homePanel").show();
    $('#navPanel').show();
    $('#tab_1 a').click();

    $('#album_owner').val(username);

    requestCurrentTabURL();

    getAlbums(username);
}


// ask the background script for the current tab URL
function requestCurrentTabURL() {
    chrome.extension.sendRequest({
        'action': 'getURL' 
    });   
}

function getAlbums(username) {
    $.post(URL + "extensionGetAlbums", {"album_owner": username}, function (data) {
        var jsonData = JSON.parse(data);
        createAlbumOptions(jsonData);
    });
}

function createAlbumOptions( jsonData ) {
    for (var i = 1; i <= 3; i++) {
        $('#track_album'+ i +' option').remove(); // first remove all options

        for (var fieldIndex in jsonData) { // then populate them
            $('#track_album' + i).append($("<option></option>").attr("value", fieldIndex).text(jsonData[fieldIndex].album_name));
        }
    }
}

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

function checkCollaboratorRequests(username) {
    $('#collabPanel').hide();
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
    $('.form-control').val('');
}

function setErrorMessage(msg) {
    $('#errorMessage').text(msg);
}

