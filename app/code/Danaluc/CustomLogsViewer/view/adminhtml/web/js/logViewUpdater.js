define(['jquery', 'domReady!'], function ($) {
    'use strict';

    /**
     * scroll to the bottom of text
     */
    const consoleScroll = function() {
        var logData = $('#log_file_data'),
            dh = logData.scrollHeight,
            ch = logData.clientHeight;

        if (dh > ch) {
            logData.scrollTop = dh - ch;
        }
    }

    /**
     * Update elements
     * @param {String} log
     * @param {String} url
     */
    const doUpdate = function(log, url) {
        $.post(url, {
            log: log
        }, function (json) {
            $('#log_file_data').html(json.content);
            $('#custom-log-header').html(json.header);
            consoleScroll();
        });
    }

    /**
     * Restart log file selected
     * @param {String} url 
     */
    const restartFile = function(url) {
        let responseArray;
        $.ajax({
            url: url,
            method: 'POST',
            data:{ 
                'fileToRestart': $('#log_files').val()
            },
            dataType: 'json'
        }).done((json) => {
            responseArray = JSON.parse(JSON.stringify(json));
            if (responseArray.result) {
                $('#clean-file-result-container').removeClass('message-error error');
                $('#clean-file-result-container').addClass('message-success success');
            } else {
                $('#clean-file-result-container').addClass('message-error error');
                $('#clean-file-result-container').removeClass('message-success success');
            }
        }).fail(() => {
            $('#clean-file-result-container').addClass('message-error error');
            $('#clean-file-result-container').removeClass('message-success success');
        }).always((json) => {
            responseArray = JSON.parse(JSON.stringify(json));
            $('#clean-file-result-container').css('visibility', 'unset');
            $('#clean-file-result-message').text(responseArray.message);
        });
    }

    /**
     * Export/return log updater
     * @param {Object} logViewUpdater
     */
    return function (logViewUpdater) {
        consoleScroll();

        //Observer select
        $('#log_files').change(function () {
            doUpdate($('#log_files').val(), logViewUpdater.urlcontent);
        });

        //Observer button click
        $('#connector-log-restart').click(function () {
            restartFile(logViewUpdater.urlreset);
            doUpdate($('#log_files').val(), logViewUpdater.urlcontent);
        });
    };
});