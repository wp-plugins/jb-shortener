<?php
/*
Plugin Name: JB Shortener
Plugin URI: http://joshbetz.com/2011/11/jb-shortener/
Description: Changes the WordPress shorturl and Twitter Tools URL based on a base-36 encode of the post ID. Also includes materials to setup custom shorturl domain.
Version: 0.4
Author: Josh Betz
Author URI: http://joshbetz.com
*/

add_filter('get_shortlink', 'jb_shortlinks',10,3);
function jb_shortlinks() {
  global $post;
  $id = base36($post->ID);
  $shorturl = get_option("jb_shorturl");
  return "$shorturl/$id";
}

add_filter('tweet_blog_post_url', 'jb_shortenter');
function jb_shortenter($url) {
  $parts = explode('/',$url);
  $count = count($parts);
  $slug = $parts[$count-1];
  $args=array(
    'name' => $slug_to_get,
    'post_type' => 'post',
    'post_status' => 'publish',
    'showposts' => 1,
    'caller_get_posts'=> 1
  );
  $my_posts = get_posts($args);
  $id = base36($my_posts[0]->ID);
  $shorturl = get_option("jb_shorturl");
  return "$shorturl/$id";
}

add_action('admin_init', 'jb_shorturl_settings');
function jb_shorturl_settings() {
  add_settings_field('jb_shorturl', "Short URL", "jb_settings_callback", "general", "default", array("label_for"=>"jb_shorturl"));
  register_setting('general','jb_shorturl', 'esc_url');
}
function jb_settings_callback() {
  echo '<input name="jb_shorturl" id="jb_shorturl" type="text" value="'.get_option("jb_shorturl").'" class="code regular-text"> <span class="description">The custom short url for your site</span>';
}

/* UTILITY */
function base36($number) {
  return base_convert($number, 10, 36);
}

?>