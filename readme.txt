=== Now Reading Reloaded ===
Contributors: bgunnink
Tags: books, widget, amazon
Requires at least: 2.7.0
Tested up to: 3.0
Stable tag: 5.1.3.2

Allows you to display the books you're reading, have read recently and plan to read, with cover art fetched automatically from Amazon.

== Description ==

Now Reading Reloaded is a fork/re-enlivening of [Rob Miller's original Now Reading plugin](http://robm.me.uk/projects/plugins/wordpress/now-reading/ "Original Now Reading Plugin").  It is forked from Rob's code as of 4.4.4svn, in order to update its interface to work with Wordpress 2.7 and above.

With it, you can manage a library of your current books, as well as historical and planned books.

== Installation ==

1. Upload `now-reading` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Make any changes the to provided template files and store them in your theme directory (see the "Template Files" section)

== Frequently Asked Questions ==

= I keep getting the error "Fatal error: Call to undefined function: simplexml_load_string() [...]" =

Now Reading Reloaded is for Wordpress 2.7 and above; users running older versions should consider the [original plugin](http://robm.me.uk/projects/plugins/wordpress/now-reading).  Users running PHP4 will experience errors if trying attempting to use the latest version of this plugin;  support for PHP4 was dropped during its original development.

= When will you implement feature X? =

New feature development is planned, but I have no clear timeline as the maintenance of this plugin is purely a hobby.

= Why does my library page look funny? =

Now Reading comes with premade templates (`/templates/`) that were originally made for the default Kubrick theme that comes with Wordpress.  If your theme has more or less markup, the templates may look strange in your theme.

My suggestion to those who are having trouble is to open up the respect Now Reading template (such as `library.php`) side-by-side with one of your standard theme templates, and make sure that the markup matches.

== Screenshots ==

1. Adding a book
2. Library view
3. Editing a book
4. Now Reading Options

== Changelog ==

= 5.1.3.2 =
* Fixed bugs occurring in with NRR 5.1.2.1 and WP 3.0-RC1
* Dutch translation (props Bas Rutjes)

= 5.1.2.1 =
* Fixed DB schema changes from 5.1.2.0 that produces errors

= 5.1.2.0 =
* Bumped compatibility to WP 2.9
* Allowed UTF-8 characters in widget title

= 5.1.1.3 =
* Roll back changes (DB caching plugin was producing original error)

= 5.1.1.2 =
* Fix wrong wpnonce name

= 5.1.1.1 =
* Fixed an issue where books could not be deleted

= 5.1.1.0 =
* PHP4 support for Amazon API request signing
* Some visual tweaks and fixed some README typos

= 5.1.0.0 =
* Update the plugin to use Amazon's changed API (requires Amazon Web Services account)
* Allow additional template files (props David Edwards)

= 5.0.3.2 =
* Hopefully fix a rare SQL bug when adding a book

= 5.0.3.1 =
* Plugin will look in theme for both `now-reading` as well as `now-reading-reloaded` for customized templates
* Bump compatibility to all of WP 2.8

= 5.0.3 =
* Fixed 404 errors from plugin name change

= 5.0.2 =
* Fixed path/permission error in Single Menu mode with Wordpress 2.8.1
* Fixed issue where searching no longer returned results

= 5.0.1 =
* Added screenshots
* Markdown syntax fixes to the `readme.txt` file

= 5.0 =
* First release in the Wordpress plugins repository
* Code cleanup

== Template Files ==

The `templates` folder of the Now Reading plugin contains a default set of templates for displaying your book data in various places (sidebar, library, etc.).  *Any changes you make to these templates should be in your own theme folder*.  Now Reading will first look inside your active theme folder for a directory called `now-reading` for template files;  if it doesn't find them, it will use its own.  Customized template should be stored in `/wp-content/yourtheme/now-reading-reloaded/` so that your changes are not overwritten when you upgrade.