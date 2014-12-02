// listener for requests sent from popup.js
chrome.extension.onRequest.addListener(function(request, sender, sendResponse) {
    if (request.action == 'getURL'){ //Get the current tab's URL
        chrome.tabs.getSelected(null, function(tab) {
            var tabURL = tab.url;
            chrome.extension.sendRequest({
                'url': tabURL 
            });
        });
    } else if (request.action == 'getUsername') { //Get the username from location storage
        chrome.storage.local.get(['username'], function(items) {
            chrome.extension.sendRequest({
                'username': items.username
            });
        });

    } else if (request.action == 'setUsername') { //Store the username to local storage.
        chrome.storage.local.set({
            'username': request.username },
                                 function(){}
                                );
    }
});