// listener for requests sent from popup.js

chrome.extension.onRequest.addListener(function(request, sender, sendResponse) {
  if (request.action == 'getURL'){
        chrome.tabs.getSelected(null, function(tab) {
            var tabURL = tab.url;
            chrome.extension.sendRequest({
               'url': tabURL 
            });
        });
  } else if (request.action == 'getAPIKey') {
      chrome.storage.local.get(['apiKey', 'username'], function(items) {
             chrome.extension.sendRequest({
                'apiKey': items.apiKey,
                 'username': items.username
             });
      });
      
  } else if (request.action == 'setAPIKey') {
      chrome.storage.local.set({
          'apiKey': request.apiKey,
          'username': request.username },
        function(){}
        );
    }
});
      
