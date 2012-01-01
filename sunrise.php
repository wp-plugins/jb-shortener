<?php
if ( !defined( 'SUNRISE_LOADED' ) )
	define( 'SUNRISE_LOADED', 1 );

// let the site admin page catch the VHOST == 'no'
$wpdb->jbtable = $wpdb->base_prefix . 'jb_shortlinks';
$jb_domain = "http://".$wpdb->escape( $_SERVER[ 'HTTP_HOST' ] );

$where = $wpdb->prepare( 'domain = %s', $jb_domain );

$wpdb->suppress_errors();
$jb_short_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->jbtable} WHERE {$where} ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1" );
$wpdb->suppress_errors( false );
if( $jb_short_id ) {
	$current_blog = $wpdb->get_row("SELECT * FROM {$wpdb->blogs} WHERE blog_id = '$jb_short_id' LIMIT 1");
  
	$domain = $current_blog->domain;
  $token = trim($_SERVER['REQUEST_URI'], '/');
  
  if (!empty($token)) {
    $id = base_convert(strip_tags($token), 36, 10);
    if($id) {
      header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
      header("Location: http://$domain/?p=$id");
      exit();
    }
  } else {
    header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
    header("Location: http://$domain/");
    exit();
  }
	
  header($_SERVER['SERVER_PROTOCOL'].' 302 Moved Temporarily');
  header("Location: http://$domain/");
  exit();
	
} else {
  if(file_exists(dirname(__FILE__).'/dm_sunrise.php'))
	  include('dm_sunrise.php');
}
?>