<?php
// Аргументи CLI
$target_file = $argv[1] ?? '';
$task_file   = $argv[2] ?? '';
$type        = $argv[3] ?? 'timecode';

if (!$target_file || !$task_file || !$type) {
    file_put_contents(__DIR__ . '/worker_error.log', "Missing arguments\n", FILE_APPEND);
    exit("Missing arguments");
}

// Підключаємо WordPress
$wp_root = dirname(__DIR__, 4); // піднімаємося 4 рівні до кореня WP
$wp_load = $wp_root . '/wp-load.php';

if (!file_exists($wp_load)) {
    file_put_contents(__DIR__ . '/worker_error.log', "wp-load.php not found at $wp_load\n", FILE_APPEND);
    exit("Cannot find wp-load.php");
}

require_once $wp_load;

// Викликаємо стару функцію
$result = ai_get_func($target_file, $type);

// Записуємо результат у JSON
file_put_contents($task_file, json_encode([
    'status'      => 'done',
    'type'        => $type,
    'result'      => $result,
    'finished_at' => time()
]));

