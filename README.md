# WordPress Social Share (and more)

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

## Installation

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

## Usage

Once activated you'll find the 'Share' options page listed in the submenu of 'Settings'.

1. Decide whether to show the number of shares (if available) or not.
2. Attach default styling or not.
3. Define the post types the share functionality should be available for.
4. Enable the share buttons you want to provide and order them via drag'n'drop.
5. Insert share links:
  * via widget
  * in template: `share_links();`
  * with shortcode directly in the editor: `[share]`

For developer: you have plenty of filters to interact and extend the code.

## Plugin Updates

Although the plugin is not _yet_ listed on https://wordpress.org/plugins/, you can use WordPress' update functionality to keep it in sync with the files from [GitHub](https://github.com/artcomventure/wordpress-plugin-share).

**Please use for this our [WordPress Repository Updater](https://github.com/artcomventure/wordpress-plugin-repoUpdater)** with the settings:

* Repository URL: https://github.com/artcomventure/wordpress-plugin-share/
* Subfolder (optionally, if you don't want/need the development files in your environment): build

_We test our plugin through its paces, but we advise you to take all safety precautions before the update. Just in case of the unexpected._

## Questions, concerns, needs, suggestions?

Don't hesitate! [Issues](https://github.com/artcomventure/wordpress-plugin-share/issues) welcome.
