<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

/**
 * connect files
 */

require_once TIME_DIR . '/includes/shortcodes.php';
require_once TIME_DIR . '/includes/ajax.php';

/**
 * register scripts|styles
 */

function timeshow_register_scripts(){

    wp_register_style('timeshow-bootstrap5-dataTables' , 'https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.css');
    wp_register_style('timeshow-bootstrap-styles' , TIME_URL.'assets/css/bootstrap.min.css');
    wp_register_style('timeshow-styles' , TIME_URL.'assets/css/styles.css');


    wp_register_script('timeshow-bootstrap_popper-script', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js', array('jquery'));
    wp_register_script('timeshow-bootstrap_bundle-script', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js', array('jquery'));
    wp_register_script('dataTables-script', 'https://cdn.datatables.net/2.2.1/js/dataTables.js', array('jquery'));
    wp_register_script('bootstrap5-script', 'https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js', array('jquery'));
    wp_register_script('timeshow-script', TIME_URL.'assets/js/script.js', array('jquery'));
   
    wp_localize_script('timeshow-script', 'timeshow_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_enqueue_scripts', 'timeshow_register_scripts');


function load_custom_wp_admin_style(){
    // wp_register_style('info-admin_style' , TIME_URL.'assets/css/admin_style.css');

}
add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');

add_action('admin_post_timeshow_export_excel', 'timeshow_export_excel');

function timeshow_export_excel(){
    global $wpdb;

    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    $table = 'timeshow_parameters';
    $users = $wpdb->users;

    $rows = $wpdb->get_results(
        "SELECT
            t.timeshow_name,
            u.display_name,
            t.upload_time
        FROM $table t
        LEFT JOIN $users u ON t.user_id = u.ID
        ORDER BY t.upload_time DESC",
        ARRAY_A
    );

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="timeshow_statistics.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Timeshow Name', 'User', 'Upload Time']);

    foreach ($rows as $row){
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

function timeshow_menu_page(){
    require_once(TIME_DIR . '/views/admin-page.php');
}

function timeshow_statistics_page(){
    require_once(TIME_DIR . '/views/admin-statistics-page.php');
}

function register_timeshow_menu_page(){
     add_menu_page('Timeshow Settings','Timeshow Settings', 'manage_options', 'timeshow-settings', 'timeshow_menu_page');
     add_submenu_page(
        'timeshow-settings',
        'Statistics',
        'Statistics',
        'manage_options',
        'timeshow-statistics',
        'timeshow_statistics_page'
    );
}
add_action( 'admin_menu', 'register_timeshow_menu_page' );


// function reg_function(){
//     if(is_admin()) { 
//         if(isset($_GET['page']) && $_GET['page'] == 'quiz-settings'){
//             wp_enqueue_style( 'quiz-admin_style' );
//         }
//     }
// }
// add_action('init', 'reg_function');



function my_plugin_template( $templates ) {

    $templates['timeshow-generator-page.php'] = 'Timeshow';
    return $templates;
}
add_filter( 'theme_page_templates', 'my_plugin_template' );


function my_plugin_template_to_page( $template ) {

    $page_template = get_page_template_slug();
    if ( 'timeshow-generator-page.php' == basename( $page_template ) ) {
        wp_enqueue_style('info-googleapis');
        wp_enqueue_style('info-gstatic');
        wp_enqueue_style('info-css2');
        wp_enqueue_style('info-family');
        wp_enqueue_style("info_normalize-style");
        wp_enqueue_style("info-style");
        wp_enqueue_script("info_common-script");

        return wp_normalize_path( TIME_DIR . '/views/timeshow-generator-page.php' );

    }
    return $template;
}
add_filter( 'template_include', 'my_plugin_template_to_page' );

add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_timeshow-settings') return;

    wp_enqueue_script('ai-json-upload', TIME_URL . '/assets/js/ai-json-upload.js', ['jquery'], null, true);
    wp_localize_script('ai-json-upload', 'ai_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('ai_upload_nonce')
    ]);
});

if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title'    => 'Quiz builder',
        'menu_title'    => 'Quiz builder',
        'menu_slug'     => 'quiz-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));

}

add_action('init', 'add_tables');

function add_tables(){
    global $wpdb;
    // $timeshow_projects = $wpdb->prefix . 'timeshow_projects';
    $timeshow_parameters_table = 'timeshow_parameters';
    $timeshow_events_table = 'timeshow_events';
    $timeshow_stacks_table = 'timeshow_stacks';
    $stack_actions_table = 'stack_actions';
    $timeshow_exports_table = 'timeshow_exports';

    $charset_collate = $wpdb->get_charset_collate();

    $timeshow_parameters = "CREATE TABLE IF NOT EXISTS $timeshow_parameters_table (
        timeshow_id int(40) NOT NULL AUTO_INCREMENT,
        timeshow_name varchar(40) DEFAULT NULL,
        status varchar(250) DEFAULT 'upload',
        user_name varchar(100) DEFAULT NULL,
        user_id int(40) NOT NULL,
        input_type varchar(25) DEFAULT 'CSV',
        input_table_url varchar(250) DEFAULT NULL,
        input_table_lable varchar(100) DEFAULT NULL,
        audio_file_url varchar(250) DEFAULT NULL,
        audio_file_lable varchar(100) DEFAULT NULL,
        active tinyint NOT NULL DEFAULT 1,
        ai_parameters text DEFAULT NULL,
        song_lable varchar(50) DEFAULT NULL,
        artist varchar(50) DEFAULT NULL,
        speed varchar(50) DEFAULT NULL,
        genre varchar(50) DEFAULT NULL,
        length varchar(50) DEFAULT NULL,
        vtt_text varchar(100) DEFAULT NULL,
        timeshow_note_1 varchar(250) DEFAULT NULL,
        timeshow_note_2 varchar(250) DEFAULT NULL,
        upload_time datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (timeshow_id)
    ) $charset_collate ENGINE = InnoDB;";

    $timeshow_events = "CREATE TABLE IF NOT EXISTS $timeshow_events_table (
        event_index_id int(40) NOT NULL AUTO_INCREMENT,
        timeshow_id int(40) NOT NULL,
        event_id varchar(50) NOT NULL,
        time_stamp varchar(50) DEFAULT NULL,
        event_lable varchar(50) DEFAULT NULL,
        event_reference_id int(30) NOT NULL,
        event_color varchar(50) DEFAULT NULL,
        status int(10) DEFAULT 1,
        event_note_1 varchar(250) DEFAULT NULL,
        event_note_2 varchar(250) DEFAULT NULL,

        PRIMARY KEY  (event_index_id)
    ) $charset_collate ENGINE = InnoDB;";

    $timeshow_stacks = "CREATE TABLE IF NOT EXISTS $timeshow_stacks_table (
        stacks_index_id int(40) NOT NULL AUTO_INCREMENT,
        timeshow_id int(40) NOT NULL,
        stack_id varchar(50) DEFAULT NULL,
        stack_time_stamp_in varchar(50) DEFAULT NULL,
        stack_time_stamp_out varchar(50) DEFAULT NULL,
        stack_type varchar(50) DEFAULT NULL,
        stack_lable varchar(150) DEFAULT NULL,
        stack_color varchar(50) DEFAULT NULL,
        stack_note_1 varchar(250) DEFAULT NULL,
        stack_note_2 varchar(250) DEFAULT NULL,
        PRIMARY KEY  (stacks_index_id)
    ) $charset_collate ENGINE = InnoDB;";

    $stack_actions = "CREATE TABLE IF NOT EXISTS $stack_actions_table (
        actions_index_id int(40) NOT NULL AUTO_INCREMENT,
        timeshow_id int(40) NOT NULL,
        stack_id int(40) NOT NULL,
        event_id int(40) NOT NULL,
        time_stamp varchar(50) DEFAULT NULL,
        actions_lable varchar(50) DEFAULT 'event_lable',
        action_id int(15) DEFAULT NULL,
        actions_type varchar(50) DEFAULT NULL,
        action_value varchar(150) DEFAULT NULL,

        PRIMARY KEY  (actions_index_id)
    ) $charset_collate ENGINE = InnoDB;";

    $timeshow_exports = "CREATE TABLE IF NOT EXISTS $timeshow_exports_table (
        export_index_id int(40) NOT NULL AUTO_INCREMENT,
        timeshow_id int(40) NOT NULL,
        timeshow_number int(15) NOT NULL,
        export_time datetime DEFAULT CURRENT_TIMESTAMP,
        export_status varchar(50) DEFAULT NULL,
        user_name varchar(50) DEFAULT NULL,
        user_id int(40) DEFAULT NULL,
        export_type varchar(50) DEFAULT NULL,
        export_lable varchar(50) DEFAULT NULL,
        export_url varchar(250) DEFAULT NULL,
        convert_url varchar(250) DEFAULT NULL,
        export_settings text DEFAULT NULL,
        export_note_1 varchar(250) DEFAULT NULL,
        export_note_2 varchar(250) DEFAULT NULL,
        PRIMARY KEY  (export_index_id)
    ) $charset_collate ENGINE = InnoDB;";
     
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $timeshow_parameters );
    dbDelta( $timeshow_events );
    dbDelta( $timeshow_stacks );
    dbDelta( $stack_actions );
    dbDelta( $timeshow_exports );
}

function get_timeshow_projects($current_user_id=0){
    global $wpdb;
    $table = 'timeshow_parameters';
    // $current_user_id = get_current_user_id();

    $results = $wpdb->get_results( "SELECT * FROM $table WHERE user_id = $current_user_id" );
    return $results;
}

function get_timeshow_exports($timeshow_id=0){
    global $wpdb;
    $table = 'timeshow_exports';

    $results = $wpdb->get_results( "SELECT * FROM $table WHERE timeshow_id = $timeshow_id" );
    return $results;
}

function get_timeshow_events($project_id=0,$stack_id=0){
    global $wpdb;
    $table = 'timeshow_events';
    $current_user_id = get_current_user_id();

    $actions = get_stack_actions($stack_id);   
    $actions_ids = [];
    foreach($actions as $item){
        array_push($actions_ids, $item->event_id);  
    }
    
    $events = $wpdb->get_results( "SELECT * FROM $table WHERE timeshow_id = $project_id" );
    $active_events = [];
    foreach($events as $event){

        if (!in_array($event->event_index_id, $actions_ids)) {

            array_push($active_events,$event);
        }
    }

    return $active_events;
}

function get_stack_actions($stack_id=0){
    global $wpdb;
    $table = 'stack_actions';
    // $timeshow_stacks_table = 'timeshow_stacks';
    $current_user_id = get_current_user_id();

    // $stack = $wpdb->get_row( "SELECT stacks_index_id FROM $timeshow_stacks_table WHERE stack_id = $project_id" );
    $results = $wpdb->get_results( "SELECT * FROM $table WHERE stack_id = $stack_id" );
    
    return $results;
}

function timeshow_get_stacks($project_id=0){
    global $wpdb;
    $table = 'timeshow_stacks';

    $results = $wpdb->get_results( "SELECT * FROM $table WHERE  timeshow_id = $project_id" );
    
    return $results;
}

function timeshow_events_row($events=[]){
    $data = [];
    if($events){ 
        foreach($events as $event){
            $item = [];
            $color = '';
            if($event->event_color){
                $color = '<input style="margin-bottom:0" type="color" class="form-control form-control-color lil_color" id="myColor" value="'.$event->event_color.'" title="Choose a color">';
            } 

            array_push($item, '<input type="checkbox" data-id="'.$event->event_index_id.'">');
            array_push($item, $event->event_id ?? '');
            array_push($item, $event->time_stamp ?? '');
            array_push($item, $event->event_lable ?? '');
            array_push($item, $color);
            array_push($item, '<a class="add_to_actions" href="javascript:void(0)"><img style="width: 20px;" decoding="async" src="'. TIME_URL .'assets/img/arrow-right.png"></a>');
            array_push($data, $item);
        }
    }
    return $data;    
}

function stack_actions_row($stack_actions=[],$stack_id){
    $data = [];
    if($stack_actions){ 
        global $wpdb;
        $table = 'timeshow_stacks';

        $stack = $wpdb->get_row( "SELECT * FROM $table WHERE stacks_index_id = $stack_id" );
        

        foreach($stack_actions as $stack_action){
            $action_type = get_select_action_type($stack->stack_type,$stack_action->actions_type);
            $item = [];
            array_push($item, '<input type="checkbox" data-id="'.$stack_action->actions_index_id.'">');
            array_push($item, '<a class="delete_action" href="javascript:void(0)"><img style="width: 20px;" decoding="async" src="'. TIME_URL .'assets/img/bancirclelinear.png"></a>');
            array_push($item, $stack_action->time_stamp);
            array_push($item, '<input type="number" style="max-width:60px;" class="action_id action_item" value="'.$stack_action->action_id.'" >');
            array_push($item,  $action_type);
            array_push($item, '<input type="text" class="actions_lable action_item" value="'.$stack_action->actions_lable.'" >');
            array_push($item, '<input type="number" min="100" max="100" style="max-width:50px;" class="action_value action_item" value="'.$stack_action->action_value.'" >');
            array_push($data, $item);
        }
    }
    return $data;    
}

function get_select_action_type($stack_type,$stack_action_type=''){
    $types_arr = [];

    if($stack_type == 'Sequence'){
        array_push($types_arr,'goto');
        array_push($types_arr,'Black');
        array_push($types_arr,'Flash_on');
        array_push($types_arr,'Go+');
        array_push($types_arr,'Select');
        array_push($types_arr,'Speed1');
        array_push($types_arr,'Rate1');
        array_push($types_arr,'Swap');
        array_push($types_arr,'Temp');
        array_push($types_arr,'Toggle');
        array_push($types_arr,'On');
        array_push($types_arr,'Off');
        array_push($types_arr,'Top');
        array_push($types_arr,'Master');
        array_push($types_arr,'Master_Temp');
        array_push($types_arr,'Master_Rate');
        array_push($types_arr,'Master_Speed');
        array_push($types_arr,'Master_Time');    
    }elseif($stack_type == 'Time Range'){
        array_push($types_arr,'TimeRange In');
        array_push($types_arr,'TimeRange Out');
    }
    $line = '<select class="form-select lil_select action_item action_type" name="action_type">';
    if (in_array($stack_action_type, $types_arr)) {
        $line .= '<option value="'.$stack_action_type.'" selected="selected">'.$stack_action_type.'</option>';
    }
    $i=0;
    foreach($types_arr as $item){
        if ($stack_action_type != $item) {

            $line .= '<option value="'.$item.'">'.$item.'</option>';
        }
        $i++;
    }
    $line .= '</select>';
    return $line;
}

function timeshow_projects_row($projects=[]){
    $data = [];
    if($projects){ 
        foreach($projects as $project){
            $item = [];
            array_push($item, '<input type="checkbox" data-id="'.$project->timeshow_id.'">');
            array_push($item, 'ID: '.$project->timeshow_id.' | Name: '.$project->timeshow_name);
            array_push($item, $project->upload_time ?? '');
            array_push($item, $project->input_table_lable ?? '');
            array_push($item, $project->audio_file_lable ?? '');
            array_push($item, '<a class="drop-down_menu_show" href="javascript:void(0)">…</a>
                                <div class="drop-down_menu">
                                    <ul style="display: none;">
                                        <li><a class="copy" data-id="'.$project->timeshow_id.'" href="javascript:void(0)">Copy</a></li>
                                        <li><a class="delete" data-id="'.$project->timeshow_id.'" href="javascript:void(0)">Delete</a></li>
                                    </ul>
                                </div>');
            array_push($data, $item);
        }
    }
    return $data;
}

function timeshow_exports_row($exports=[]){
    $data = [];

    global $wpdb;
    $table = 'timeshow_parameters';
    if($exports){ 
        foreach($exports as $export){
            $timeshow_parameter = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE timeshow_id = $export->timeshow_id" ) );

            $item = [];
            
            if (is_user_logged_in()) {
                array_push($item, '<a href="'.$export->export_url.'" download class="load"><img src="'.TIME_URL.'/assets/img/eject-symbol-svgrepo-com.svg"></a>');
            }else{
                array_push($item, '<a href="javascript:void(0)" class="load"><img src="'.TIME_URL.'/assets/img/eject-symbol-svgrepo-com.svg"></a>');
            }

            // if (is_user_logged_in()) {
            //     array_push($item, '<a href="/school/wp-json/timeshow/v1/export/'.$export->timeshow_id.'" target="_blank" class="load"><img src="'.TIME_URL.'/assets/img/eject-symbol-svgrepo-com.svg"></a>');
            // }

            array_push($item, $export->export_lable ?? '');
            if (is_user_logged_in()) {
                array_push($item, 'Ready');
            }else{
                array_push($item, $export->export_status ?? '');
            }
            array_push($item, $export->export_time ?? '');
            array_push($item, $timeshow_parameter->input_table_lable ?? '');
            array_push($item, $timeshow_parameter->audio_file_lable ?? '');
            array_push($item, '<a class="drop-down_menu_show" href="javascript:void(0)">...</a>
                                    <div class="drop-down_menu">
                                        <ul style="display: none;">
                                            <li><a class="copy_export" data-id="'.$export->export_index_id.'" href="javascript:void(0)">Copy</a></li>
                                            <li><a class="delete_export" data-id="'.$export->export_index_id.'" href="javascript:void(0)">Delete</a></li>
                                        </ul>
                                    </div>');
            array_push($data, $item);
        }
    }
    return $data;
}

function stack_row($stacks=[]){
    $data = [];
    if($stacks){ 
        foreach($stacks as $stack){
            $item = [];
            array_push($item, '<input class="stack_row" type="checkbox" data-id="'.$stack->stacks_index_id.'">');
            array_push($item, $stack->stack_lable ?? '');
            array_push($item, '<div class="stack_type">'.$stack->stack_type.'</div>' ?? '');
            array_push($item, $stack->stack_id ?? '');
            array_push($item, '<input style="margin-bottom:0" type="color" class="form-control form-control-color lil_color" id="myColor" value="'.$stack->stack_color.'" title="Choose a color">' ?? '');

            array_push($data, $item);
        }
    }
    return $data;
}

function show_timeshow_projects($projects=[]){
    foreach($projects as $project){
        echo '<tr>
                  <td class="sorting_1"><input type="checkbox" data-id="'.$project->timeshow_id.'"></td>
                  <td>ID: '.$project->timeshow_id.' | Name: '.$project->timeshow_name.'</td>
                  <td>'.$project->upload_time.'</td>
                  <td>'.$project->input_table_lable.'</td>
                  <td>'.$project->audio_file_lable.'</td>
                  <td>
                    <a class="drop-down_menu_show" href="javascript:void(0)">…</a>
                    <div class="drop-down_menu">
                        <ul style="display: none;">
                            <li><a class="copy" data-id="'.$project->timeshow_id.'" href="javascript:void(0)">Copy</a></li>
                            <li><a class="delete" data-id="'.$project->timeshow_id.'" href="javascript:void(0)">Delete</a></li>
                        </ul>
                    </div>
                  </td>
                </tr>';
    }
}

function parse_events($timeshow_id=0,$input_table_url=''){

    global $wpdb;
    $table = 'timeshow_events';

    $row = 1;
    $current_user_id = get_current_user_id();

    if($input_table_url){
        if (($handle = fopen($input_table_url, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);

                $row++;
                if($row > 2){
                    $event_id='';$event_lable='';$time_stamp='';$event_color='';
                    for ($c = 0; $c < $num; $c++) {
                        switch ($c) {
                            case 0:
                                $event_id = $data[$c] ?? '';
                                break;
                            case 1:
                                $event_lable = $data[$c] ?? '';
                                break;
                            case 2:
                                $time_stamp = $data[$c] ?? '';
                                break;
                            case 3:
                                $color = '';
                                if($data[$c]){
                                    $color = '#'.$data[$c];
                                }
                                $event_color = $color;
                                break;
                        }         
                    }

                    $test = $wpdb->insert($table, array(
                        'timeshow_id' => $timeshow_id,
                        'event_id' => $event_id ?? '',
                        'event_lable' => $event_lable ?? '', 
                        'time_stamp' => $time_stamp ?? '',
                        'event_color' =>  $event_color ?? '',
                    ));
              
                }
                
            }

            fclose($handle);
        }
    }
    
}

function parse_stacks($timeshow_id=0,$input_table_url=''){

    global $wpdb;
    $table = 'timeshow_stacks';

    $row = 1;
    $current_user_id = get_current_user_id();

    if($input_table_url){
        if (($handle = fopen($input_table_url, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);

                $row++;
                if($row > 2){
                    $stack_id='';$stack_type='';$stack_lable='';$stack_color='';
                    for ($c = 0; $c < $num; $c++) {
                        switch ($c) {
                            
                            case 0:
                                $stack_id = $data[$c] ?? '';
                                break;
                            case 1:
                                $stack_type = $data[$c] ?? '';
                                break;
                            case 2:
                                $stack_lable = $data[$c] ?? '';
                                break;
                            case 3:
                                $stack_color = $data[$c] ?? '';
                                break;
                        }         
                    }
                  
                    $stack = $wpdb->get_row( "SELECT stack_color FROM $table WHERE timeshow_id = $timeshow_id AND stack_color = '$stack_color'" );
                    if(!$stack->stack_color){
                        $wpdb->insert($table, array(
                            'stack_id' => $stack_id,
                            'stack_type' => $stack_type ?? '',
                            'stack_lable' => $stack_lable ?? '', 
                            'stack_color' => $stack_color ?? '',
                            'timeshow_id' =>  $timeshow_id,
                        ));
                    }
              
                }
                
            }
          
            fclose($handle);
        }
    }
    
}

function rand_color() {
    return sprintf('#%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255));
}

function generateUuidV4() {
    $data = random_bytes(16);

    // Версія 4
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Variant RFC 4122
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function generateHexBytes() {
    $bytes = random_bytes(16); // 16 байтів
    return strtoupper(implode(' ', str_split(bin2hex($bytes), 2)));
}

function ai_make_request($system_prompt, $user_prompt, $file_content, $openai_api_key) {
    $ch = curl_init();

    $data = json_encode([
        "model" => "gpt-4.1",
        "messages" => [
            [
                "role" => "system",
                "content" => $system_prompt
            ],
            [
                "role" => "user",
                "content" => $user_prompt . $file_content
            ],
        ]
    ]);

    //Here we use cURL to contact the AI and send it prompts.

    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openai_api_key
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = 'Error: ' . curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }

    curl_close($ch);

    $arr = json_decode($result, true);
    return $arr;
}

function ai_get_func($output_file = '', $type = 'timecode') {
    $openai_api_key = get_option('openai_api_key');
    if (!$openai_api_key) {
        return 'no openai_api_key';
    }

    $openai_system   = get_option('openai_system');
    $openai_user     = get_option('openai_user');
    $openai_system_s = get_option('openai_system_s');
    $openai_user_s   = get_option('openai_user_s');

    $file_content = file_get_contents($output_file);
    if ($file_content === false) {
        return "The file could not be read.";
    }

    $upload_dir = TIME_DIR . '/uploads/ai_docs';
    if (!file_exists($upload_dir)) {
        wp_mkdir_p($upload_dir);
    }
    $upload_url = TIME_URL . 'uploads/ai_docs';

    //Here depending on the type we choose what to transmit for communication with the AI.

    if ($type === 'timecode') {
        $response_timecode = ai_make_request($openai_system, $openai_user, $file_content, $openai_api_key);

        if(isset($response_timecode['error'])){
            $responce1 = $response_timecode['error']['message'] ?? '';
        }else{
            $responce1 = $response_timecode['choices'][0]['message']['content'] ?? '';
        }
        

        $responce1_clean = remove_first_last_line($responce1);
        $timecode_file = $upload_dir . '/timecode.xml';
        file_put_contents($timecode_file, $responce1_clean);

        return [
            'timecode'     => $responce1,
            'timecode_url' => $upload_url . '/timecode.xml'
        ];
    }

    if ($type === 'sequence') {
        $response_sequence = ai_make_request($openai_system_s, $openai_user_s, $file_content, $openai_api_key);
        $responce2 = $response_sequence['choices'][0]['message']['content'] ?? '';
        if(isset($response_sequence['error'])){
            $responce2 = $response_sequence['error']['message'] ?? '';
        }else{
            $responce2 = $response_sequence['choices'][0]['message']['content'] ?? '';
        }

        $responce2_clean = remove_first_last_line($responce2);
        $sequence_file = $upload_dir . '/sequence.xml';
        file_put_contents($sequence_file, $responce2_clean);

        return [
            'sequence'     => $responce2,
            'sequence_url' => $upload_url . '/sequence.xml'
        ];
    }

    return ['error' => 'Unknown type'];
}


function remove_first_last_line($text) {
    // $lines = explode("\n", $text); // розбиваємо на рядки
    // if (count($lines) <= 3) {
    //     return ''; // якщо рядків менше або рівно 3, повертаємо пустий рядок
    // }
    // array_shift($lines); // видаляємо перший рядок
    // array_pop($lines);   // видаляємо останній рядок
    // array_pop($lines);   // видаляємо ще один останній рядок
    // return implode("\n", $lines); // збираємо назад
    return $text;
}

function jsonToXml($json, $rootElement = 'root') {
    // Декодуємо JSON у масив
    $data = json_decode($json, true);

    // Створюємо об'єкт SimpleXMLElement
    $xml = new SimpleXMLElement("<$rootElement></$rootElement>");

    // Рекурсивна функція для додавання елементів до XML
    function arrayToXml($data, &$xml) {
        foreach ($data as $key => $value) {
            // Якщо значення - це масив, рекурсивно додаємо його
            if (is_array($value)) {
                // Якщо масив містить інші асоціативні масиви, то додаємо відповідний елемент
                if (is_assoc($value)) {
                    $subnode = $xml->addChild($key);
                    arrayToXml($value, $subnode);
                } else {
                    // Якщо це просто масив, додаємо елементи з індексами
                    foreach ($value as $item) {
                        $subnode = $xml->addChild($key);
                        arrayToXml($item, $subnode);
                    }
                }
            } else {
                // Додаємо елемент до XML
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }

    // Викликаємо рекурсивну функцію
    arrayToXml($data, $xml);

    // Повертаємо XML
    return $xml;
}

// Перевірка на асоціативний масив
function is_assoc($array) {
    return array_keys($array) !== range(0, count($array) - 1);
}

function get_event_color($project_id,$color){
    global $wpdb;
    $events_table = 'timeshow_events';
    $stack_table = 'timeshow_stacks';

    $colors = [];
    $events = $wpdb->get_results( $wpdb->prepare( "SELECT event_color FROM $events_table WHERE timeshow_id = $project_id" ) );
    foreach($events as $event){
        if($event->event_color){
            if(!in_array($event->event_color, $colors)){
                array_push($colors,$event->event_color);
            }
        }
    }
    foreach($colors as $color){
        $stack_color = $wpdb->get_row( $wpdb->prepare( "SELECT stack_color FROM $stack_table WHERE stack_color = '$color' AND timeshow_id = $project_id") );
        
        if(!$stack_color){
            
            $result = $color;
            break;
        }
    }

    if(!$result){
        $result = rand_color();
    }

    return $result;
}

function add_new_stacks_by_colors($project_id, $project_name, $events) {
    global $wpdb;

    $stack_table  = 'timeshow_stacks';
    $action_table = 'stack_actions';

    $stacks = []; // масив нових/обраних стеків для подальшої обробки
    $i = 0;

    // Попередній скан — чи є хоча б один "librosa" івент
    $has_librosa = false;
    foreach ($events as $ev_check) {
        if (isset($ev_check->event_note_1) && strtolower(trim($ev_check->event_note_1)) === 'librosa') {
            $has_librosa = true;
            break;
        }
    }

    // === 1) Створюємо стеки за кольором / No color (як раніше) ===
    foreach ($events as $event) {
        $color = isset($event->event_color) ? trim($event->event_color) : '';

        if ($color !== '') {
            // чи існує стек з таким кольором в цьому проекті?
            $existing_stack = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM $stack_table WHERE stack_color = %s AND timeshow_id = %d",
                    $color,
                    $project_id
                )
            );

            if (!$existing_stack) {
                // формуємо label (унікальний індекс у рамках проекту)
                $stack_label = $project_name;

                // чи є стек з точно такою назвою в цьому timeshow?
                $count_same = intval($wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM $stack_table WHERE stack_lable = %s AND timeshow_id = %d",
                        $stack_label,
                        $project_id
                    )
                ));

                if ($count_same > 0) {
                    // get all labels starting with project_name
                    $rows = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT stack_lable FROM $stack_table WHERE stack_lable LIKE %s AND timeshow_id = %d",
                            $project_name . '%',
                            $project_id
                        )
                    );
                    $maxIndex = 0;
                    foreach ($rows as $r) {
                        if (preg_match('/_(\d+)$/', $r->stack_lable, $m)) {
                            $idx = intval($m[1]);
                            if ($idx > $maxIndex) $maxIndex = $idx;
                        } else {
                            // there is a pure project_name without a suffix -> count as 0
                            if ($r->stack_lable === $project_name) {
                                $maxIndex = max($maxIndex, 0);
                            }
                        }
                    }
                    $stack_label = $project_name . '_' . ($maxIndex + 1);
                }

                // If you have a marked librosa - let's make label = 'Additional'
                if (isset($event->event_note_1) && strtolower(trim($event->event_note_1)) === 'librosa') {
                    $stack_label = 'Additional';
                }

                // selection of numeric stack_id (1000+)
                $number = 1000;
                $max_stack_id = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT MAX(stack_id) FROM $stack_table WHERE timeshow_id = %d",
                        $project_id
                    )
                );
                if ($max_stack_id !== null) {
                    $max_stack_id = intval($max_stack_id);
                    if ($max_stack_id >= 1000) $number = $max_stack_id + 1;
                }

                // inserting a new stack
                $wpdb->insert($stack_table, [
                    'timeshow_id'  => $project_id,
                    'stack_type'   => 'Sequence',
                    'stack_lable'  => $stack_label,
                    'stack_id'     => $number,
                    'stack_color'  => $color
                ]);

                $row_id = $wpdb->insert_id; // car number (primary key)
                // we save the data we need later
                $stacks[$i] = [
                    'row_id'      => $row_id,      // PK timeshow_stacks.id
                    'stack_number'=> $number,      // custom stack_id (1000...)
                    'stack_color' => $color,
                    'name'        => $stack_label
                ];
                $i++;
            }
        } else {
            // No color case
            $existing_stack = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM $stack_table WHERE stack_color = %s AND timeshow_id = %d",
                    $color,
                    $project_id
                )
            );

            if (!$existing_stack) {
                $stack_label = 'No color';
                $already = intval($wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM $stack_table WHERE stack_lable = %s AND timeshow_id = %d",
                        $stack_label,
                        $project_id
                    )
                ));

                if ($already === 0) {
                    $number = 1000;
                    $max_stack_id = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT MAX(stack_id) FROM $stack_table WHERE timeshow_id = %d",
                            $project_id
                        )
                    );
                    if ($max_stack_id !== null) {
                        $max_stack_id = intval($max_stack_id);
                        if ($max_stack_id >= 1000) $number = $max_stack_id + 1;
                    }

                    $wpdb->insert($stack_table, [
                        'timeshow_id'  => $project_id,
                        'stack_type'   => 'Sequence',
                        'stack_lable'  => $stack_label,
                        'stack_id'     => $number,
                        'stack_color'  => $color
                    ]);

                    $row_id = $wpdb->insert_id;
                    $stacks[$i] = [
                        'row_id'      => $row_id,
                        'stack_number'=> $number,
                        'stack_color' => $color,
                        'name'        => $stack_label
                    ];
                    $i++;
                }
            }
        }
    } // кінець створення стеків за кольором

    // === Додатково: гарантуємо існування стека 'Additional' якщо були librosa-івенти ===
    if ($has_librosa) {
        $additional = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $stack_table WHERE stack_lable = %s AND timeshow_id = %d",
                'Additional',
                $project_id
            )
        );

        if ($additional) {
            // додаємо існуючий 'Additional' до масиву $stacks (щоб додати до нього екшени)
            // визначаємо PK (id) — намагаємось узяти поле 'id' або 'ID', інакше беремо stack_id (як fallback)
            $add_row_id = null;
            if (isset($additional->id)) $add_row_id = intval($additional->id);
            elseif (isset($additional->ID)) $add_row_id = intval($additional->ID);
            elseif (isset($additional->stack_id)) $add_row_id = intval($additional->stack_id);

            if ($add_row_id) {
                // перевіримо чи він вже в $stacks щоб не дублювати
                $found = false;
                foreach ($stacks as $s) {
                    if (isset($s['row_id']) && intval($s['row_id']) === $add_row_id) {
                        $found = true; break;
                    }
                }
                if (!$found) {
                    $stacks[$i++] = [
                        'row_id'      => $add_row_id,
                        'stack_number'=> $additional->stack_id ?? 0,
                        'stack_color' => $additional->stack_color ?? '',
                        'name'        => 'Additional'
                    ];
                }
            }
        } else {
            // створюємо новий Additional (без кольору)
            $number = 1000;
            $max_stack_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT MAX(stack_id) FROM $stack_table WHERE timeshow_id = %d",
                    $project_id
                )
            );
            if ($max_stack_id !== null) {
                $max_stack_id = intval($max_stack_id);
                if ($max_stack_id >= 1000) $number = $max_stack_id + 1;
            }

            $wpdb->insert($stack_table, [
                'timeshow_id'  => $project_id,
                'stack_type'   => 'Sequence',
                'stack_lable'  => 'Additional',
                'stack_id'     => $number,
                'stack_color'  => ''
            ]);

            $row_id = $wpdb->insert_id;
            $stacks[$i] = [
                'row_id'      => $row_id,
                'stack_number'=> $number,
                'stack_color' => '',
                'name'        => 'Additional'
            ];
            $i++;
        }
    }

    // === 2) Створення екшенів (для кожного створеного/обраного стека) ===
    foreach ($stacks as $stack) {
        $stack_row_id = intval($stack['row_id']);
        $stack_name   = $stack['name'];
        $stack_color  = $stack['stack_color'];

        foreach ($events as $event) {
            // якщо стек — Additional, переносимо тільки librosa-івенти
            if ($stack_name === 'Additional') {
                if (!(isset($event->event_note_1) && strtolower(trim($event->event_note_1)) === 'librosa')) {
                    continue;
                }
            } else {
                // для звичних стеків — по кольору / No color
                if ($stack_name === 'No color') {
                    if (!(isset($event->event_color) && trim($event->event_color) === '')) {
                        continue;
                    }
                } else {
                    // звичайний кольоровий стек: event_color має дорівнювати stack_color
                    if (!isset($event->event_color) || trim($event->event_color) !== $stack_color) {
                        continue;
                    }
                }
            }

            // перевірка чи уже існує екшин для цього event_id в цьому стеку
            $action_exists = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM $action_table WHERE event_id = %d AND stack_id = %d",
                    intval($event->event_index_id),
                    $stack_row_id
                )
            );

            if (!$action_exists) {
                // визначаємо action_id (1..20)
                $last_action = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM $action_table WHERE stack_id = %d ORDER BY action_id DESC LIMIT 1",
                        $stack_row_id
                    )
                );

                if ($last_action) {
                    $action_id = intval($last_action->action_id) + 1;
                    // if ($action_id > 20) $action_id = 20;
                } else {
                    $action_id = 1;
                }

                $wpdb->insert($action_table, [
                    'timeshow_id'   => $project_id,
                    'stack_id'      => $stack_row_id,
                    'event_id'      => intval($event->event_index_id),
                    'time_stamp'    => $event->time_stamp ?? '',
                    'actions_lable' => $event->event_lable ?? '',
                    'action_id'     => $action_id,
                    'actions_type'  => 'goto',
                    'action_value'  => 0
                ]);
            }
        }
    }

    // === 3) Оновлюємо часові рамки (in/out) для кожного стека ===
    foreach ($stacks as $stack) {
        $stack_row_id = intval($stack['row_id']);

        // Обчислюємо мінімальний та максимальний час екшенів у стеку, конвертуючи VARCHAR у DECIMAL
        $timestamps = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                    MIN(CAST(time_stamp AS DECIMAL(10,3))) AS time_in,
                    MAX(CAST(time_stamp AS DECIMAL(10,3))) AS time_out
                 FROM $action_table
                 WHERE stack_id = %d AND timeshow_id = %d",
                $stack_row_id,
                $project_id
            )
        );

        // Оновлюємо, якщо знайдено хоча б один екшен
        if ($timestamps && ($timestamps->time_in !== null || $timestamps->time_out !== null)) {
            $wpdb->update(
                $stack_table,
                [
                    'stack_time_stamp_in'  => (string)$timestamps->time_in,
                    'stack_time_stamp_out' => (string)$timestamps->time_out
                ],
                [ 'id' => $stack_row_id ], // оновлюємо по PK (AUTO_INCREMENT id)
                [ '%s', '%s' ],
                [ '%d' ]
            );
        }
    }

    // якщо потрібно — можна повертати масив створених стеків
    return $stacks;
}



function add_to_archive($file, $name) {
    $zip = new ZipArchive();

    // Унікальна папка за time()
    $timestamp = time();
    $folderPath = TIME_DIR . '/uploads/ai_docs/' . $timestamp;
    $folderUrl  = TIME_URL . '/uploads/ai_docs/' . $timestamp;

    // Створюємо папку, якщо не існує
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }

    // Ім'я архіву
    $zipFileName = $folderPath . '/' . $name . '.zip';
    $zipFileUrl = $folderUrl . '/' . $name . '.zip';

    // Відкриваємо або створюємо архів
    if ($zip->open($zipFileName, ZipArchive::CREATE) !== TRUE) {
        exit("Не вдалося створити архів\n");
    }

    // Шляхи до файлів, які потрібно заархівувати
    $file1 = $file;
    $file2 = TIME_DIR . '/uploads/ai_docs/sequences.xml';

    // Додаємо файли в архів
    if (file_exists($file1)) {
        $zip->addFile($file1, basename($file1));
    } else {
        return "Файл не знайдено: $file1\n";
    }

    if (file_exists($file2)) {
        $zip->addFile($file2, basename($file2));
    } else {
        return "Файл не знайдено: $file2\n";
    }

    // Закриваємо архів
    $zip->close();

    return $zipFileUrl;
}

function get_audio_file_path_from_db( $timeshow_id ) {
    global $wpdb;

    $table = 'timeshow_parameters';

    $audio_url = $wpdb->get_var( $wpdb->prepare(
        "SELECT audio_file_url FROM $table WHERE timeshow_id = %d",
        $timeshow_id
    ) );

    if ( empty( $audio_url ) ) {
        return false;
    }

    // Find the position 'uploads/' in the URL
    $pos = strpos($audio_url, 'uploads/');
    if ($pos === false) {
        return false;
    }

    // Relative path from 'uploads/' to the end of the line
    $relative_path = substr($audio_url, $pos);

    // We guarantee that TIME_DIR ends with a slash
    $time_dir = rtrim(TIME_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    // Remove the leading slash from the relative path (if any)
    $relative_path = ltrim($relative_path, DIRECTORY_SEPARATOR);

    // Forming an absolute path to the file
    $file_path = $time_dir . $relative_path;

    if ( file_exists($file_path) ) {
        return realpath($file_path);
    }

    return false;
}

function analyze_audio_file_from_project_id($project_id) {
    $project_id = intval($project_id);
    $path = get_audio_file_path_from_db($project_id);

    if (!$path || !file_exists($path)) {
        return new WP_Error('no_audio', 'Audio file not found.');
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'http://152.53.141.150:5000/analyze',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_SAFE_UPLOAD => true,
        CURLOPT_POSTFIELDS => [
            'audio' => new CURLFile($path)
        ],
        CURLOPT_TIMEOUT => 120,
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        curl_close($curl);
        return new WP_Error('api_error', 'Error accessing API: ' . $error_msg);
    }

    $content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    curl_close($curl);

    $upload_dir = wp_upload_dir();
    $filename = 'audio_analysis_' . $project_id . '.png';
    $img_path = $upload_dir['basedir'] . '/' . $filename;

    if (strpos($content_type, 'application/json') !== false) {
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_response', 'Invalid JSON response.');
        }

        if (isset($data['image_base64'])) {
            $img_data = base64_decode($data['image_base64']);
            if ($img_data !== false) {
                $saved = file_put_contents($img_path, $img_data);
                if ($saved !== false) {
                    $img_url = trailingslashit($upload_dir['baseurl']) . $filename;
                    $data['image_url'] = $img_url;
                }
            }   
        }
     
        return $data;

    } elseif (strpos($content_type, 'image/png') !== false) {
        $saved = file_put_contents($img_path, $response);
        if ($saved === false) {
            return new WP_Error('save_error', 'Failed to save image to server.');
        }
        $img_url = trailingslashit($upload_dir['baseurl']) . $filename;
        return ['image_url' => $img_url];
    } else {

        return new WP_Error('invalid_response', 'Unknown API response format: ' . $content_type);
    }
}

function analyze_audio_and_save_events($project_id) {
    global $wpdb;

    $project_id = intval($project_id);
    $path = get_audio_file_path_from_db($project_id);

    if (!$path || !file_exists($path)) {
        return new WP_Error('no_audio', 'Audio file not found.');
    }

    // === 1. Відправка файлу в API ===
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'http://152.53.141.150:5000/analyze',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_SAFE_UPLOAD => true,
        CURLOPT_POSTFIELDS => [
            'audio' => new CURLFile($path)
        ],
        CURLOPT_TIMEOUT => 120,
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        curl_close($curl);
        return new WP_Error('api_error', 'Error accessing API: ' . $error_msg);
    }

    $content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    curl_close($curl);

    $upload_dir = wp_upload_dir();
    $filename = 'audio_analysis_' . $project_id . '.png';
    $img_path = $upload_dir['basedir'] . '/' . $filename;

    // === 2. Обробка відповіді від API ===
    if (strpos($content_type, 'application/json') !== false) {
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_response', 'Invalid JSON response.');
        }

        // Зберігаємо картинку, якщо є
        if (isset($data['image_base64'])) {
            $img_data = base64_decode($data['image_base64']);
            if ($img_data !== false) {
                $saved = file_put_contents($img_path, $img_data);
                if ($saved !== false) {
                    $img_url = trailingslashit($upload_dir['baseurl']) . $filename;
                    $data['image_url'] = $img_url;
                }
            }
        }

        // === 3. Витягуємо потрібні дані для івентів ===
        $beat_times = isset($data['beat_times']) ? $data['beat_times'] : [];
        $rms = isset($data['rms']) ? $data['rms'] : [];
        $tempo = isset($data['tempo'][0]) ? $data['tempo'][0] : 120;
        
        // витягуємо івенти

        $events_librosa = isset($data['events']) ? $data['events'] : [];
  
        // === 4. Генеруємо івенти і зберігаємо в БД ===
        $events = generate_timeshow_events($project_id, $beat_times, $rms, $tempo, $events_librosa);

        // Повертаємо все: дані з API + нові івенти
        return [
            'analysis' => $data,
            'events'   => $events
        ];

    } elseif (strpos($content_type, 'image/png') !== false) {
        $saved = file_put_contents($img_path, $response);
        if ($saved === false) {
            
            return new WP_Error('save_error', 'Failed to save image to server.');
        }
        $img_url = trailingslashit($upload_dir['baseurl']) . $filename;
        return ['image_url' => $img_url];
    } else {

        return new WP_Error('invalid_response', 'Unknown API response format: ' . $content_type);
    }
}


function generate_timeshow_events($timeshow_id, $beat_times, $rms, $tempo, $events_librosa = []) {
    global $wpdb;

    $table = 'timeshow_events'; 

    $existing_color = $wpdb->get_var(
        $wpdb->prepare("SELECT event_color FROM {$table} WHERE timeshow_id = %d LIMIT 1", $timeshow_id)
    );

    if (!$existing_color) {
        $existing_color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    $events = [];

    if(!empty($events_librosa)){

        foreach($events_librosa as $event){
            $result = $wpdb->insert(
                $table,
                [
                    "timeshow_id"   => $timeshow_id,
                    "event_id"      => $event['event_id'] ?? '',
                    "event_lable"   => $event['event_label'] ?? '',
                    "time_stamp"    => $event['time_stamp'] ?? '',
                    "event_color"   => $existing_color,
                    "event_note_1"  => "librosa"
                ],
                [
                    "%d", "%s", "%s", "%s", "%s", "%s"
                ]
            );

            if ($result === false) {
                error_log("DB Insert failed: " . $wpdb->last_error);
            }
        }

    }else{
        foreach ($beat_times as $index => $time) {
            $event_id = "M" . ($index + 1);
            $current_rms = $rms[$index] ?? 0;

         
            if ($current_rms < 1e-6) {
                $label = "ruhe";
            } elseif ($current_rms < 0.002) {
                $label = ($tempo > 150) ? "fillIn" : "Teppich";
            } elseif ($current_rms < 0.01) {
                $label = "buildUp";
            } else {
                $label = "drop";
            }

            $time_stamp = round($time, 3);

            $events[] = [
                "timeshow_id" => $timeshow_id,
                "event_id"    => $event_id,
                "event_label" => $label,
                "time_stamp"  => $time_stamp,
                "event_color" => $existing_color,
                "event_note_1" => "librosa"
            ];

            $result = $wpdb->insert(
                $table,
                [
                    "timeshow_id"   => $timeshow_id,
                    "event_id"      => $event_id,
                    "event_lable"   => $label,
                    "time_stamp"    => $time_stamp,
                    "event_color"   => $existing_color,
                    "event_note_1"  => "librosa"
                ],
                [
                    "%d", "%s", "%s", "%s", "%s", "%s"
                ]
            );

            if ($result === false) {
                error_log("DB Insert failed: " . $wpdb->last_error);
            }
        }
    }

    

    return $events; 
}

function export_timeshow_project($project_id) {
    global $wpdb;

    // --- 1. Basic parameters with timeshow_parameters ---
    $params = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM timeshow_parameters WHERE timeshow_id = %d", $project_id),
        ARRAY_A
    );

    if (!$params) {
        return ["error" => "Project not found"];
    }

    // --- 2. Data from timeshow_exports ---
    $exports = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM timeshow_exports WHERE timeshow_id = %d", $project_id),
        ARRAY_A
    );

    // --- 3. Stacks ---
    $stacks_raw = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM timeshow_stacks WHERE timeshow_id = %d", $project_id),
        ARRAY_A
    );

    $stacks = [];
    foreach ($stacks_raw as $stack) {
        $index_id = (int) ($stack['stacks_index_id'] ?? 0);

        $actions_raw = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM stack_actions WHERE timeshow_id = %d AND stack_id = %d ORDER BY CAST(time_stamp AS DECIMAL(10,3)) ASC",
                $project_id, $index_id
            ),
            ARRAY_A
        );

        $actions = [];
        $used_labels = [];
        $time_in  = null;
        $time_out = null;

        foreach ($actions_raw as $a) {
            $ts = isset($a['time_stamp']) ? floatval($a['time_stamp']) : 0;

            // Перша і остання мітка часу
            if ($time_in === null) {
                $time_in = $ts;
            }
            $time_out = $ts;

            // --- робимо actions_lable унікальним ---
            $label = !empty($a['actions_lable']) ? sanitize_title($a['actions_lable']) : 'event_label';
            $base_label = $label;
            $i = 1;
            while (in_array($label, $used_labels, true)) {
                $label = "{$base_label}_{$i}";
                $i++;
            }
            $used_labels[] = $label;
            // ---------------------------------------

            $actions[] = [
                "time_stamp"     => $ts,
                "actions_lable"  => $label,
                "action_id"      => (int) ($a['action_id'] ?? 0),
                "actions_type"   => $a['actions_type'] ?? "",
                "action_value"   => $a['action_value'] ?? null,
            ];
        }

        $stacks[] = [
            "stack_id"            => $stack['stack_id'] ?? '',
            "stack_time_stamp_in"  => $time_in ?? 0,
            "stack_time_stamp_out" => $time_out ?? 0,
            "stack_type"           => $stack['stack_type'] ?? "Sequence",
            "stack_lable"          => isset($stack['stack_lable']) ? sanitizeName($stack['stack_lable']) : "",
            "stack_color"          => ltrim($stack['stack_color'] ?? "", "#"),
            "actions"              => $actions
        ];
    }

    // --- 5. Events ---
    $events = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM timeshow_events WHERE timeshow_id = %d", $project_id),
        ARRAY_A
    );

    // --- 6. Forming JSON ---
    $export = [
        "timeshow_id"   => (int) $params['timeshow_id'],
        "timeshow_name" => isset($params['timeshow_name']) ? sanitizeName($params['timeshow_name']) : "",
        "user_id"       => (int) ($params['user_id'] ?? 0),
        "input_type"    => $params['input_type'] ?? "",

        "project" => [
            "timeshow_number"   => $exports['timeshow_number'] ?? "",
            "export_lable"      => isset($exports['export_lable']) ? sanitizeName($exports['export_lable']) : "",
            "audio_file_lable"  => $params['audio_file_lable'] ?? "",
            "audio_file_url"    => $params['audio_file_url'] ?? "",
            "input_table_lable" => $params['input_table_lable'] ?? "",
            "input_table_url"   => $params['input_table_url'] ?? "",
            "song_lable"        => $params['song_lable'] ?? "",
            "artist"            => $params['artist'] ?? "",
            "speed"             => $params['speed'] ?? "",
            "genre"             => $params['genre'] ?? "",
            "length"            => $params['length'] ?? "",
        ],

        "stacks" => $stacks,
        // "events" => $events
    ];

    return $export;
}

function export_timeshow_project_json_file($project_id) {
    global $wpdb;

    // --- 1. Дані проекту ---
    $params = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM timeshow_parameters WHERE timeshow_id = %d", $project_id),
        ARRAY_A
    );

    if (!$params) {
        return ["error" => "Project not found"];
    }

    $exports = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM timeshow_exports WHERE timeshow_id = %d", $project_id),
        ARRAY_A
    );

    $stacks_raw = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM timeshow_stacks WHERE timeshow_id = %d", $project_id),
        ARRAY_A
    );

    $stacks = [];
    foreach ($stacks_raw as $stack) {
        $index_id = (int) ($stack['stacks_index_id'] ?? 0);

        $actions_raw = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM stack_actions WHERE timeshow_id = %d AND stack_id = %d ORDER BY CAST(time_stamp AS DECIMAL(10,3)) ASC",
                $project_id, $index_id
            ),
            ARRAY_A
        );

        $actions = [];
        $used_labels = [];
        $time_in  = null;
        $time_out = null;

        foreach ($actions_raw as $a) {
            $ts = isset($a['time_stamp']) ? floatval($a['time_stamp']) : 0;

            if ($time_in === null) {
                $time_in = $ts;
            }
            $time_out = $ts;

            $label = !empty($a['actions_lable']) ? sanitize_title($a['actions_lable']) : 'event_label';
            $base_label = $label;
            $i = 1;
            while (in_array($label, $used_labels, true)) {
                $label = "{$base_label}_{$i}";
                $i++;
            }
            $used_labels[] = $label;

            $actions[] = [
                "time_stamp"     => $ts,
                "actions_lable"  => $label,
                "action_id"      => (int) ($a['action_id'] ?? 0),
                "actions_type"   => $a['actions_type'] ?? "",
                "action_value"   => $a['action_value'] ?? null,
            ];
        }

        $stacks[] = [
            "stack_id"            => $stack['stack_id'] ?? '',
            "stack_time_stamp_in"  => $time_in ?? 0,
            "stack_time_stamp_out" => $time_out ?? 0,
            "stack_type"           => $stack['stack_type'] ?? "Sequence",
            "stack_lable"          => isset($stack['stack_lable']) ? sanitizeName($stack['stack_lable']) : "",
            "stack_color"          => ltrim($stack['stack_color'] ?? "", "#"),
            "actions"              => $actions
        ];
    }

    // --- 5. Events ---
    $events = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM timeshow_events WHERE timeshow_id = %d", $project_id),
        ARRAY_A
    );

    // --- 6. Формуємо JSON ---
    $export = [
        "timeshow_id"   => (int) $params['timeshow_id'],
        "timeshow_name" => isset($params['timeshow_name']) ? sanitizeName($params['timeshow_name']) : "",
        "user_id"       => (int) ($params['user_id'] ?? get_current_user_id()),
        "input_type"    => $params['input_type'] ?? "",
        "project" => [
            "timeshow_number"   => $exports['timeshow_number'] ?? "",
            "export_lable"      => isset($exports['export_lable']) ? sanitizeName($exports['export_lable']) : "",
            "audio_file_lable"  => $params['audio_file_lable'] ?? "",
            "audio_file_url"    => $params['audio_file_url'] ?? "",
            "input_table_lable" => $params['input_table_lable'] ?? "",
            "input_table_url"   => $params['input_table_url'] ?? "",
            "song_lable"        => $params['song_lable'] ?? "",
            "artist"            => $params['artist'] ?? "",
            "speed"             => $params['speed'] ?? "",
            "genre"             => $params['genre'] ?? "",
            "length"            => $params['length'] ?? "",
        ],
        "stacks" => $stacks,
        "events" => $events
    ];

    // --- 7. Формуємо шлях до файлу ---
    $user_id = get_current_user_id();
    $user_folder = TIME_DIR . '/uploads/conv/user_' . $user_id;

    // якщо папки немає — створюємо
    if (!file_exists($user_folder)) {
        wp_mkdir_p($user_folder);
    }

    $file_path = $user_folder . '/project_' . $project_id . '.json';

    // --- 8. Записуємо JSON ---
    $json_data = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($file_path, $json_data);

    // --- 9. Записуємо JSON ---
    $file_url = TIME_URL.'uploads/conv/user_' . $user_id.'/project_' . $project_id . '.json';

    // --- 10. Повертаємо результат ---
    return [
        "success" => true,
        "file_url" => $file_url,
        "file" => $file_path
    ];
}


function sanitizeName($name) {
    // Replace anything that is not a letter, number, or underscore with "_"
    $clean = preg_replace('/[^a-zA-Z0-9]+/', '_', $name);

    // Remove unnecessary underscores from the beginning and end
    $clean = trim($clean, '_');

    return $clean;
}

add_action('rest_api_init', function() {
    register_rest_route('timeshow/v1', '/export/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => function($data) {
            $project_id = intval($data['id']);
            $export = export_timeshow_project($project_id);

            if (isset($export['error'])) {
                return $export;
            }

            header('Content-Type: application/json; charset=utf-8');
            echo wp_json_encode(
                $export,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            exit;
        },
        'permission_callback' => '__return_true'
    ]);
});

// Helpers
function ai_get_tasks() {
    $tasks = get_option('ai_tasks', []);
    return is_array($tasks) ? $tasks : [];
}

function ai_save_tasks($tasks) {
    update_option('ai_tasks', $tasks);
}

add_filter('theme_page_templates', function($templates) {
    $templates['test_api_page.php'] = 'Test Converter Page';
    return $templates;
});

add_filter('template_include', function($template) {
    if (is_page()) {
        $page_template = get_page_template_slug();
        if ($page_template === 'test_api_page.php') {

            $plugin_template = TIME_DIR . '/views/test_api_page.php';


            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
    }
    return $template;
});



function send_json_to_api($name_file, $file_path, $user_id, $api_url = 'http://152.53.141.150:8081/convert_zip') {
    if (!file_exists($file_path)) {
        return ['error' => 'File not found'];
    }

    $access_key = 'API'; // береться з config.py

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'tsc-AccessKey: ' . $access_key,
        'tsc-UserID: ' . $user_id
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file_path));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120); // якщо великі файли
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code >= 400) {
        return ['error' => "HTTP $http_code", 'raw' => $response];
    }

    // Якщо API повертає ZIP, збережемо його в файл
    $content_type = mime_content_type($file_path);
    if (strpos($response, 'PK') === 0) { // ZIP-файли починаються з 'PK'
        $path = TIME_DIR. '/uploads/conv/user_'.$user_id;
        if (!file_exists($path)) {
            mkdir($path, 0755, true); // 0755 — стандартні права доступу, true — дозволяє створювати вкладені папки
        }
        $out_file = $path .'/'.$name_file.'.zip'; // змінити шлях
        file_put_contents($out_file, $response);
        return ['success' => true, 'file' => $out_file];
    }

    // Спроба декодувати як JSON
    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Invalid JSON response', 'raw' => $response];
    }

    return $decoded;
}