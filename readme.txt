=== JB Shortener ===
Contributors: betzster
Tags: shorturl, twitter
Requires at least: 3.1
Tested up to: 3.3
Stable tag: 0.6

Changes the Short URL and the Twitter Tools URL for each post on your site using a custom, shortened domain and a base-36 encode of the post ID.

== Description ==

This plugin modifies the default short-url functionality to work with a custom shortened domain that you own. It works fully with Twitter Tools, a popular plugin for automatically tweeting about new posts. If you have a twitter plugin that you think we should support, let us know. For the nerds: we're doing a base-36 encode of the post ID as the short-url for each post.

*Note*: This plugin no longer requires external hosting of shortening scripts unless you're in a multisite environment.

== Installation ==

1. Buy a short domain
2. Upload `jb-shortener.php` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Update the "Short URL" in Options->General

== Frequently Asked Questions ==

= Why would I want this Plugin? =

You can use a custom, shorter url for the short urls in WordPress and with Twitter Tools

= Does it work with Multisite enabled? =

In short, kinda. The plugin will still work, but you need to install the contents of short.domain itself. If you don't feel like that's possible, try to hold out for the version that works fully with multisite enabled.

== Changelog ==

= 0.5 =
* No longer need to host the short-url redirect on an external domain for non-multisite installs

= 0.4 =
* First version