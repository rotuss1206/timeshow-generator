jQuery(document).ready(function($) {

    const progressBar = $('#progress-bar');
    const progressContainer = $('#progress-container');
    const progressCircle = $('#progress-circle');
    const progressPrcent = $('#progress-text');

    function startFakeProgress(targetPercent, duration = 3000, callback) {
        let start = parseFloat(progressBar.css('width')) / progressContainer.width() * 100 || 0;
        let step = 50; // оновлення кожні 50ms
        let increment = (targetPercent - start) / (duration / step);

        progressContainer.removeClass('hidden');
        progressCircle.removeClass('hidden');
        progressPrcent.removeClass('hidden');

        const interval = setInterval(() => {
            start += increment;
            if (start >= targetPercent) {
                start = targetPercent;
                clearInterval(interval);
                if (callback) callback();
            }
            progressBar.css('width', start + '%');
            $('#progress-text').text(Math.round(start) + '%');
        }, step);
    }

    function finishProgress() {
        progressBar.css('width', '100%');
        $('#progress-text').text('100%');
        setTimeout(() => {
        }, 500);
    }

    function sendAjaxRequest(type, beforeMsg, successCallback, errorContainer) {
        let formData = new FormData();
        formData.append('action', 'ai_upload_json');
        formData.append('security', ai_ajax.nonce);
        formData.append('type', type);

        let fileInput = $('#ai-json-form input[name="json_file"]')[0];
        if(fileInput && fileInput.files.length) {
            formData.append('json_file', fileInput.files[0]);
        }

        $.ajax({
            url: ai_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $(errorContainer).html('<p>' + beforeMsg + '</p>');
                // стартуємо прогрес до 40% на початку
                startFakeProgress(30, 3000);
            },
            success: function(response) {
                if (response.success) {
                    let taskId = response.data.task_id;
                    pollTask(taskId, errorContainer, successCallback, beforeMsg);
                } else {
                    $(errorContainer).html('<p style="color:red;">Error: ' + response.data + '</p>');
                    finishProgress();
                }
            },
            error: function(xhr, status, error) {
                $(errorContainer).html('<p style="color:red;">Error: ' + error + '</p>');
                finishProgress();
            }
        });
    }

    function pollTask(taskId, container, successCallback, processingMsg) {
        let formData = new FormData();
        formData.append('action', 'ai_check_task');
        formData.append('security', ai_ajax.nonce);
        formData.append('task_id', taskId);

        $.ajax({
            url: ai_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    if(response.data.status === 'done') {
                        // після завершення швидко до 100%
                        startFakeProgress(100, 500, () => {
                            finishProgress();
                            successCallback({ data: { result: response.data.result } });
                        });
                    } else {
                        $(container).html('<p>' + processingMsg + '</p>');
                        // просуваємо прогрес трохи до 70-90%
                        startFakeProgress(70, 1000);
                        setTimeout(function() {
                            pollTask(taskId, container, successCallback, processingMsg);
                        }, 3000);
                    }
                } else {
                    $(container).html('<p style="color:red;">Error: ' + response.data + '</p>');
                    finishProgress();
                }
            },
            error: function(xhr, status, error) {
                $(container).html('<p style="color:red;">Error: ' + error + '</p>');
                finishProgress();
            }
        });
    }

    $('#ai-json-form').on('submit', function(e) {
        e.preventDefault();

        sendAjaxRequest(
            'timecode',
            'Processing timecode... Please wait',
            function(response) {
                $('#ai-response').html('<pre>' + $('<div>').text(response.data.result.timecode).html() + '</pre>');
                $('.button_timecode').removeClass('hide').attr('href', response.data.result.timecode_url);

                sendAjaxRequest(
                    'sequence',
                    'Processing sequence... Please wait',
                    function(seqResponse) {
                        $('#ai-response_sequence').html('<pre>' + $('<div>').text(seqResponse.data.result.sequence).html() + '</pre>');
                        $('.button_sequence').removeClass('hide').attr('href', seqResponse.data.result.sequence_url);
                        console.log(seqResponse.data.result.sequence_url)
                    },
                    '#ai-response_sequence'
                );
            },
            '#ai-response'
        );
    });

    // function forceDownload(selector) {
    //     $(selector).on('click', function(e) {
    //         e.preventDefault();
    //         const url = $(this).attr('href');
    //         const a = document.createElement('a');
    //         a.href = url;
    //         a.download = url.split('/').pop();
    //         document.body.appendChild(a);
    //         a.click();
    //         document.body.removeChild(a);
    //     });
    // }
    // forceDownload('.button_timecode');
    // forceDownload('.button_sequence');

});
