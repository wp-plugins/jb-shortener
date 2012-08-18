<?php
/*
Plugin Name: JB Shortener
Plugin URI: http://joshbetz.com/2011/11/jb-shortener/
Description: Changes the WordPress shorturl and Twitter Tools URL based on a base-36 encode of the post ID. Also includes materials to setup custom shorturl domain.
Version: 1.1
Author: Josh Betz
Author URI: http://joshbetz.com
*/

class JB_Shortlinks {

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'jb_redirect' ), 1 );
		add_action( 'admin_init', array( $this, 'jb_shorturl_settings' ) );
		if ( is_multisite() ) {
			register_activation_hook( __FILE__, array( $this, 'jb_maybe_create_db' ) );
			register_deactivation_hook( __FILE__, array( $this, 'jb_delete_db_entry' ) );
		}
	}

	function init() {
		// Replace core shortlinks with JB shortlinks
		add_filter( 'get_shortlink', array( $this, 'jb_shortlink' ), 10, 4 );

		// Get the short URL for Twitter Tools
		add_filter( 'tweet_blog_post_url', array( $this, 'jb_shortenter' ) );
	}

	/**
	 * Redirect to the correct page based on the short url
	 */
	function jb_redirect() {
		$jb_domain = esc_url( $_SERVER[ 'HTTP_HOST' ] );

		if( $jb_domain == get_option( 'jb_shorturl' ) ) {
			$token = trim( esc_url( $_SERVER[ 'REQUEST_URI' ] ), '/' );
			$domain = esc_url( get_option( 'siteurl' ) );

			if ( ! empty( $token ) ) {
				$id = intval( $token, 36 );
				$permalink = get_permalink( $id );

				if( $permalink ) {
					// Redirect to the permalink
					wp_safe_redirect( $permalink, 301 );
				} else {
					// No post with that ID exists, go home
					wp_safe_redirect( $domain, 302 );
				}
			}
			else
				wp_safe_redirect( $domain, 301 );

			exit;
		}
	}

	function jb_shortlink( $shortlink, $id, $context, $allowslugs ) {
		return esc_url( get_option( 'jb_shorturl' ) ) . '/' . self::base36( $id );
	}

	function jb_shortenter( $url ) {
		$parts = explode( '/', $url );
		$slug = $parts[ count( $parts ) - 1 ];

		$args = array(
			'name' => $slug,
			'post_type' => 'post',
			'post_status' => 'publish',
			'numberposts' => 1
		);

		$post = get_posts( $args );
		$id = self::base36( $post[0]->ID );
		$shorturl = esc_url( get_option( 'jb_shorturl' ) );

		return "$shorturl/$id";
	}

	/**
	 * Add our short domain setting to the General settings page
	 */
	function jb_shorturl_settings() {
		add_settings_field( 'jb_shorturl', __( 'Short URL' ), array( $this, 'jb_settings_callback' ), 'general', 'default', array( 'label_for' => 'jb_shorturl' ) );
		register_setting( 'general', 'jb_shorturl', 'esc_url' );
	}

	/**
	 * Output a field to define the short domain
	 */
	function jb_settings_callback() {
		echo '<input name="jb_shorturl" id="jb_shorturl" type="text" value="' . $this->get_jb_shorturl() . '" class="code regular-text"> <span class="description">The custom short url for your site</span>';
	}

	/**
	 * Get the short url
	 *
	 * If we're in mulisite, look it up in the extra database table.
	 * If we're not in multisite, just use get_option
	 */
	function get_jb_shorturl() {
		global $wpdb, $blog_id;
		
		if( ! is_multisite() )
			return get_option( "jb_shorturl" );
		
		$wpdb->jbtable = $wpdb->base_prefix . 'jb_shortlinks';
		$where = $wpdb->prepare( 'blog_id = %s', $blog_id );
		
		if( get_option('jb_shorturl') == $wpdb->get_var( "SELECT domain FROM {$wpdb->jbtable} WHERE {$where} ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1" ) ) {
			return get_option( 'jb_shorturl' );
		} else {
			$this->update_jb_shorturl( get_option( 'jb_shorturl' ) );
		}
	}

	/**
	 * Update the short domain in the extra table
	 *
	 * This only gets called if we're in multisite because
	 * we're using a standard options page otherwise
	 */
	function update_jb_shorturl( $domain ) {
		global $wpdb, $blog_id;

		$wpdb->jbtable = $wpdb->base_prefix . 'jb_shortlinks';
		$where = $wpdb->prepare( 'blog_id = %s', $blog_id );  

		if( $id = $wpdb->get_var( "SELECT id FROM {$wpdb->jbtable} WHERE {$where} ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1" ) ) {
			$wpdb->update( $wpdb->jbtable, array( 'domain' => $domain ), array( 'id' => $id ), '%s', '%d' );
		} else {
			$wpdb->insert( $wpdb->jbtable, array( 'blog_id' => $blog_id, 'domain' => $domain ), array( '%d', '%s' ) );
		}
	}

	/**
	 * Create a new database table on install if we're in multisite
	 */
	function jb_maybe_create_db() {
		global $wpdb;

		$wpdb->jbtable = $wpdb->base_prefix . 'jb_shortlinks';
		if ( is_super_admin() || is_site_admin() ) {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->jbtable}'" ) != $wpdb->jbtable ) {
				$created = $wpdb->query( "CREATE TABLE IF NOT EXISTS `{$wpdb->jbtable}` (
					`id` bigint(20) NOT NULL auto_increment,
					`blog_id` bigint(20) NOT NULL,
					`domain` varchar(255) NOT NULL,
					PRIMARY KEY  (`id`),
					KEY `blog_id` (`blog_id`,`domain`)
					);" );
				if ( $created ) {
					'<div id="message" class="updated fade"><p><strong>Shortlink database table created</strong></p></div>';
				}
			}
		}
	}

	/**
	 * Delete the extra database table on uninstall if we're in multisite
	 */
	function jb_delete_db_entry() {
		global $wpdb;

		$blog_id = get_current_blog_id();
		$wpdb->jbtable = $wpdb->base_prefix . 'jb_shortlinks';
		if( is_super_admin() || is_site_admin() ) {
			$where = $wpdb->prepare( 'blog_id = %s', $blog_id );  
			$deleted = $wpdb->query( "DELETE FROM {$wpdb->jbtable} WHERE {$where} LIMIT 1" );
			if( $deleted ) {
				echo '<div id="message" class="updated fade"><p><strong>Shortlinks plugin has been disabled</strong></p></div>';
			} 
		}
	}

	/**
	 * Converts a base 10 number to base36
	 */
	static function base36( $number ) {
		return base_convert( $number, 10, 36 );
	}

}

new JB_Shortlinks();
