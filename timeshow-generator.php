<?php

/**
 * Plugin Name: Timeshow-generator
 * Description: 
 * Version: 0.0.1
 */

define('TIME_DIR', __DIR__);
// define('TIME_URL', plugins_url().'/timeshow-generator/');
define('TIME_URL', preg_replace('/^http:/i', 'https:', plugins_url() . '/timeshow-generator/'));


require (TIME_DIR.'/includes/timeshow-functions.php');
// require (TIME_DIR.'/includes/download.php');