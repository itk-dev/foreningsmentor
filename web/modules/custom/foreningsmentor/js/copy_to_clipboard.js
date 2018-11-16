(function ($, Drupal) {
  Drupal.AjaxCommands.prototype.copyToClipboardCommand = function (ajax, response) {
    console.log(ajax, response);
    function fallbackCopyTextToClipboard(text) {
      var textArea = document.createElement("textarea");
      textArea.value = text;
      textArea.id = 'copy-to-clipboard-area';
      document.body.appendChild(textArea);

      textArea.focus();
      textArea.select();

      try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Fallback: Copying text command was ' + msg);

        // If not successful fallback to window.prompt.
        if (!successful) {
          window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
        }
      } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
      }

      document.body.removeChild(textArea);
    }

    function copyTextToClipboard(text) {
      navigator.clipboard.writeText(text).then(function() {
        console.log('Async: Copying to clipboard was successful!');
      }, function(err) {
        console.error('Async: Could not copy text: ', err);
      });
    }

    navigator.permissions.query({
      name: 'clipboard-write'
    }).then(function (permissionStatus) {
      console.log(permissionStatus);
      if (permissionStatus === 'granted' && navigator.clipboard) {
        copyTextToClipboard(response.list)
      }
      else {
        fallbackCopyTextToClipboard(response.list);
      }
    });
  };
})(jQuery, Drupal);
