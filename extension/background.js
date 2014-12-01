// listener for requests sent from popup.js

chrome.extension.onRequest.addListener(function(request, sender, sendResponse) {
    if (request.action == 'getURL'){
        chrome.tabs.getSelected(null, function(tab) {
            var tabURL = tab.url;
            chrome.extension.sendRequest({
                'url': tabURL 
            });
        });
    } else if (request.action == 'getUsername') {
        chrome.storage.local.get(['username'], function(items) {
            chrome.extension.sendRequest({
                'username': items.username
            });
        });

    } else if (request.action == 'setUsername') {
        chrome.storage.local.set({
            'username': request.username },
                                 function(){}
                                );
    }
});