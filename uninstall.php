<?php 
if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit();

global $wpdb;
$table = $wpdb->prefix . 'cfeedbacks';
$wpdb->query( "DROP TABLE $table" );

?>