/* jslint browser: true */
/* globals document, window, ace, jQuery */
'use strict';
(function (ace, $, storage) {
    const codeSeparator = '#php-sandbox-end-output#',
        errorHeader = 'X-Error',
        storageKey = 'php_sandbox';

    const $output = $('#output'),
        $form = $('#form'),
        $buttons = $('.btn', $form),
        $editor = $('#editor');

    let code = $editor.val();
    // Load last session
    if (storage) {
        code = storage.getItem(storageKey) || code;
    }
    // Initialize Ace editor
    $editor.replaceWith(`<div class="position-relative rounded-top" id="editor"></div>`);
    const editor = ace.edit(
        document.getElementById('editor'),
        {
            theme: 'ace/theme/eclipse',
            mode: 'ace/mode/php',
            highlightActiveLine: true,
            highlightSelectedWord: true,
            highlightGutterLine: true,
            autoScrollEditorIntoView: true,
            showPrintMargin: true,
            wrap: true,
            useWorker: true,
            useSoftTabs: true,
            tooltipFollowsMouse: true,
            enableBasicAutocompletion: true,
            enableLiveAutocompletion: true,
            enableSnippets: false,
            enableCodeLens: true,
            behavioursEnabled: true,
            enableMultiselect: true,
            minLines: 2,
            tabSize: 4,
            printMargin: 120,
            cursorStyle: 'ace',
            newLineMode: 'unix',
            foldStyle: 'manual',
            fontSize: 14
        }
    );
    editor.setValue(code, 2);
    editor.focus();

    // Add custom PHP autocomplete
    const autocomplete = ace.require('ace/autocomplete').Autocomplete;
    autocomplete.for(editor);
    editor.completer.exactMatch = true;

    const langTools = ace.require('ace/ext/language_tools');
    langTools.addCompleter({
        getCompletions: function (editor, session, pos, prefix, callback) {
            if (prefix.length > 2 && /^[_A-Za-z\\]\w+$/.test(prefix)) {
                $.getJSON(
                    '/autocomplete',
                    {prefix: prefix, position: pos},
                    function (response) {
                        callback(null, response);
                    }
                );
            } else {
                callback(null, []);
            }
        }
    });

    // Initialize status bar to display cursor position
    const StatusBar = ace.require('ace/ext/statusbar').StatusBar;
    new StatusBar(editor, document.getElementById('statusbar'));

    // Adds commands
    editor.commands.addCommand({
        name: 'executeCode',
        bindKey: {win: 'Ctrl-E', mac: 'Ctrl-E'},
        exec: function () {
            // Save session
            if (storage) {
                storage.setItem(storageKey, editor.getValue());
            }
            editor.getSession().setAnnotations([]);
            // Lock form buttons and editor
            editor.setReadOnly(true);
            $buttons.prop('disabled', true);
            $output.html('<p class="text-primary">Execution...</p>');
            // Execute code
            $.post(
                '/execute',
                {code: editor.getValue()},
                function (response) {
                    // Unlock form buttons and editor
                    editor.setReadOnly(false);
                    $buttons.prop('disabled', false);

                    if (response.endsWith(codeSeparator)) {
                        $output.html(response.slice(0, -codeSeparator.length));
                    } else {
                        $output.html('<p class="text-danger">PHP script ended unexpectedly.</p>\n' + response);
                    }
                }
            );
        },
        readOnly: false
    });
    editor.commands.addCommand({
        name: 'formatCode',
        bindKey: {win: 'Ctrl-F', mac: 'Ctrl-F'},
        exec: function () {
            // Save session
            if (storage) {
                storage.setItem(storageKey, editor.getValue());
            }
            editor.getSession().setAnnotations([]);
            $output.html('<p class="text-primary">Format code style...</p>');
            // Lock form buttons and editor
            editor.setReadOnly(true);
            $buttons.prop('disabled', true);
            // Execute code
            $.post(
                '/format',
                {code: editor.getValue()},
                function (response) {
                    // Unlock form buttons and editor
                    editor.setReadOnly(false);
                    $buttons.prop('disabled', false);

                    editor.setValue(response, 2);
                    $output.html('<p class="text-success">Code style was formatted!</p>');
                }
            );
        },
        readOnly: false
    });

    // Initialize request errors
    $(document).ajaxError(function (event, jqXHR) {
        // unlock form buttons and editor
        editor.setReadOnly(false);
        $buttons.prop('disabled', false);
        if (jqXHR.getResponseHeader(errorHeader) != null) {
            const matches =  jqXHR.getResponseHeader(errorHeader).match(/^"([^"]+)"; line=(\d+)$/);
            editor.getSession().setAnnotations([{
                row: matches[2],
                column: 0,
                text: matches[1],
                type: 'error'
            }]);
            $output.html(`<p class="text-danger">Execution error, see error marker in editor</p>`);
        } else {
            $output.html(`<p class="text-danger">Execution error: ${jqXHR.statusText}</p>`);
        }
    });

    // Reset code
    $('#reset').on('click', function (event) {
        event.preventDefault();
        if (storage) {
            editor.setValue(storage.getItem(storageKey) || '<?php\n\n', 2);
        } else {
            editor.setValue('<?php\n\n', 2);
        }
    });

    // Format code
    $('#format').on('click', function (event) {
        event.preventDefault();
        editor.execCommand('formatCode');
    });

    // Does an async request to eval the PHP code and displays the result
    $form.on('submit', function (event) {
        event.preventDefault();
        editor.execCommand('executeCode');
    });
}(ace, jQuery, window.localStorage));
