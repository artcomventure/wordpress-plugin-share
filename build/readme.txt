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

* Facebook
* Twitter
* Google+
* Pinterest
* Tumblr
* Whatsapp
* SMS
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

Once activated you'll find the 'Share' options page listed in the submenu of 'Settings'.

1. Decide if you want to show the number of shares of every social network.
2. Define the post types the share functionality should be available.
3. Enable the share buttons you want to provide and arrange them via drag'n'drop.
4. Insert share links:
  * via widget
  * in Template: `share_links();`
  * with shortcode directly in the editor: `[share]`

== Plugin Updates ==

Although the plugin is not _yet_ listed on https://wordpress.org/plugins/, you can use WordPress' update functionality to keep it in sync with the files from [GitHub](https://github.com/artcomventure/wordpress-plugin-share).

Please use for this our [WordPress Repository Updater](https://github.com/artcomventure/wordpress-plugin-repoUpdater).

**Repository Updater _Share_ Settings**

* Repository URL: https://github.com/artcomventure/wordpress-plugin-share/
* Subfolder (optionally, if you don't want/need the development files in your environment): build

_We test our plugin through its paces, but we advise you to take all safety precautions before the update. Just in case of the unexpected._

== Changelog ==

= Unreleased =

* default share list styles

== 1.0.4 - 2016-03-10 [YANKED] ==
**Fixed**

* theme share links

= 1.0.3 - 2016-03-10 =
**Added**

* gulp
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
