<?php

function timeshow_create_project(){

    global $wpdb;
    $table = 'timeshow_parameters';

    $current_user_id = get_current_user_id();
    $first_name = get_user_meta( $current_user_id, 'first_name', true );
    $last_name = get_user_meta( $current_user_id, 'last_name', true );
    $time = time();
    $input_table_upload_url = TIME_DIR.'/uploads/'.$time.'_'.$_FILES["file"]['name'];
    $audio_file_upload_url = TIME_DIR.'/uploads/'.$time.'_'.$_FILES["mediafile"]['name'];
    

    if ( 0 < $_FILES['file']['error'] ) {
        // echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        $check = move_uploaded_file($_FILES['file']['tmp_name'], $input_table_upload_url);
        if($check){
            $file_name_ex = explode(".", $_FILES["file"]['name']);
            $file_lable = $file_name_ex[0];
            $file_url = TIME_URL.'uploads/'.$time.'_'.$_FILES["file"]['name'];
        }        
    }

    if ( 0 < $_FILES['mediafile']['error'] ) {
        // echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        $check = move_uploaded_file($_FILES['mediafile']['tmp_name'], $audio_file_upload_url);
        if($check){
            $file_name_ex = explode(".", $_FILES["mediafile"]['name']);
            $music_lable = $file_name_ex[0];
            $music_url = TIME_URL.'uploads/'.$time.'_'.$_FILES["mediafile"]['name'];
        }        
    }

    $wpdb->insert($table, array(
        'timeshow_name' => trim($_POST['projects_name']),
        'input_table_url' => $file_url ?? '',
        'input_table_lable' => $file_lable ?? '', 
        'audio_file_url' => $music_url ?? '',
        'audio_file_lable' =>  $music_lable ?? '',
        'user_name' => $first_name.' '.$last_name,
        'user_id' => $current_user_id
    ));
    $timeshow_id = $wpdb->insert_id;


    parse_events($timeshow_id,$input_table_upload_url);

    $liblosa = analyze_audio_and_save_events($timeshow_id);

    if ( is_wp_error($liblosa) ) {

        $error_message = $liblosa->get_error_message();
        wp_send_json_error(array('error' => $error_message));
        wp_die();
    }

    $wpdb->update( $table, array( 
        'song_lable' => $liblosa['analysis']['song_label'] ?? '',
        'artist' => $liblosa['analysis']['artist'] ?? '',
        'audio_file_lable' => $music_lable ?? '',
        'upload_time' => date('Y-m-d H:i:s'),
        'genre' => $liblosa['analysis']['genre'] ?? '',
        'speed' => $liblosa['analysis']['speed'][0] ?? '',
        'length' => $liblosa['analysis']['length'] ?? '',
        'timeshow_note_1' => $liblosa['analysis']['image_url'] ?? ''

    ),array('timeshow_id'=>$timeshow_id));

    $projects = get_timeshow_projects($current_user_id);
    $result['data'] = timeshow_projects_row($projects);
    $result['liblosa'] = $liblosa;
    $result['insert_id'] = $timeshow_id;
    echo json_encode($result, true);
  
    // show_timeshow_projects($projects);

    wp_die();
    
} //endfunction
add_action('wp_ajax_timeshow_create_project', 'timeshow_create_project');
add_action('wp_ajax_nopriv_timeshow_create_project', 'timeshow_create_project');

function import_stacks(){

    $project_id = intval($_POST['project_id']);

    $time = time();
    $input_table_upload_url = TIME_DIR.'/uploads/'.$time.'_'.$_FILES["file"]['name'];
    if ( 0 < $_FILES['file']['error'] ) {
        // echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        $check = move_uploaded_file($_FILES['file']['tmp_name'], $input_table_upload_url);
        if($check){
            $file_name_ex = explode(".", $_FILES["file"]['name']);
            $file_lable = $file_name_ex[0];
            $file_url = TIME_URL.'uploads/'.$time.'_'.$_FILES["file"]['name'];
        }        
    }

    parse_stacks($project_id,$input_table_upload_url);

    $get_stacks = timeshow_get_stacks($project_id);
    $result['data_stacks'] = stack_row($get_stacks);
    $result['project_id'] = $project_id;
    echo json_encode($result, true);
  
    // show_timeshow_projects($projects);

    wp_die();
    
} //endfunction
add_action('wp_ajax_import_stacks', 'import_stacks');
add_action('wp_ajax_nopriv_import_stacks', 'import_stacks');

function timeshow_delete_project(){

    global $wpdb;
    $table = 'timeshow_parameters';
    $table_timeshow_events = 'timeshow_events';
    $stack_actions_table = 'stack_actions';
    $timeshow_stacks_table = 'timeshow_stacks';
    $current_user_id = get_current_user_id();
    $project_id = intval($_POST['project_id']) ?? 0;

    $wpdb->delete( $table, array( 'timeshow_id' => $project_id ));
    $wpdb->delete( $table_timeshow_events, array( 'timeshow_id' => $project_id ));
    $wpdb->delete( $stack_actions_table, array( 'timeshow_id' => $project_id ));
    $wpdb->delete( $timeshow_stacks_table, array( 'timeshow_id' => $project_id ));

    $projects = get_timeshow_projects($current_user_id);
    $result['data'] = timeshow_projects_row($projects);
    echo json_encode($result, true);
  
    // show_timeshow_projects($projects);

    wp_die();
    
} //endfunction
add_action('wp_ajax_timeshow_delete_project', 'timeshow_delete_project');
add_action('wp_ajax_nopriv_timeshow_delete_project', 'timeshow_delete_project');

function timeshow_delete_export(){

    global $wpdb;
    $table = 'timeshow_exports';
    $current_user_id = get_current_user_id();
    $project_id = intval($_POST['project_id']) ?? 0;
    $export_id = intval($_POST['export_id']) ?? 0;

    $wpdb->delete( $table, array( 'export_index_id' => $export_id ));

    $exports = get_timeshow_exports($project_id);
    $result['data'] = timeshow_exports_row($exports);
    echo json_encode($result, true);
  
    // show_timeshow_projects($projects);

    wp_die();
    
} //endfunction
add_action('wp_ajax_timeshow_delete_export', 'timeshow_delete_export');
add_action('wp_ajax_nopriv_timeshow_delete_export', 'timeshow_delete_export');

function timeshow_copy_project(){

    global $wpdb;
    $table = 'timeshow_parameters';
    $timeshow_events_table = 'timeshow_events';
    $stack_actions_table = 'stack_actions';
    $timeshow_stacks_table = 'timeshow_stacks';
    $current_user_id = get_current_user_id();
    $project_id = intval($_POST['project_id']) ?? 0;

    $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE timeshow_id = $project_id" ) );
    $timeshow_events = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $timeshow_events_table WHERE timeshow_id = $project_id" ) );
    $timeshow_stacks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $timeshow_stacks_table WHERE timeshow_id = $project_id" ) );

    if($project){
        $wpdb->insert($table, array(
            'timeshow_name' => $project->timeshow_name.'(copy)',
            'input_table_url' => $project->input_table_url ?? '',
            'input_table_lable' => $project->input_table_lable ?? '', 
            'audio_file_url' => $project->audio_file_url ?? '',
            'audio_file_lable' =>  $project->audio_file_lable ?? '',
            'user_name' =>  $project->user_name ?? '',
            'user_id' => $project->user_id
        ));
        $timeshow_id = $wpdb->insert_id;
    }
    
    if($timeshow_events){
        foreach($timeshow_events as $event){

            $wpdb->insert($timeshow_events_table, array(
                'timeshow_id' => $timeshow_id,
                'event_id' => $event->event_id ?? '',
                'event_lable' => $event->event_lable ?? '', 
                'time_stamp' => $event->time_stamp ?? '',
                'event_color' => $event->event_color ?? '',
            ));
        }
    }
    
    if($timeshow_stacks){
        foreach($timeshow_stacks as $stack){

            $wpdb->insert($timeshow_stacks_table, array(
                'timeshow_id' => $timeshow_id,
                'stack_id' => $stack->stack_id ?? '',
                'stack_time_stamp_in' => $stack->stack_time_stamp_in ?? '', 
                'stack_time_stamp_out' => $stack->stack_time_stamp_out ?? '',
                'stack_type' => $stack->stack_type ?? '',
                'stack_lable' => $stack->stack_lable ?? '',
                'stack_color' => $stack->stack_color ?? '',
                'stack_note_1' => $stack->stack_note_1 ?? '',
                'stack_note_2' => $stack->stack_note_2 ?? '',
            ));
            $stack_id = $wpdb->insert_id;

            $stack_actions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $stack_actions_table WHERE stack_id = $stack->stacks_index_id" ) );

            if($stack_actions){
                foreach($stack_actions as $action){

                    $wpdb->insert($stack_actions_table, array(
                        'timeshow_id' => $timeshow_id,
                        'stack_id' => $stack_id,
                        'event_id' => $action->event_id ?? '', 
                        'time_stamp' => $action->time_stamp ?? '',
                        'actions_lable' => $action->actions_lable ?? '',
                        'action_id' => $action->action_id ?? '',
                        'actions_type' => $action->actions_type ?? '',
                        'action_value' => $action->action_value ?? '',
                    ));
                }
            }
        }
    }

    $projects = get_timeshow_projects($current_user_id);
    $result['data'] = timeshow_projects_row($projects);
    echo json_encode($result, true);
  
    // show_timeshow_projects($projects);

    wp_die();
    
} //endfunction
add_action('wp_ajax_timeshow_copy_project', 'timeshow_copy_project');
add_action('wp_ajax_nopriv_timeshow_copy_project', 'timeshow_copy_project');

function get_timeshow_project(){

    global $wpdb;
    $table = 'timeshow_parameters';
    // $stacks_table = 'timeshow_stacks';
    $project_id = intval($_POST['project_id']) ?? 0;

    $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE timeshow_id = $project_id" ) );

    // $stack = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $stacks_table WHERE timeshow_id = $project_id" ) );

    // if(!$stack){
    //     $wpdb->insert($stacks_table, array(
    //         'timeshow_id' => $project_id,
    //         'stack_id' => $stack_id,
    //         'stack_type' => 'Sequence', 
    //         'stack_lable' => $project->timeshow_name,
    //         'stack_id' =>  1000,
    //         'stack_color' =>  rand_color()
    //     ));
    //     $stack_id = $wpdb->insert_id;
    // }
    if ( $project ) {
      
        $length_float = isset($project->length) && $project->length !== '' ? (float) $project->length : 0.0;

        $seconds_total = (int) floor( $length_float );

        if ($seconds_total < 0) $seconds_total = 0;

        $minutes = (int) floor( $seconds_total / 60 );
        $seconds = $seconds_total % 60;

        $length_human = sprintf('%d:%02d', $minutes, $seconds);

        $project->length_seconds = $seconds_total;      // ціле число секунд
        $project->length = $length_human;               // тепер "0:29"
        $project->length_raw = $length_float;           // оригінал як float (якщо треба)
    }
    $result['project'] = $project;
    echo json_encode($result, true);
  
    // show_timeshow_projects($projects);

    wp_die();
    
} //endfunction
add_action('wp_ajax_get_timeshow_project', 'get_timeshow_project');
add_action('wp_ajax_nopriv_get_timeshow_project', 'get_timeshow_project');

function save_timeshow_project(){

    global $wpdb;
    $table = 'timeshow_parameters';

    $wpdb->update( $table, array( 
        'timeshow_name' => trim($_POST['timeshow_name']),
        'song_lable' => trim($_POST['song_lable']),
        'artist' => trim($_POST['artist']),
        'audio_file_lable' => trim($_POST['audio_file_lable']),
        'upload_time' => trim($_POST['upload_time']),
        'genre' => trim($_POST['genre']),
        'speed' => trim($_POST['speed']),
        'length' => trim($_POST['length'])

    ),array('timeshow_id'=>intval($_POST['project_id'])));

    $result['project_id'] = $_POST['project_id'];
    $result['timeshow_name'] = $_POST['timeshow_name'];
    $result['song_lable'] = $_POST['song_lable'];
    $result['artist'] = $_POST['artist'];
    $result['audio_file_lable'] = $_POST['audio_file_lable'];
    $result['upload_time'] = $_POST['upload_time'];
    $result['genre'] = $_POST['genre'];
    $result['speed'] = $_POST['speed'];
    $result['length'] = $_POST['length'];

    echo json_encode($result, true);
  
    // show_timeshow_projects($projects);

    wp_die();
    
} //endfunction
add_action('wp_ajax_save_timeshow_project', 'save_timeshow_project');
add_action('wp_ajax_nopriv_save_timeshow_project', 'save_timeshow_project');

function get_timeshow_events_stacks(){

    global $wpdb;
    $table = 'timeshow_events';
    $project_id = intval($_POST['project_id']);
    $stack_id = 1;
    $events = get_timeshow_events($project_id,$stack_id);
    // $stack_actions = get_stack_actions(1);
    // $stack_actions_data = stack_actions_row($stack_actions);
    $events_data = timeshow_events_row($events);
    // $stack = $wpdb->get_row( "SELECT stacks_index_id FROM $timeshow_stacks_table WHERE stack_id = $project_id" );

    $exports = get_timeshow_exports($project_id);
    // $get_stacks = timeshow_get_stacks($project_id);
    // $data_stacks = stack_row($get_stacks);

    $result['data'] = $events_data;
    $result['data_exports'] = timeshow_exports_row($exports);
    // $result['data_stacks'] = $data_stacks;
    $result['stack_id'] = $stack_id;
    echo json_encode($result, true);
  
    // show_timeshow_projects($projects);

    wp_die();
    
} //endfunction
add_action('wp_ajax_get_timeshow_events_stacks', 'get_timeshow_events_stacks');
add_action('wp_ajax_nopriv_get_timeshow_events_stacks', 'get_timeshow_events_stacks');


function save_stack(){

    global $wpdb;
    $table = 'timeshow_stacks';
    $project_id = intval($_POST['project_id']);
    $stack_id = intval($_POST['stack_id']);
    $stack_number = intval($_POST['stack_number']);
    $stack_color = trim($_POST['stack_color']);
    $stack_name = trim($_POST['stack_name']);

    $stack = $wpdb->get_results( "SELECT * FROM $table WHERE stack_id = '$stack_number' AND stack_lable = '$stack_name' AND stack_color = '$stack_color'" );
    if ($stack_id) {
        $stack_number_id_test = $wpdb->get_row( "SELECT stacks_index_id FROM $table WHERE timeshow_id = $project_id AND stack_id = $stack_number" );
        $stack_color_test = $wpdb->get_row( "SELECT stacks_index_id FROM $table WHERE timeshow_id = $project_id AND stack_color = '$stack_color'" );
        $stack_name_test = $wpdb->get_row( "SELECT stacks_index_id FROM $table WHERE timeshow_id = $project_id AND stack_lable = '$stack_name'" );
       
        if($stack_color_test){
            if($stack_color_test->stacks_index_id != $stack_id){
                $result['error'] = 'The color must be unique.';
                echo json_encode($result, true);

                wp_die();
            }   
        }
        if($stack_number_id_test){
          
            if($stack_number_id_test->stacks_index_id != $stack_id){

                $result['error'] = 'The number must be unique.';
                echo json_encode($result, true);

                wp_die();
            }   
        }
        if($stack_name_test){
            if($stack_name_test->stacks_index_id != $stack_id){
                $result['error'] = 'The name must be unique.';
                echo json_encode($result, true);

                wp_die();
            }   
        }
    }
    

    $stack_count = 1;
    if(is_countable($stack)){
        $stack_count = count($stack);
    }
    if($stack_count > 1){
        if($stack->stacks_index_id == $stack_id){
            $wpdb->update( $table, array( 
                'stack_id' => $stack_number,
                'stack_type' => trim($_POST['stack_type']),
                'stack_lable' => $stack_name,
                'stack_color' => $stack_color,

            ),array('stacks_index_id'=>intval($_POST['stack_id'])));

            $get_stacks = timeshow_get_stacks($project_id);
            $data = stack_row($get_stacks);

            $result['stack_id'] = $stack_id;
            $result['data'] = $data;
        }else{
            $result['error'] = 'The name, number and color must be unique.';
        }
    }else{
        $wpdb->update( $table, array( 
            'stack_id' => $stack_number,
            'stack_type' => trim($_POST['stack_type']),
            'stack_lable' => $stack_name,
            'stack_color' => $stack_color,

        ),array('stacks_index_id'=>intval($_POST['stack_id'])));

        $get_stacks = timeshow_get_stacks($project_id);
        $data = stack_row($get_stacks);

        $result['stack_id'] = $stack_id;
        $result['data'] = $data;
    }

    
    echo json_encode($result, true);

    wp_die();
    
} //endfunction
add_action('wp_ajax_save_stack', 'save_stack');
add_action('wp_ajax_nopriv_save_stack', 'save_stack');

function move_to_actions(){

    global $wpdb;

    $actions_table = 'stack_actions';
    $events_table = 'timeshow_events';
    $current_user_id = get_current_user_id();
    $project_id = intval($_POST['project_id']) ?? 0;
    $event_id = intval($_POST['event_id']) ?? 0;
    $stack_id = intval($_POST['stack_id']);

    $event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $events_table WHERE event_index_id = $event_id" ) );
    $action = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $actions_table WHERE event_id = $event_id  AND stack_id = $stack_id" ) );
    
    $action_id = 0;
    if($event){
        if(!$action){
            $stack = $wpdb->get_row( "SELECT * FROM $table WHERE stacks_index_id = $stack_id" );
            if($stack->stack_type == 'Sequence'){
                $action_type = 'goto';
            }elseif($stack->stack_type == 'Time Range'){
                $action_type = 'TimeRange In';
            }
            $action = $wpdb->get_row( "SELECT * FROM $actions_table WHERE stack_id = $stack_id ORDER BY action_id DESC" );
            if($action){
                $action_id_int = intval($action->action_id);
                $action_id = $action_id_int+1;
                if($action_id > 20){
                    $action_id = 20;
                }
            }else{
                $action_id = 1;
            }
            

            $wpdb->insert($actions_table, array(
                'timeshow_id' => intval($_POST['project_id']),
                'stack_id' => $stack_id,
                'event_id' => intval($_POST['event_id']), 
                'time_stamp' => $event->time_stamp ?? '',
                'actions_lable' =>  $event->event_lable ?? '',
                'action_id' =>  $action_id,
                'actions_type' => $action_type,
                'action_value' => 0
            ));
            $action_id = $wpdb->insert_id;
        }
    }

    $wpdb->update( $events_table, array( 'status' => 0 ),array('event_index_id'=> $event_id ));

    $stack_actions = get_stack_actions($stack_id);
    $stack_actions_data = stack_actions_row($stack_actions,$stack_id);

    $timeshow_events = get_timeshow_events($project_id,$stack_id);
    $timeshow_events_data = timeshow_events_row($timeshow_events);

    $result['action_id'] = $action_id;
    $result['data'] = $stack_actions_data;
    $result['events_data'] = $timeshow_events_data;
    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_move_to_actions', 'move_to_actions');
add_action('wp_ajax_nopriv_move_to_actions', 'move_to_actions');

function move_all_sel_to_actions(){

    global $wpdb;

    $actions_table = 'stack_actions';
    $events_table = 'timeshow_events';
    $current_user_id = get_current_user_id();
    $project_id = intval($_POST['project_id']) ?? 0;
    $stack_id = intval($_POST['stack_id']);
    $events_ids = explode(",", $_POST['events_ids']);

    if($events_ids){
        foreach($events_ids as $event_id){
            $event_id = intval($event_id);
            $event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $events_table WHERE event_index_id = $event_id" ) );
            $action = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $actions_table WHERE event_id = $event_id AND stack_id = $stack_id" ) );


            $action_id = 0;
            if($event){
                if(!$action){

                    $stack = $wpdb->get_row( "SELECT * FROM $table WHERE stacks_index_id = $stack_id" );
                    if($stack->stack_type == 'Sequence'){
                        $action_type = 'goto';
                    }elseif($stack->stack_type == 'Time Range'){
                        $action_type = 'TimeRange In';
                    }
                    $action = $wpdb->get_row( "SELECT * FROM $actions_table WHERE stack_id = $stack_id ORDER BY action_id DESC" );
                    if($action){
                        $action_id_int = intval($action->action_id);
                        $action_id = $action_id_int+1;
                        if($action_id > 20){
                            $action_id = 20;
                        }
                    }else{
                        $action_id = 1;
                    }
                    

                    $wpdb->insert($actions_table, array(
                        'timeshow_id' => intval($_POST['project_id']),
                        'stack_id' => $stack_id,
                        'event_id' => $event_id, 
                        'time_stamp' => $event->time_stamp ?? '',
                        'actions_lable' =>  $event->event_lable ?? '',
                        'action_id' =>  $action_id,
                        'actions_type' => $action_type,
                        'action_value' => 0
                    ));
                    $action_id = $wpdb->insert_id;
            
                }
            }

            $wpdb->update( $events_table, array( 'status' => 0 ),array('event_index_id'=> $event_id ));
        }
    }
 
    $stack_actions = get_stack_actions($stack_id);
    $stack_actions_data = stack_actions_row($stack_actions,$stack_id);

    $timeshow_events = get_timeshow_events($project_id,$stack_id);
    $timeshow_events_data = timeshow_events_row($timeshow_events);

    $result['data'] = $stack_actions_data;
    $result['events_data'] = $timeshow_events_data;
    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_move_all_sel_to_actions', 'move_all_sel_to_actions');
add_action('wp_ajax_nopriv_move_all_sel_to_actions', 'move_all_sel_to_actions');

function delete_action(){

    global $wpdb;

    $actions_table = 'stack_actions';
    $events_table = 'timeshow_events';
    $action_id = intval($_POST['action_id']) ?? 0;
    $project_id = intval($_POST['project_id']) ?? 0;

    $stack_id = intval($_POST['stack_id']);

    $action = $wpdb->get_row( $wpdb->prepare( "SELECT event_id FROM $actions_table WHERE actions_index_id = $action_id" ) );
    $wpdb->update( $events_table, array( 'status' => 1 ),array('event_index_id'=> $action->event_id ));

    $wpdb->delete( $actions_table, array( 'actions_index_id' => $action_id ));

    

    $stack_actions = get_stack_actions($stack_id);
    $stack_actions_data = stack_actions_row($stack_actions,$stack_id);

    $timeshow_events = get_timeshow_events($project_id,$stack_id);
    $timeshow_events_data = timeshow_events_row($timeshow_events);

    $result['data'] = $stack_actions_data;
    $result['event_id'] = $action->event_id;
    $result['events_data'] = $timeshow_events_data;
    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_delete_action', 'delete_action');
add_action('wp_ajax_nopriv_delete_action', 'delete_action');

function delete_stack(){

    global $wpdb;

    $actions_table = 'timeshow_stacks';
    $stack_id = intval($_POST['stack_id']) ?? 0;
    $project_id = intval($_POST['project_id']);

    $wpdb->delete( $actions_table, array( 'stacks_index_id' => $stack_id ));

    $get_stacks = timeshow_get_stacks($project_id);
    $data = stack_row($get_stacks);

    $result['data'] = $data;
    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_delete_stack', 'delete_stack');
add_action('wp_ajax_nopriv_delete_stack', 'delete_stack');

function add_stack(){

    global $wpdb;

    $stack_table = 'timeshow_stacks';
    $timeshow_parameters_table = 'timeshow_parameters';
    
    $project_id = intval($_POST['project_id']);

    $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $timeshow_parameters_table WHERE timeshow_id = $project_id" ) );

    $project_name = '%'.$project->timeshow_name.'%';

    $stack_lable = trim($_POST['stack_name']);
    $stack_name_ch = $wpdb->get_row( "SELECT stack_lable FROM $stack_table WHERE stack_lable = '$stack_lable'");


    if($stack_name_ch){
        $stack_name = $wpdb->get_row( $wpdb->prepare( "SELECT stack_lable FROM $stack_table WHERE stack_lable LIKE %s ORDER BY stack_lable DESC", $project_name) );
        if($stack_name){
            $stack_name_arr = explode("_", $stack_name->stack_lable);
            $stack_name_index = intval($stack_name_arr[1]);
            $stack_name_index = $stack_name_index+1;
            $index = $stack_name_index ?? '';
            $stack_lable = $project->timeshow_name.'_'.$index;
        }else{
            $stack_lable = $project->timeshow_name;
        }
    }

    
    
    $color = trim($_POST['stack_color']);

    $stack_color = get_event_color($project_id,$color);
    

    $number = 1000;
    $stack_id = $wpdb->get_row( "SELECT stack_id FROM $stack_table WHERE timeshow_id = $project_id ORDER BY stack_id DESC" );
    $stack_id_num = intval($stack_id->stack_id);
    if($stack_id_num >= 1000){
        $number = $stack_id_num+1;
    }
   
    $wpdb->insert($stack_table, array(
                'timeshow_id' => $project_id,
                'stack_id' => $number,
                'stack_type' => trim($_POST['stack_type']) ?? 'Sequence', 
                'stack_lable' => $stack_lable,
                'stack_color' => $stack_color,
            ));
    $stack_id = $wpdb->insert_id;
    $get_stacks = timeshow_get_stacks($project_id);
    $data = stack_row($get_stacks);

    $result['data'] = $data;
    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_add_stack', 'add_stack');
add_action('wp_ajax_nopriv_add_stack', 'add_stack');

function load_stacks(){

    global $wpdb;
    $stacks_table = 'timeshow_stacks';
    $timeshow_parameters_table = 'timeshow_parameters';
    $events_table = 'timeshow_events';

    $project_id = intval($_POST['project_id']);

    $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $timeshow_parameters_table WHERE timeshow_id = $project_id" ) );
    $events = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $events_table WHERE timeshow_id = $project_id" ) );

    $project_name = $project->timeshow_name;

    add_new_stacks_by_colors($project_id,$project_name,$events);

    // $stack = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $stacks_table WHERE timeshow_id = $project_id" ) );
    // $color = '';
    // foreach($events as $event){
    //     if($event->event_color){
    //         $color = $event->event_color;
    //         break;
    //     }
    // }

    // if(!$stack){
    //     $wpdb->insert($stacks_table, array(
    //         'timeshow_id' => $project_id,
    //         'stack_id' => $stack_id,
    //         'stack_type' => 'Sequence', 
    //         'stack_lable' => $project->timeshow_name,
    //         'stack_id' =>  1000,
    //         'stack_color' =>  $color
    //     ));
    //     $stack_id = $wpdb->insert_id;
    // }

    $get_stacks = timeshow_get_stacks($project_id);
    $data = stack_row($get_stacks);

    $result['data'] = $data;
    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_load_stacks', 'load_stacks');
add_action('wp_ajax_nopriv_load_stacks', 'load_stacks');

function get_stack(){

    global $wpdb;
    $project_id = intval($_POST['project_id']);
    $stack_id = intval($_POST['stack_id']);
    $table = 'timeshow_stacks';

    $stack = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE stacks_index_id = $stack_id" ) );

    $stack_id = intval($stack->stacks_index_id);

    $stack_actions = get_stack_actions($stack_id);
    $data = stack_actions_row($stack_actions,$stack_id);
    $events = get_timeshow_events($project_id,$stack_id);
    $events_data = timeshow_events_row($events);

    $result['stack_name'] = $stack->stack_lable;
    $result['stack_type'] = $stack->stack_type;
    $result['stack_number'] = $stack->stack_id;
    $result['stack_color'] = $stack->stack_color;
    $result['data'] = $data;
    $result['events_data'] = $events_data;
    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_get_stack', 'get_stack');
add_action('wp_ajax_nopriv_get_stack', 'get_stack');

function save_action_item(){

    global $wpdb;
    $table = 'stack_actions';
    $stack_id = intval($_POST['stack_id']);
    $action_id = trim($_POST['action_id']);
    $actions_index_id = trim($_POST['actions_index_id']);
    $action = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE stack_id = $stack_id AND action_id = '$action_id'" ) );
    if($action){
        // if($action->actions_index_id == $actions_index_id){
            $wpdb->update( $table, array( 
                'action_id' => trim($_POST['action_id']),
                'actions_lable' => trim($_POST['actions_lable']),
                'actions_type' => trim($_POST['action_type']),
                'action_value' => trim($_POST['action_value'])
            ),array('actions_index_id'=>intval($_POST['actions_index_id'])));
            $result['status'] = 'ok';
        // }else{
        //     $result['error'] = 'The ID must be unique.';
        // }
        
    }else{
        $wpdb->update( $table, array( 
            'action_id' => trim($_POST['action_id']),
            'actions_lable' => trim($_POST['actions_lable']),
            'actions_type' => trim($_POST['action_type']),
            'action_value' => trim($_POST['action_value'])
        ),array('actions_index_id'=>intval($_POST['actions_index_id'])));
        $result['status'] = 'ok';
    }
    
    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_save_action_item', 'save_action_item');
add_action('wp_ajax_nopriv_save_action_item', 'save_action_item');

function udate_actions(){

    global $wpdb;
    $table = 'stack_actions';

    $stack_id = intval($_POST['stack_id']);
    $project_id = intval($_POST['project_id']);
    $actions_ids = explode(",", $_POST['actions_ids']);
    $select_field = trim($_POST['select_field']);
    $value = trim($_POST['bulk_action']);

    if($select_field == 'action_delete'){
        if($actions_ids){
            foreach($actions_ids as $action_id){
                $action_id = intval($action_id);
                $action = $wpdb->get_row( $wpdb->prepare( "SELECT actions_index_id FROM $table WHERE actions_index_id = $action_id" ) );

                if($action){
                    $wpdb->delete( $table, array( 'actions_index_id' => $action_id ));
                }
            }
        }
        $stack_actions = get_stack_actions($stack_id);
        $stack_actions_data = stack_actions_row($stack_actions,$stack_id);
        $timeshow_events = get_timeshow_events($project_id,$stack_id);
        $timeshow_events_data = timeshow_events_row($timeshow_events);

        $result['data'] = $stack_actions_data;
        $result['events_data'] = $timeshow_events_data;
        $result['status'] = 'ok';
        echo json_encode($result, true);
      
        wp_die();
    }

    if($actions_ids){
        foreach($actions_ids as $action_id){
            $action_id = intval($action_id);
            $action = $wpdb->get_row( $wpdb->prepare( "SELECT actions_index_id FROM $table WHERE actions_index_id = $action_id" ) );

            if($action){
                $wpdb->update( $table, array($select_field => $value),array('actions_index_id'=>$action_id));
            }
        }
    }
    $stack_actions = get_stack_actions($stack_id);
    $stack_actions_data = stack_actions_row($stack_actions,$stack_id);

    $result['data'] = $stack_actions_data;
    $result['status'] = 'ok';
    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_udate_actions', 'udate_actions');
add_action('wp_ajax_nopriv_udate_actions', 'udate_actions');

function timeshow_add_export(){

    global $wpdb;
    $table = 'timeshow_exports';

    $timeshow_parameters_table = 'timeshow_parameters';
    $timeshow_stacks = 'timeshow_stacks';

    $project_id = intval($_POST['project_id']) ?? 0;
    $stack_id = intval($_POST['stack_id']) ?? 0;

    $user_id = get_current_user_id();

    $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $timeshow_parameters_table WHERE timeshow_id = $project_id" ) );
    $stack = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $timeshow_stacks WHERE stacks_index_id = $stack_id" ) );

    // $CmdEvents = [];
    // $events = get_timeshow_events($project_id,$stack_id);
    // foreach($events as $event){
    //     $ev['Name'] = $event->event_lable;
    //     $ev['Time'] = $event->time_stamp;
    //     $ev['CueDestination'] = $event->event_id;
    //     array_push($CmdEvents,$ev);
    // }

    // $Hex = generateHexBytes();

    // $TimeRange = array(
    //     'Start' => "0.000",
    //     'End' => "",
    //     'Name' => $stack->stack_lable,
    //     'Action' => "Goto",
    //     'Color' => $stack->stack_color,
    // );

    // $GMA3['GMA3']['Timecode']['Name'] = trim($_POST['export_lable']) ? $_POST['export_lable'] : $project->timeshow_name;
    // $GMA3['GMA3']['Timecode']['Guid'] = $Hex;
    // $GMA3['GMA3']['Timecode']['Duration'] = "10.000";
    // $GMA3['GMA3']['DataVersion'] = '2.1.1.5';
    // // $GMA3['GMA3']['Timecode']['TrackGroup']['MarkerTrack']['_Name'] = $stack->stack_lable;
    // $GMA3['GMA3']['Timecode']['Name'] = $stack->stack_lable;
    // $GMA3['GMA3']['Timecode']['Guid'] = $Hex;
    // $GMA3['GMA3']['Timecode']['Play'] = '';
    // $GMA3['GMA3']['Timecode']['Rec'] = '';
    // $GMA3['GMA3']['Timecode']['TrackGroup']['Name'] = '';
    // $GMA3['GMA3']['Timecode']['TrackGroup']['Guid'] = '';
    // $GMA3['GMA3']['Timecode']['TrackGroup']['MarkerTrack']['Name'] = 'Marker';
    // $GMA3['GMA3']['Timecode']['TrackGroup']['MarkerTrack']['Guid'] = $Hex;
    // $GMA3['GMA3']['Timecode']['TrackGroup']['MarkerTrack']['Track']['Name'] = 'MainTrack';
    // $GMA3['GMA3']['Timecode']['TrackGroup']['MarkerTrack']['Track']['Guid'] = $Hex;
    // $GMA3['GMA3']['Timecode']['TrackGroup']['MarkerTrack']['Track']['TimeRange'] = $TimeRange;
    // $GMA3['GMA3']['Timecode']['TrackGroup']['MarkerTrack']['Track']['TimeRange']['CmdSubTrack']['CmdEvent'] = $CmdEvents;
    // if($project->audio_file_url){

    //     $file_name = basename($project->audio_file_url);  
        
    //     $file_path = TIME_DIR.'/uploads/'.$file_name;
    //     $file_contents = file_get_contents($file_path);
    //     $base64_audio = base64_encode($file_contents);

    //     $GMA3['GMA3']['Timecode']['AudioFile']['Name'] = $project->audio_file_lable;
    //     $GMA3['GMA3']['Timecode']['AudioFile']['Base64'] = $base64_audio;

        
    //     $nfile_name = time()."_GMA3.json";


    // }
    // $nfile_name = time()."_GMA3.json";
    // $file_content = json_encode($GMA3, true);
    // $output_file = TIME_DIR.'/uploads/ai_docs/'.$nfile_name;
    // file_put_contents($output_file, $file_content);

    // $nfile_name_xml = time()."_GMA3.xml";
    // $output_file_xml = TIME_DIR.'/uploads/ai_docs/'.$nfile_name_xml;
    // $xmlOutput = jsonToXml($file_content);
    // file_put_contents($output_file_xml, $xmlOutput->asXML());
    // $xml_url = TIME_URL.'uploads/ai_docs/'.$nfile_name_xml;
    // // $GMA3['GMA3']['Timecode']['TrackGroup']['MarkerTrack']['TimeRange'] = $TimeRange;

    // $archive_url = add_to_archive($output_file_xml, $project->timeshow_name);

    $export_lable = trim($_POST['export_lable']) ? $_POST['export_lable'] : $project->timeshow_name;

    $project_json_file = export_timeshow_project_json_file($project_id);
    $zip_converter = send_json_to_api($export_lable ,$project_json_file['file'], $user_id);
    if($zip_converter['success'] === true){
        $zip_converter = TIME_URL. '/uploads/conv/user_'.$user_id.'/'.$export_lable.'.zip';
        $archive_url = $zip_converter;
    }

    $wpdb->insert($table, array(
                'timeshow_id' => $project_id,
                'export_lable' => $export_lable,
                'timeshow_number' => intval($_POST['timeshow_number']) ?? 1000, 
                'export_status' => trim($_POST['export_status']) ?? '',
                'user_name' => $project->user_name ?? '',
                'export_type' => $project->input_type ?? '',
                'export_url' => $archive_url ?? '',
                'user_id' => $project->user_id ?? '',
            ));
    $stack_id = $wpdb->insert_id;



    $exports = get_timeshow_exports($project_id);
    $result['data'] = timeshow_exports_row($exports);
    $result['project_json_file'] = $project_json_file;
    $result['zip_converter'] = $zip_converter;
    // $result['ai_get'] = ai_get_func($output_file);

    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_timeshow_add_export', 'timeshow_add_export');
add_action('wp_ajax_nopriv_timeshow_add_export', 'timeshow_add_export');

function timeshow_copy_export(){

    global $wpdb;
    $table = 'timeshow_exports';

    $timeshow_parameters_table = 'timeshow_parameters';
    $project_id = intval($_POST['project_id']) ?? 0;

    $project_export = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $timeshow_parameters_table WHERE export_index_id = $project_id" ) );

    $wpdb->insert($table, array(
                'timeshow_id' => $project_id,
                'export_lable' => trim($_POST['export_lable']) ? $_POST['export_lable'] : $project->timeshow_name,
                'timeshow_number' => intval($_POST['timeshow_number']) ?? 1000, 
                'export_status' => trim($_POST['export_status']) ?? '',
                'user_name' => $project->user_name ?? '',
                'export_type' => $project->input_type ?? '',
                'export_url' => $project->input_table_url ?? '',
                'user_id' => $project->user_id ?? '',
            ));
    $stack_id = $wpdb->insert_id;

    $exports = get_timeshow_exports($project_id);
    $result['data'] = timeshow_exports_row($exports);

    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_timeshow_copy_export', 'timeshow_copy_export');
add_action('wp_ajax_nopriv_timeshow_copy_export', 'timeshow_copy_export');

function update_action_menu(){
    $stack_type = trim($_POST['stack_type']) ?? '';
    $select_options = get_select_action_type($stack_type);
    $result['data'] = $select_options;

    echo json_encode($result, true);
  
    wp_die();
    
} //endfunction
add_action('wp_ajax_update_action_menu', 'update_action_menu');
add_action('wp_ajax_nopriv_update_action_menu', 'update_action_menu');

function save_timeshow_project_gen_audio_events(){

    global $wpdb;
    $table = 'timeshow_parameters';

    $result['project_id'] = $_POST['project_id'];
    $result['timeshow_name'] = $_POST['timeshow_name'];
    $result['song_lable'] = $_POST['song_lable'];
    $result['artist'] = $_POST['artist'];
    $result['audio_file_lable'] = $_POST['audio_file_lable'];
    $result['upload_time'] = $_POST['upload_time'];
    $result['genre'] = $_POST['genre'];
    $result['speed'] = $_POST['speed'];
    $result['length'] = $_POST['length'];

    $result['audio_file_path'] = analyze_audio_file_from_project_id(intval($_POST['project_id']));

    $wpdb->update( $table, array( 
        'timeshow_name' => trim($_POST['timeshow_name']),
        'song_lable' => $result['audio_file_path']['song_label'] ?? '',
        'artist' => $result['audio_file_path']['artist'] ?? '',
        'audio_file_lable' => $result['audio_file_path']['song_label'] ?? '',
        'upload_time' => date('Y-m-d H:i:s'),
        'genre' => $result['audio_file_path']['genre'] ?? '',
        'speed' => $result['audio_file_path']['speed'][0] ?? '',
        'length' => $result['audio_file_path']['length'] ?? ''

    ),array('timeshow_id'=>intval($_POST['project_id'])));

    echo json_encode($result, true);
  
    // show_timeshow_projects($projects);

    wp_die();
    
} //endfunction
add_action('wp_ajax_save_timeshow_project_gen_audio_events', 'save_timeshow_project_gen_audio_events');
add_action('wp_ajax_nopriv_save_timeshow_project_gen_audio_events', 'save_timeshow_project_gen_audio_events');

// function ai_upload_json_callback() {
//     check_ajax_referer('ai_upload_nonce', 'security');

//     if (!current_user_can('manage_options')) {
//         wp_send_json_error('No rights');
//     }

//     if (empty($_FILES['json_file']['tmp_name']) && empty($_POST['skip_file'])) {
//         wp_send_json_error('File not uploaded');
//     }

//     // If the file has arrived (only on the first request)
//     if (!empty($_FILES['json_file']['tmp_name'])) {
//         $ext = pathinfo($_FILES['json_file']['name'], PATHINFO_EXTENSION);
//         if (strtolower($ext) !== 'json') {
//             wp_send_json_error('Only JSON files are allowed');
//         }

//         $upload_dir = wp_upload_dir();
//         $target_dir = $upload_dir['basedir'] . '/ai_docs/';
//         if (!file_exists($target_dir)) {
//             wp_mkdir_p($target_dir);
//         }

//         $target_file = $target_dir . 'ai_data.json';

//         if (!move_uploaded_file($_FILES['json_file']['tmp_name'], $target_file)) {
//             wp_send_json_error('Failed to save file');
//         }
//     } else {
//         // If the second request - the file already exists
//         $upload_dir = wp_upload_dir();
//         $target_file = $upload_dir['basedir'] . '/ai_docs/ai_data.json';
//     }

//     // here we pass the type
//     $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';

//     $result = ai_get_func($target_file, $type); // Here we pass the file data and type

//     wp_send_json_success($result);
// }
// add_action('wp_ajax_ai_upload_json', 'ai_upload_json_callback');


add_action('wp_ajax_ai_upload_json', function() {
    // check_ajax_referer('ai_ajax_nonce', 'security');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('No rights');
    }

    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';

    $upload_dir = wp_upload_dir();
    $target_dir = $upload_dir['basedir'] . '/ai_docs/';
    if (!file_exists($target_dir)) wp_mkdir_p($target_dir);

    $target_file = $target_dir . 'ai_data.json';

    // зберігаємо файл, якщо він прийшов
    if (!empty($_FILES['json_file']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['json_file']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'json') wp_send_json_error('Only JSON files are allowed');

        if (!move_uploaded_file($_FILES['json_file']['tmp_name'], $target_file)) {
            wp_send_json_error('Failed to save file');
        }
    }

    // унікальний task_id
    $task_id = uniqid();
    $task_file = sys_get_temp_dir() . "/task_{$task_id}.json";

    // записуємо статус "processing"
    file_put_contents($task_file, json_encode([
        'status' => 'processing',
        'type' => $type,
        'started_at' => time()
    ]));

    // запускаємо CLI воркер асинхронно
    $cmd = "php " . TIME_DIR . "/workers/long_ai_task.php " 
            . escapeshellarg($target_file) . " " 
            . escapeshellarg($task_file) . " " 
            . escapeshellarg($type) 
            . " > " . TIME_DIR . "/workers/worker.log 2>&1 &";
    exec($cmd);

    wp_send_json_success(['task_id' => $task_id]);
});


// AJAX для перевірки статусу
add_action('wp_ajax_ai_check_task', function() {
    // check_ajax_referer('ai_ajax_nonce', 'security');

    $task_id = sanitize_text_field($_POST['task_id']);
    $task_file = sys_get_temp_dir() . "/task_{$task_id}.json";

    if (!file_exists($task_file)) {
        wp_send_json_error('Task not found');
    }

    $content = json_decode(file_get_contents($task_file), true);
    if (!$content) {
        wp_send_json_error('Invalid task file');
    }

    if ($content['status'] === 'done') {
        unlink($task_file);
        wp_send_json_success([
            'status'  => 'done',
            'result'  => $content['result'],
        ]);
    }

    elseif (!empty($content['error']) || $content['status'] === 'error' || !empty($content['result']) && is_string($content['result'])) {
        $error_msg = $content['error'] ?? $content['result'] ?? 'Unknown error';
        wp_send_json_success([
            'status'  => 'error',
            'error'   => $error_msg,
        ]);
    }

    else {
        wp_send_json_success([
            'status'  => 'processing',
        ]);
    }
});

add_action('wp_ajax_timeshow_create_project_worker', function() {

    $projects_name = trim($_POST['projects_name']);

    $time = time();

    $input_table_upload_url = TIME_DIR.'/uploads/'.$time.'_'.$_FILES["file"]['name'];
    $audio_file_upload_url = TIME_DIR.'/uploads/'.$time.'_'.$_FILES["mediafile"]['name'];


    if ( 0 < $files['file']['error'] ) {
        // echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        $check = move_uploaded_file($_FILES['file']['tmp_name'], $input_table_upload_url);
        if($check){
            $file_name_ex = explode(".", $_FILES["file"]['name']);
            $file_lable = $file_name_ex[0];
            $file_url = TIME_URL.'uploads/'.$time.'_'.$_FILES["file"]['name'];
        }else{
            $input_table_upload_url = null;
        }        
    }

    if ( 0 < $files['mediafile']['error'] ) {
        // echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        $check = move_uploaded_file($_FILES['mediafile']['tmp_name'], $audio_file_upload_url);
        if($check){
            $file_name_ex = explode(".", $_FILES["mediafile"]['name']);
            $music_lable = $file_name_ex[0];
            $music_url = TIME_URL.'uploads/'.$time.'_'.$_FILES["mediafile"]['name'];
        }else{
            $audio_file_upload_url = null;
        }        
    }

    // унікальний task_id
    $task_id = uniqid();
    $task_file = sys_get_temp_dir() . "/task_{$task_id}.json";

    // записуємо статус "processing"
    file_put_contents($task_file, json_encode([
        'status' => 'processing',
        'started_at' => time()
    ]));

    $current_user_id = get_current_user_id();

    // запускаємо CLI воркер асинхронно
    $cmd = "php " . TIME_DIR . "/workers/timeshow_worker.php " 
            . escapeshellarg($task_file) . " " 
            . escapeshellarg($projects_name) . " "
            . escapeshellarg($input_table_upload_url) . " "
            . escapeshellarg($audio_file_upload_url) . " "
            . escapeshellarg($current_user_id) . " "
            . escapeshellarg($file_lable) . " "
            . escapeshellarg($file_url) . " "
            . escapeshellarg($music_lable) . " "
            . escapeshellarg($music_url)
            . " > " . TIME_DIR . "/workers/timeshow_worker.log 2>&1 &";
    exec($cmd);

    wp_send_json_success(['task_id' => $task_id]);
});