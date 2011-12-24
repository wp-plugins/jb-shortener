<?php
/*
Plugin Name: JB Shortener
Plugin URI: http://joshbetz.com/2011/11/jb-shortener/
Description: Changes the WordPress shorturl and Twitter Tools URL based on a base-36 encode of the post ID. Also includes materials to setup custom shorturl domain.
Version: 0.9
Author: Josh Betz
Author URI: http://joshbetz.com
*/

add_filter('get_shortlink', 'jb_shortlinks',10,3);
function jb_shortlinks() {
  global $post;
  $id = base36($post->ID);
  $shorturl = get_option('jb_shorturl');
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
  $shorturl = get_option('jb_shorturl');
  return "$shorturl/$id";
}

add_action('admin_init', 'jb_shorturl_settings');
function jb_shorturl_settings() {
  add_settings_field('jb_shorturl', "Short URL", "jb_settings_callback", "general", "default", array("label_for"=>"jb_shorturl"));
  register_setting('general','jb_shorturl', 'esc_url');
}
function jb_settings_callback() {
  echo '<input name="jb_shorturl" id="jb_shorturl" type="text" value="'.get_jb_shorturl().'" class="code regular-text"> <span class="description">The custom short url for your site</span>';
}

/* UTILITY */
function base36($number) {
  return base_convert($number, 10, 36);
}

add_action('init', 'jb_redirect');
function jb_redirect() {
  global $wpdb;
  
	$jb_domain = $wpdb->escape( $_SERVER[ 'HTTP_HOST' ] );
	$jb_domain = "http://" . $jb_domain;

	if( $jb_domain == get_option('jb_shorturl') ) {
	  $domain = get_option('siteurl');
	  $token = trim($wpdb->escape( $_SERVER['REQUEST_URI'] ), '/');
  
	  if (!empty($token)) {
	    $id = base_convert(strip_tags($token), 36, 10);
			$permalink = get_permalink($id);
	    if($permalink) {
	      wp_redirect("$permalink", 301);
	      exit();
	    }
	  } else {
	    wp_redirect("$domain/", 301);
	    exit();
	  }
	  
    wp_redirect("$domain/", 302);
    exit();
	}
}

if( is_multisite() ) {
  add_action('admin_init', 'jb_maybe_create_db');
  function jb_maybe_create_db() {
  	global $wpdb;

  	$wpdb->jbtable = $wpdb->base_prefix . 'jb_shortlinks';
  	if ( is_super_admin() || is_site_admin() ) {
  		$created = 0;
  		if ( $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->jbtable}'") != $wpdb->jbtable ) {
  			$wpdb->query( "CREATE TABLE IF NOT EXISTS `{$wpdb->jbtable}` (
  				`id` bigint(20) NOT NULL auto_increment,
  				`blog_id` bigint(20) NOT NULL,
  				`domain` varchar(255) NOT NULL,
  				PRIMARY KEY  (`id`),
  				KEY `blog_id` (`blog_id`,`domain`)
  			);" );
  			$created = 1;
  		}
  		if ( $created ) {
  			?> <div id="message" class="updated fade"><p><strong>Shortlink database table created</strong></p></div> <?php
  		}
  	}
  }
}

function get_jb_shorturl() {
	global $wpdb, $blog_id;
	
	if(!is_multisite()) {
		return get_option("jb_shorturl");
	}
	
	$wpdb->jbtable = $wpdb->base_prefix . 'jb_shortlinks';
	$where = $wpdb->prepare( 'blog_id = %s', $blog_id );
	
	if( get_option('jb_shorturl') == $wpdb->get_var( "SELECT domain FROM {$wpdb->jbtable} WHERE {$where} ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1" ) ) {
		return get_option('jb_shorturl');
	} else {
		update_jb_shorturl(get_option('jb_shorturl'));
	}
}

function update_jb_shorturl($domain) {
  global $wpdb, $blog_id;
  
	$wpdb->jbtable = $wpdb->base_prefix . 'jb_shortlinks';
  $where = $wpdb->prepare( 'blog_id = %s', $blog_id );  
  
  if( $id = $wpdb->get_var( "SELECT id FROM {$wpdb->jbtable} WHERE {$where} ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1") ) {
    $wpdb->update($wpdb->jbtable, array(
      'domain' => $domain
    ), array(
     'id' => $id      
    ), '%s', '%d');
  } else {
		$wpdb->insert($wpdb->jbtable, array(
			'blog_id' => $blog_id,
			'domain' => $domain
		), array(
			'%d',
			'%s'
		));
  }
}

?>
