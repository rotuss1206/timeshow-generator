<?php

$task_file = $argv[1] ?? '';
$projects_name = $argv[2] ?? '';
$input_table_upload_url = $argv[3] ?? '';
$audio_file_upload_url = $argv[4] ?? '';
$current_user_id = $argv[5] ?? '';
$file_lable = $argv[6] ?? '';
$file_url = $argv[7] ?? '';
$music_lable = $argv[8] ?? '';
$music_url = $argv[9] ?? '';


// Підключаємо WordPress
$wp_root = dirname(__DIR__, 4); // піднімаємося 4 рівні до кореня WP
$wp_load = $wp_root . '/wp-load.php';

if (!file_exists($wp_load)) {
    file_put_contents(__DIR__ . '/worker_error.log', "wp-load.php not found at $wp_load\n", FILE_APPEND);
    exit("Cannot find wp-load.php");
}
require_once $wp_load;

global $wpdb;
$table = 'timeshow_parameters';
$stacks_table = 'timeshow_stacks';
$events_table = 'timeshow_events';


$first_name = get_user_meta( $current_user_id, 'first_name', true );
$last_name = get_user_meta( $current_user_id, 'last_name', true );



$wpdb->insert($table, array(
    'timeshow_name' => $projects_name,
    'input_table_url' => $file_url ?? '',
    'input_table_lable' => $file_lable ?? '', 
    'audio_file_url' => $music_url ?? '',
    'audio_file_lable' =>  $music_lable ?? '',
    'user_name' => $first_name.' '.$last_name,
    'user_id' => $current_user_id
));
$timeshow_id = $wpdb->insert_id;

parse_events($timeshow_id,$input_table_upload_url);

if ( !empty($audio_file_upload_url) ) {
    $liblosa = analyze_audio_and_save_events($timeshow_id,$task_file);  
    if ( is_wp_error($liblosa) ) {
        $wpdb->delete( $table, array( 'timeshow_id' => $timeshow_id ));
        $error_message = $liblosa->get_error_message();
        file_put_contents($task_file, json_encode(['status' => 'error', 'result' => $error_message]));
        wp_send_json_error(array('error' => $error_message));
        wp_die();
    }
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


$events = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $events_table WHERE timeshow_id = $timeshow_id" ) );

$stacks = add_new_stacks_by_colors($timeshow_id,$projects_name,$events);

$projects = get_timeshow_projects($current_user_id);
$result['data'] = timeshow_projects_row($projects);
$result['liblosa'] = $liblosa;
$result['insert_id'] = $timeshow_id;
$result['stacks'] = $stacks;
// $json = json_encode($result, true);
  
// show_timeshow_projects($projects);

file_put_contents($task_file, json_encode(['status' => 'done', 'result' => $result]));