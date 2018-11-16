(function ($, Drupal) {
  Drupal.AjaxCommands.prototype.copyToClipboardCommand = function (ajax, response) {
    /**
     * Fallback function if access to clipboard is not allowed.
     * @param text
     */
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

    /**
     * Async copy to clipboard.
     * @param text
     */
    function copyTextToClipboard(text) {
      navigator.clipboard.writeText(text).then(function() {
        console.log('Async: Copying to clipboard was successful!');
      }, function(err) {
        console.error('Async: Could not copy text: ', err);
      });
    }

    // Ask for access to clipboard and existence, else use fallback.
    navigator.permissions.query({
      name: 'clipboard-write'
    }).then(function (permissionStatus) {
      if (permissionStatus.state === 'granted' && navigator.clipboard) {
        copyTextToClipboard(response.list);
      }
      else {
        fallbackCopyTextToClipboard(response.list);
      }
    });
  };
})(jQuery, Drupal);
