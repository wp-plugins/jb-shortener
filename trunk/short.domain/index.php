<?php

$domain = "joshbetz.com";

if (!empty($_GET['token'])) {
  $id = base_convert(strip_tags($_GET['token']), 36, 10);
  if(is_numeric($id)) {
    header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
    header("Location:http://$domain/?p=$id");
    exit();
  }
} else {
  header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
  header("Location:http://$domain/");
  exit();
}

header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
header('Status:404');
die('404 Not Found');

?>