<?php
    
    if ( !defined('ABSPATH') )
        define('ABSPATH', str_replace( '\\', '/', dirname(__FILE__) . '/' ) );
    
    $action             =   isset($_GET['action'])              ?   filter_var ( $_GET['action'],               FILTER_SANITIZE_STRING)         :   '';
    $file_path          =   isset($_GET['file_path'])           ?   filter_var ( $_GET['file_path'],            FILTER_SANITIZE_STRING)         :   '';
    $blog_id            =   isset($_GET['blog_id'])             ?   filter_var ( $_GET['blog_id'],              FILTER_VALIDATE_INT)            :   '';
    
    if(empty($action)   ||  empty($file_path)   ||  empty($blog_id))
        die();
    
    include_once('class.file-processor.php');
        
    $WPH_FileProcess  =   new WPH_File_Processor( $action, $file_path, $blog_id );
    $WPH_FileProcess->run();    

?>