=== JB Shortener ===
Contributors: betzster
Tags: shorturl, twitter
Requires at least: 3.3
Tested up to: 3.3
Stable tag: 0.8

Changes the Short URL and the Twitter Tools URL for each post on your site using a custom, shortened domain and a base-36 encode of the post ID.

== Description ==

This plugin modifies the default short-url functionality to work with a custom shortened domain that you own. It works fully with Twitter Tools, a popular plugin for automatically tweeting about new posts. If you have a twitter plugin that you think we should support, let us know. For the nerds: we're doing a base-36 encode of the post ID as the short-url for each post.

*Note*: This plugin no longer requires external hosting of shortening scripts.

== Installation ==

1. Buy a short domain
2. Upload `jb-shortener.php` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Update the "Short URL" in Options->General

If you're running a multisite configuration, copy `sunrise.php` to the `wp-content` folder. If you're running the multisite domain mapping plugin and already have a file called `sunrise.php`, rename it to `dm_sunrise.php`.

== Frequently Asked Questions ==

= Why would I want this Plugin? =

You can use a custom, shorter url for the short urls in WordPress and with Twitter Tools.

= Does it work with Multisite enabled? =

Yes, but you have to copy the `sunrise.php` file into the `wp_content` folder.

== Changelog ==

= 0.8 =
* No longer requires hosting of short-url redirect scripts, even for multisite
* Added `sunrise.php`

= 0.7 =
* Straight redirect to post instead of bouncing to `?p=POSTID` first

= 0.5 =
* No longer need to host the short-url redirect on an external domain for non-multisite installs

= 0.4 =
* First version