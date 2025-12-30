<?php
    $openai_api_key = get_option( 'openai_api_key' );
    $openai_system = get_option( 'openai_system' );
    $openai_user = get_option( 'openai_user' );
    $openai_system_s = get_option( 'openai_system_s' );
    $openai_user_s = get_option( 'openai_user_s' );

    if(isset($_POST['openai_api_key'])){
        $openai_api_key = trim(stripslashes($_POST['openai_api_key']));
        update_option( 'openai_api_key', $openai_api_key );
    }
    if(isset($_POST['openai_system'])){
        $openai_system = trim(stripslashes($_POST['openai_system']));
        update_option( 'openai_system', $openai_system );
    }
    if(isset($_POST['openai_user'])){
        $openai_user = trim(stripslashes($_POST['openai_user']));
        update_option( 'openai_user', $openai_user );
    }if(isset($_POST['openai_system_s'])){
        $openai_system_s = trim(stripslashes($_POST['openai_system_s']));
        update_option( 'openai_system_s', $openai_system_s );
    }
    if(isset($_POST['openai_user_s'])){
        $openai_user_s = trim(stripslashes($_POST['openai_user_s']));
        update_option( 'openai_user_s', $openai_user_s );
    }
    
?>

<h2>Settings page</h2>

<div class="admin-block">
    <h3>OpenAI settings</h3>
    <div>
        <div>  
            <form class="w_profile" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                    
                
                <p><label><strong>Api key</strong></label></p>
                <textarea name="openai_api_key" rows="8" cols="150"><?= esc_textarea($openai_api_key) ?></textarea>

                <h3>Timecode</h3>

                <p><label><strong>Role (System) For Timecode</strong></label></p>
                <textarea name="openai_system" rows="8" cols="150"><?= esc_textarea($openai_system) ?></textarea>

                <p><label><strong>Role (User)  For Timecode</strong></label></p>
                <p>After this message a link with a file in json format is attached.</p>
                <textarea name="openai_user" rows="8" cols="150"><?= esc_textarea($openai_user) ?></textarea>

                <h3>Sequence</h3>

                <p><label><strong>Role (System) For Sequence</strong></label></p>
                <textarea name="openai_system_s" rows="20" cols="150"><?= esc_textarea($openai_system_s) ?></textarea>

                <p><label><strong>Role (User)  For Sequence</strong></label></p>
                <p>After this message a link with a file in json format is attached.</p>
                <textarea name="openai_user_s" rows="20" cols="150"><?= esc_textarea($openai_user_s) ?></textarea>

                <!-- <input type="text" name="openai_api_key" value="<?= $quiz_curl_url; ?>"> -->
                <p><button class="admin-prop-btn w-200" name="upd-price">Update</button></p>
            </form>
        </div>
    </div>
    <hr>
    <div>
        <div>  
           <div class="wrap">
                <h1>JSON upload for AI</h1>
                <form id="ai-json-form" enctype="multipart/form-data">
                    <input type="file" name="json_file" accept=".json" required>
                    <button type="submit" class="button button-primary">Send</button>
                </form>
                <div id="progress-container-wrapper" class="w-full max-w-[800px] mt-4 relative">
                    <div id="progress-text" class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-sm font-bold text-gray-700 hidden">0%</div>
                    <div id="progress-container" class="w-full bg-gray-200 rounded-full h-6 hidden">
                        <div id="progress-bar" class="bg-blue-500 h-6 rounded-full w-0 transition-all duration-300 "></div>
                        <div id="progress-circle" class="absolute -top-3 -right-3 w-6 h-6 border-4 border-blue-500 rounded-full animate-spin hidden"></div>
                    </div>
                </div>
                <div id="ai-response" style="margin-top:20px;"></div>
                <div id="ai-response_sequence" style="margin-top:20px;"></div>
                <a href="" download="" class="button button-primary button_timecode hide">Download TC</a>
                <a href="" download="" class="button button-primary button_sequence hide">Download Sequence</a>
            </div>
        </div>
    </div>
</div>

<style>
    textarea {
        resize: none;
/*      min-width: 300px; */
    }
    #ai-response pre {
        max-width: 800px;
        white-space: pre-wrap;
        word-break: break-word;
        background: #f5f5f5;
        padding: 10px;
        border: 1px solid #ddd;
        overflow-x: auto; 
    }
    #ai-response_sequence pre {
        max-width: 800px;
        white-space: pre-wrap;
        word-break: break-word;
        background: #f5f5f5;
        padding: 10px;
        border: 1px solid #ddd;
        overflow-x: auto; 
    }
    body .hide{
        display: none!important;
    }
    #progress-container-wrapper {
        width: 100%;
        max-width: 800px;
        position: relative;
        margin-top: 10px;
    }

    #progress-container {
        background-color: #e2e8f0;
        border-radius: 12px;
        height: 24px;
        width: 100%;
        position: relative;
    }

    #progress-bar {
        background-color: #3b82f6;
        height: 100%;
        width: 0%;
        border-radius: 12px;
        transition: width 0.3s ease;
    }

    #progress-text {
        position: absolute;
        top: 50%; /* по центру бару */
        left: 50%;
        transform: translate(-50%, -50%);
        color: #374151;
        font-weight: bold;
        z-index: 10;
    }

    #progress-circle {
        border-top-color: transparent;
        border-right-color: transparent;
        border-bottom-color: #3b82f6;
        position: absolute;
    }
</style>