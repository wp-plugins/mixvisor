=== Mixvisor ===
Contributors: gilesbutler
Donate link: http://mixvisor.com
Tags: audio, music, discovery, tool
Requires at least: 3.0.1
Tested up to: 4.1.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Mixvisor helps your users discover more from the artists they read about in your content.

== Description ==

This is the Wordpress plugin to help you integrate the [Mixvisor.com](http://mixvisor.com) music discovery service into your Wordpress powered site.

You need to be a registered Mixvisor user to use this service. Registration is free.

== Installation ==

1. Upload `mixvisor.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions == 

= Do i need an account to use this? =

Yes, you must register for a free account on [Mixvisor.com](http://mixvisor.com) to use this service.

== Screenshots ==

1. Mixvisor settings page

== Changelog ==

= 0.1.5 =
* Set the protocol to work with both secure and insecure requests 
* 

= 0.1.4 =
* Fixed a bug in the http protocol
* 

= 0.1.3 =
* Fixed a bug so that a post will only be excluded from Mixvisor if all it's categories are excluded

= 0.1.2 =
* Fixed a bug which made Mixvisor output on all categories 

= 0.1.1 =
* Added a check for SID & AT attributes before outputting to the page to prevent needlessly loading MV for non-registered users

= 0.1.0 =
* Add support for excluding categories that Mixvisor appears in
* Add support to exclude Mixvisor from certain pages

== Upgrade Notice == 

= 0.1.0 =