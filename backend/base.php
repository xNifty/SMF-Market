<?php
//DO NOT DISPLAY ERRORS TO USER
ini_set("display_errors", 0);
ini_set("log_errors", 1);
error_reporting(E_NONE);

//Define where do you want the log to go, syslog or a file of your liking with
ini_set("error_log", dirname(__FILE__).'/php_errors.log');

register_shutdown_function(function(){
    $last_error = error_get_last();
    if ( !empty($last_error) && 
         $last_error['type'] & (E_ERROR | E_COMPILE_ERROR | E_PARSE | E_CORE_ERROR | E_USER_ERROR)
       )
    {
       require_once(dirname(__FILE__).'/public_html/maintenance.php');
       exit(1);
    }
});
?>