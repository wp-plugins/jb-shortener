=== JB Shortener ===
Contributors: betzster
Tags: shorturl, twitter
Requires at least: 3.3
Tested up to: 3.4.1
Stable tag: 1.1.1

Changes the Short URL and the Twitter Tools URL for each post on your site using a custom, shortened domain and a base-36 encode of the post ID.

== Description ==

Changes the Short URL and the Twitter Tools URL for each post on your site using a custom, shortened domain and a base-36 encode of the post ID.

== Installation ==

1. Buy a short domain
2. Upload `jb-shortener.php` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Update the "Short URL" in Options->General

If you're running a multisite configuration, copy `sunrise.php` to the `wp-content` folder and set `define('SUNRISE', 'on');` in `wp-config.php`.

== Frequently Asked Questions ==

= Why would I want this Plugin? =

You can use a custom, shorter url for the short urls in WordPress and with Twitter Tools.

= Does it work with Multisite enabled? =

Yes, but you have to copy the `sunrise.php` file into the `wp_content` folder.

== Changelog ==

= 1.1.1 =
* Fix formatting on options page
* Display warning if no short domain is set

= 1.1 =
* Now works with multisite installs with subdirectories
* Various optimizations
* Removed explicit support for domain mapping plugin

= 1.0 =
* Removes entries from the redirect database in multisite mode
* No longer continues to redirect after plugin has been disabled in multisite mode
* more efficient check for database install

= 0.8 =
* No longer requires hosting of short-url redirect scripts, even for multisite
* Added `sunrise.php`

= 0.7 =
* Straight redirect to post instead of bouncing to `?p=POSTID` first

= 0.5 =
* No longer need to host the short-url redirect on an external domain for non-multisite installs

= 0.4 =
* First version