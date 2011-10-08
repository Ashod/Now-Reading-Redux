# Now Reading Redux #

Contributors: ashodnakashian
Donate link:
Tags: wordpress, books, widget, amazon
Requires at least: 2.7.0
Tested up to: 3.2.1
Stable tag: trunk

Track and share books you read, are reading and plan to read, with Amazon integration and cover art and graphs reading statistics.

## Description ##

Now Reading Redux is a resurrection of the [Now Reading Reloaded project by Ben Gunnink](http://heliologue.com/ "Now Reading Reloaded") 5.1.3.2 codebase, which was itself a fork/re-enlivening of [Rob Miller's original Now Reading plugin](http://robm.me.uk/projects/plugins/wordpress/now-reading/ "Original Now Reading Plugin").  It was forked from Rob's code as of 4.4.4svn, in order to update its interface to work with Wordpress 2.7 and above.

With NRR, you can manage a library of your current books, as well as historical and planned books.

NRR depends on jQuery and assumes it already loaded by WP or your theme. Please use a sufficiently recent version, say 1.4 or newer, preferably the latest. The graph is generated using [TufteGraph](http://xaviershay.github.com/tufte-graph/ "TufteGraph") which depends on [Raphaël](http://raphaeljs.com/ "Raphaël"). All necessary files are included, no additional libraries necessary (beyond jQuery).

The home of Now Reading Redux is [blog.ashodnakashian.com/projects/now-reading-redux/](http://blog.ashodnakashian.com/projects/now-reading-redux/ "Now Reading Redux"). You may find news and leave comments and requests there.

For issues and complaints and for development source code, please go to [github.com/Ashod/Now-Reading-Redux](https://github.com/Ashod/Now-Reading-Redux "Git repository").

## Installation ##

Please backup your database before installing/upgrading. I try hard to make sure the releases are bug-free. However I can't perform extensive tests. If you find any bugs or have feature requests, please kindly report them at https://github.com/Ashod/Now-Reading-Redux/issues.

1. Upload `now-reading-redux` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Optionally, make any changes to the provided template files and store them in your theme directory (see the "Template Files" section.)

## Upgrade ##

1. Just to be safe than sorry, backup your database.
1. Deactivate any existing versions of `now-reading` or `now-reading-reloaded`.
1. Install `now-reading-redux` as described below.
1. Activate `now-reading-redux`.
1. Your database will be updated and library restored as was previously.

Note: After upgrading the widget, if any, may get removed. This may happen in some themes but not most. If it is, simply drag it where you'd like to see it.

## Frequently Asked Questions ##

### Can I upgrade from Now Reading or Now Reading Reloaded? How? ###

Please read the Upgrade section under Installation.

### Where is the old changelog? ###

Please find the original Now Reading Relaoded readme with the changelog in the readme.old file.

## Screenshots ##

1. The book manager where all the books are visible, sorted by any of the columns, in both ascending and descending orders. Searching for -partial- book titles is supported. Author names are shortcuts to filter by that author only.
2. The options page showing some of the new options available in Redux.

3. The book search/add page is unchanged, but...

4. The search results may return more editions than older versions of the plugin. Clicking "Use This Result" adds the book in question to the library.

5. The library page showing statistics graph.

6. The side-bar widget showing read, current and up-next books, link to the library and search box.

### Changelog ###

### 6.1.0.0 ###
* New feature: Reading statistics and graph shown in the library page by calling print_book_stats(). Uses TafteGraph/Raphaël.
* Updated and included Suffusion NRR templates.

#### 6.0.9.0 ####
* New option: Hides Added dates in Manager and Book Edit pages.
* Authors and Statuses are linked to filter the books shown in the Manager.

#### 6.0.8.0 ####
* New option: Controls the default number of books displayed. Primarily controls the sidebar book count.
* The 'reading' and 'unread' lists are now shown in random order, the 'read' list is shown last read first.
* 'View Full Library' is now hidden in the library page. Search button is centered.

#### 6.0.6.0 ####
* New option: Set book visibility as private or public. Private books are not shown to anyone but the owner. Note: admins may be able to view them!
* Templates updated to improve utility.

#### 6.0.4.0 ####
* New option: Ignores time in timestamps, showing only dates.

#### 6.0.3.0 ####
* Started and Finished timestamps are automatically set when changing the status from 'yet to read' to 'currently reading' and from 'currently reading' to 'finished', respectively, only when the user leaves these fields blank. NRR no longer overwrites the user's set values.

#### 6.0.2.0 ####
* Added sorting support to the Book Manager table.

#### 6.0.0.0 ####
* Initial import of Now Reading Reloaded 5.1.3.2
* Added this README file and renamed the old one to readme.old.

### Upgrade Notice ###

Please read the changelog for reasons to upgrade.

## Known Issues ##

* When js-minify is enabled by an optimization plugin, the javascript files used by the TafteGraph/Raphaël may get corrupted. This issue is known to exist in W3 Total Cache (tested version 0.9.2.4). The only known workaround at this point is to disable js-minify from the Minify settings. CSS and HTML minification can still be enabled as well as compression.
