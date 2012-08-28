=== Remove Double Space ===
Contributors: jjeaton
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JKWPDXGYLASCY
Tags: plugin, posts, Post, formatting, typography, editing, spaces
Requires at least: 2.9.2
Tested up to: 3.4.1
Stable tag: 0.3

Remove duplicate whitespace in between sentences or elsewhere within posts. Useful if multiple contributors use different styles for sentence spacing.

== Description ==

On display, duplicate whitespace (including unicode whitespace characters) in between sentences or elsewhere within posts will be replaced with a single space. Useful if multiple contributors use different styles for sentence spacing or as a catch-all for any unintended extra whitespace.

This enables consistency in a blog with multiple contributors where one writer uses double spaces between sentences and another uses single spaces.

No modifications are made to the post content itself, the replacement happens when the content is displayed to the user.

== Installation ==

1. Unzip `remove-double-space.zip`.
1. Upload the `remove-double-space` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Navigate to the admin panel `Settings > Remove Spaces` to turn the replacement on/off (off by default).

== Frequently Asked Questions ==

= Does this plugin clean up after itself if uninstalled? =

Yes, if deleted via the admin interface, the plugin will remove its options from the database using the `uninstall.php` file. Deactivating the plugin won't have any effect on the database options. If this file is not included with the plugin, the uninstall process will not run.

== Changelog ==

= 0.3 =
* Tested up to 3.2

= 0.2 =
* Fully uses Settings API
* Changed menu name and moved to Settings panel, added link to settings from plugins page
* Encapsulated plugin in a class
* Fixed donation link

= 0.1 =
* Initial release, supports switching replacement off/on globally.

