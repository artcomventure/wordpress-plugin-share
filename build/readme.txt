=== Share ===

Contributors:
Donate link:
Tags: share, social networks, Facebook, Twitter, Google+, Pinterest, Tumblr, SMS, Whatsapp, Email
Requires at least:
Tested up to:
Stable tag:
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Spread your content over social networks and more (Facebook, Twitter, Google+, Pinterest, Tumblr, Whatsapp, SMS, Email).

== Description ==

Spread your WordPress content over the social networks **without the use of the networks' javascripts**.

* Facebook _(counter available)_
* Twitter
* Google+  _(counter available)_
* Pinterest  _(counter available)_
* Tumblr
* Linkedin  _(counter available)_
* **Whatsapp**
* **SMS**
* Email

== Installation ==

1. Upload files to the `/wp-content/plugins/` directory of your WordPress installation.
  * Either [download the latest files](https://github.com/artcomventure/wordpress-plugin-share/archive/master.zip) and extract zip (optionally rename folder)
  * ... or clone repository:
  ```
  $ cd /PATH/TO/WORDPRESS/wp-content/plugins/
  $ git clone https://github.com/artcomventure/wordpress-plugin-share.git
  ```
  If you want a different folder name than `wordpress-plugin-share` extend the clone command by ` 'FOLDERNAME'` (replace the word `'FOLDERNAME'` by your chosen one):
  ```
  $ git clone https://github.com/artcomventure/wordpress-plugin-share.git 'FOLDERNAME'
  ```
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. **Enjoy**

== Usage ==

Once activated you'll find the 'Share/Follow' options page listed in the submenu of 'Settings'.

1. Attach default styling (as buttons) or not.
2. Share settings
  * Decide whether to show the number of shares (if available) or not.
  * Define the post types the share functionality should be available for.
  * Enable the share buttons you want to provide and order them via drag'n'drop.
3. Follow settings
  * Add as many networks (name and url) as you like.
4. Insert share/follow links:
  * via widget
  * in template: `share_links();` or `follow_links();`
  * with shortcode directly in the editor: `[share]` or `[follow]`

For developer: you have plenty of filters to interact and extend the code.

== Plugin Updates ==

Although the plugin is not _yet_ listed on https://wordpress.org/plugins/, you can use WordPress' update functionality to keep it in sync with the files from [GitHub](https://github.com/artcomventure/wordpress-plugin-share).

**Please use for this our [WordPress Repository Updater](https://github.com/artcomventure/wordpress-plugin-repoUpdater)** with the settings:

* Repository URL: https://github.com/artcomventure/wordpress-plugin-share/
* Subfolder (optionally, if you don't want/need the development files in your environment): build

_We test our plugin through its paces, but we advise you to take all safety precautions before the update. Just in case of the unexpected._

== Questions, concerns, needs, suggestions? ==

Don't hesitate! [Issues](https://github.com/artcomventure/wordpress-plugin-share/issues) welcome.

== Changelog ==

= 1.5.2 - 2018-10-05 =
**Fixed**

* Default texts.

= 1.5.1 - 2018-08-13 =
**Changed**

* If standard CSS is excluded: no default share styles at all.

= 1.5.0 - 2017-11-14 =
**Added**

* Xing share.

= 1.4.4 - 2017-10-20 =
**Fixed**

* Justify share items to flex-start.

= 1.4.3 - 2017-10-09 =
**Fixed**

* PHP errors.

= 1.4.2 - 2017-09-25 =
**Added**

* Add specific Open Graph and Twitter meta tags for title and description since Facebook seems to ignore default ones :/

= 1.4.1 - 2017-09-13 =
**Fixed**

* wp_register_style vs wp_enqueue_style

= 1.4.0 - 2017-09-13 =
**Added**

* Icon support.

= 1.3.2 - 2017-09-13 =
**Fixed**

* Removed default follow option.

= 1.3.1 - 2017-05-30 =
**Removed**

* Test data.

= 1.3.0 - 2017-05-30 =
**Added**

* Follow section.

= 1.2.5 - 2016-08-23 =
**Fixed**

* Facebook REST API is deprecated.

= 1.2.4 - 2016-05-17 =
**Fixed**

* Meta image PHP parse error.
* Remove empty meta.

= 1.2.3 - 2016-05-17 =
**Fixed**

* Meta (image and default values).

= 1.2.2 - 2016-03-13 =
**Fixed**

* Minor bugs

**Added**

* german translation

= 1.2.1 - 2016-03-13 =
**Fixed**

* Share links in post's ´!$more´ view
* Minor bugs

= 1.2.0 - 2016-03-12 =
**Added**

* Linkedin
* Network's favicon
* More apply_filters() to interact with code
* Default CSS

**Changed**

* Refactor backend (more settings)

**Removed**

* Twitter share count (depricated since 2015-11-20 https://blog.twitter.com/2015/hard-decisions-for-a-sustainable-platform)

= 1.1.1 - 2016-03-11 =
**Added**

* German translations
* Gulp del for clearing build folder before building it again
* Changed 'Plugins' screen detail link

= 1.1.0 - 2016-03-11 =
**Added**

* Default styles
* .csscomb.json

**Fixed**

* Facebook link text for non app use

= 1.0.5 - 2016-03-10 =
**Added**

* ... and **changed** 'Plugins' screen links

== 1.0.4 - 2016-03-10 [YANKED] ==
**Fixed**

* Theme share links

= 1.0.3 - 2016-03-10 =
**Added**

* Gulp
* build/
* README.md and CHANGELOG.md

= 1.0.2 - 2016-03-02 =
**Added**

* LICENSE

= 1.0.1 - 2016-03-02 =
**Added**

* .gitignore

= 1.0.0 - 2016-03-02 =
**Added**

* Initial file commit
