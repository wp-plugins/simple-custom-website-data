=== Custom Website Data ===
Contributors: DannyWeeks
Donate link: http://dannyweeks.com/contact-me
Tags: information, data, storage, business details, developer tools, contact, details, phone, email, address, global, info
Requires at least: 3.5.2
Tested up to: 3.6.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Store any data you wish and access it using shortcodes or PHP functions. Easy and simple data storage and retrieval for beginners and advanced users.

== Description ==

CWD allows you to simply store and retrieve  data quickly and easily for your own use. Example applications of this could be to save your websites contact email address and phone number. Storing them using CWD you can then output them using the simple shortcodes throughout your website; if something changes, no problem just update it in one place and you are good to go.

Your custom data can either be displayed on the website using shortcodes or you can use the data to manipulate the website in ways only limited by your imagination through use of a PHP function!

Key Features:

*   Easy and quick installation.
*   Data stored in your Wordpress database for security and quick access.
*   Shortcode works instantly with your data with not need for additional settings.
*   Developers can use the PHP function provided to access the data quickly and simply.

Future Features:

*   Import and export data using using csv.
*   Implement AJAX submissions for quicker manipulation and better UX
*   Opt in anonymous usage tracking

== Installation ==

Installation of Custom Website Data is simple.

e.g.

1. Upload `simple-custom-website-data` to the `/wp-content/plugins/` directory or upload the .zip to in your Wordpress admin area.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the "Custom Data" tab in your admin menu

== Frequently Asked Questions ==

= How do I use my data =

You have three options.
*   You can place the shortcode generated on any page or post.
*   Use the Wordpress function `do_shortcode('[cwd ref="yourdataref"]')`
*   Use the CWD PHP function `cwd_getThe('yourdataref')`

== Screenshots ==

You can find these screenshots in the /assets/ directory of CWD

1. post-install.png     - This screenshot shows the plugin dashboard after installing CWD.
2. adding-data.png      - This is how data is added.
3. added.png            - This is how the dashboard looks after adding a new record.
4. using-shortcode.png  - Example of how shortcode can be used.
5. finished.png         - Example of how that shortcode works.

== Changelog ==

= 1.2 =
* Added advanced function `cwd_updateThe()` for writing to a record via PHP
* Updated user guide to reflect changes
* CSS change to hide wp footer

= 1.1 =
* Fixed security issues.
* Updated folder name to 'simple-custom-website-data' and documentation to match.

= 1.0 =
* N/A for version 1.0.

== Upgrade Notice ==

= 1.1 =
N/A for version 1.1.

= 1.0 =
N/A for version 1.0.