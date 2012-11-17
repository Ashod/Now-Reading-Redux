<?php
/*
Plugin Name: Now Reading Redux
Version: 6.5.0.0
Plugin URI: http://wordpress.org/extend/plugins/now-reading-redux/
Description: Display the books you're reading, have read recently and plan to read, with cover art fetched automatically from Amazon.
Author: Ashod Nakashian
Author URI: http://blog.ashodnakashian.com
*/

define('NOW_READING_VERSION', '6.5.0.0');
define('NOW_READING_DB_VERSION', 58);
define('NOW_READING_OPTIONS_VERSION', 17);
define('NOW_READING_REWRITE_VERSION', 9);

define('NRTD', 'now-reading');
define('NOW_READING_OPTIONS', 'nowReadingOptions');
define('NOW_READING_VERSIONS', 'nowReadingVersions');

define('NR_MENU_SINGLE', 2);
define('NR_MENU_MULTIPLE', 4);

define('DEFAULT_SIDEBAR_CSS',
'div.booklisting img {
	border: 1px solid #c0c0c0;
	padding: 3px 3px 3px 3px;
	margin: 0 5px 5px 5px;
	width: 67px;    /* Jacket image width. */
	height: 100px;  /* Jacket image height. */
}
a.nr_booktitle, .library, .nr_nobooks, .nr_wishlist {
    font-weight: bold;
}
a.nr_bookauthor {
    font-style: italic;
}
.nr_widget {
	padding-bottom: 20px;
}
.nr_widget h4 {
	padding: 5px;
	border: 1px solid #ccc;
	font: bold 100%/100% Arial, Helvetica, sans-serif;
	margin: 20px 0 5px 0;
	clear: both;
	-moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}
.nr_widget input {
	-moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}
.nr_widget ul {
    list-style-type: none;
    padding: 0px;
    margin: 0px;
}
.nr_widget li {
    list-style-type: none;
    padding: 0px;
    margin: 0px;
    display: -moz-inline-box;
    -moz-box-orient: vertical;
    display: inline-block;
    vertical-align:top;
    word-wrap: break-word;
}
* html .nr_widget li {
    display: inline;
}
* + html .nr_widget li {
    display: inline;
}
.nr_widget #content td {
	padding: 6px;
	border-top: 0px;
	border-bottom: 1px dotted #ccc;
}
.nr_widget #content table {
	border: 0px;
	border-collapse: collapse;
	margin: 0 -1px 24px 0;
	text-align: center;
	width: 100%;
}
.nr_widget:hover .nr_ads {
	display: block !important;
}
.nr_ads {
    display:none; text-align:center; font-size:120%; padding: 4px; text-shadow: 0 0 0.1em grey;
}
.nr_ads .nr_now_reading {
    font-style: italic;
}
.nr_ads .nr_redux {
    font-weight:bold; font-family: arial; position:relative; top:-7px; left:42px; font-size:140%; color:#999; text-shadow: 0 0 0.1em #FFFFCC;
}
.nr_wishlist {
	text-align: center;
	padding: 3px;
	padding-bottom: 5px;
}
');

define('DEFAULT_LIBRARY_CSS',
'.nr_library div.booklisting img {
	border: 1px solid #c0c0c0;
	padding: 5px 5px 5px 5px;
	margin: 0 12px 12px 12px;
	width: 108px;	/* Jacket image width. */
	height: 160px;	/* Jacket image height. */
}
.nr_library h3 {
	padding: 5px;
	border: 1px solid #ccc;
	font: bold 100%/100% Arial, Helvetica, sans-serif;
	margin: 20px 0 5px 0;
	clear: both;
	-moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}
.nr_library input {
	-moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}
.nr_library ul {
    list-style-type: none;
    padding: 0px;
    margin: 0px;
}
.nr_library li {
    list-style-type: none;
    padding: 0px;
    margin: 0px;
    display: -moz-inline-box;
    -moz-box-orient: vertical;
    display: inline-block;
    vertical-align:top;
    word-wrap: break-word;
}
* html .nr_library li {
    display: inline;
}
* + html .nr_library li {
    display: inline;
}
.nr_library #content td {
	padding: 6px;
	border-top: 0px;
	border-bottom: 1px dotted #ccc;
}
.nr_library #content table {
	border: 0px;
	border-collapse: collapse;
	margin: 0 -1px 24px 0;
	text-align: center;
	width: 100%;
}
');

define('DEFAULT_SEARCH_CSS', DEFAULT_LIBRARY_CSS);

/*
 div.booklisting {
	list-style: none;
}
div.booklisting,
div.bookentry {
	margin:  10px 0;
}

div.bookentry {
	display: inline-block;
}

 .nr_widget li > * {
    display: table;
    table-layout: fixed;
    overflow: hidden;
}
* html .nr_widget li { // for IE 6
    width: 80px;
}
.nr_widget li > * { // for all other browser
    width: 80px;
}
*/

/**
 * Load our l18n domain.
 */
$locale = get_locale();
$path = "wp-content/plugins/now-reading-redux/translations/$locale";
load_plugin_textdomain(NRTD, $path);

define('DEFAULT_LIBRARY_TITLE', 'Library');
define('DEFAULT_WISHLIST_TITLE', 'Buy me a gift!');
define('DEFAULT_UNREAD_TITLE', 'Planned');
define('DEFAULT_ONHOLD_TITLE', 'On Hold');
define('DEFAULT_READING_TITLE', 'Reading');
define('DEFAULT_READ_TITLE', 'Finished');
define('DEFAULT_SEARCH_TITLE', 'Search Results');

/**
 * Array of the statuses that books can be.
 * @global array $GLOBALS['nr_statuses']
 * @name $nr_statuses
 */
$nr_statuses = apply_filters('nr_statuses', array(
    'unread'	=> __(DEFAULT_UNREAD_TITLE, NRTD),
    'onhold'	=> __(DEFAULT_ONHOLD_TITLE, NRTD),
    'reading'	=> __(DEFAULT_READING_TITLE, NRTD),
    'read'		=> __(DEFAULT_READ_TITLE, NRTD)
));

/**
 * Array of the post-options that books can have.
 * @global array $GLOBALS['nr_post_options']
 * @name $nr_post_options
 */
$nr_post_options = apply_filters('nr_post_options', array(
    'link'	=> __('Add Link to Post', NRTD),
    'trans'	=> __('Transclude Post Content', NRTD),
    'redirect'	=> __('Redirect to Post', NRTD)
));

/**
 * Array of the domains we can use for Amazon.
 * @global array $GLOBALS['nr_domains']
 * @name $nr_domains
 */
$nr_domains = array(
    '.com'		=> __('International', NRTD),
    '.co.uk'	=> __('United Kingdom', NRTD),
    '.fr'		=> __('France', NRTD),
    '.de'		=> __('Germany', NRTD),
    '.co.jp'	=> __('Japan', NRTD),
    '.ca'		=> __('Canada', NRTD)
);

/**
 * Array of the default library options.
 * @global array $GLOBALS['def_library_options']
 * @name $def_library_options
 */
$def_library_options = array(
    'readingShelf'	=> array('viz' => 'show_image_text', 'title' => DEFAULT_READING_TITLE),
    'unreadShelf'	=> array('viz' => 'show_image_text', 'title' => DEFAULT_UNREAD_TITLE),
    'onholdShelf'	=> array('viz' => 'hide', 'title' => DEFAULT_ONHOLD_TITLE),
    'readShelf'		=> array('viz' => 'show_image_text', 'title' => DEFAULT_READ_TITLE),
	'css'			=> DEFAULT_LIBRARY_CSS,
	'renderStyle'	=> 'list',
    'itemsPerTableRow'	=> 4,
	'showStats'		=> true
);

/**
 * Array of the default sidebar options.
 * @global array $GLOBALS['def_sidebar_options']
 * @name $def_sidebar_options
 */
$def_sidebar_options = array(
    'readingShelf'	=> array('viz' => 'show_image', 'title' => DEFAULT_READING_TITLE, 'maxItems' => 3),
    'unreadShelf'	=> array('viz' => 'show_image', 'title' => DEFAULT_UNREAD_TITLE, 'maxItems' => 3),
    'onholdShelf'	=> array('viz' => 'hide', 'title' => DEFAULT_ONHOLD_TITLE, 'maxItems' => 3),
    'readShelf'		=> array('viz' => 'show_image', 'title' => DEFAULT_READ_TITLE, 'maxItems' => 3),
	'css'			=> DEFAULT_SIDEBAR_CSS,
	'renderStyle'	=> 'list',
    'itemsPerTableRow'	=> 3,
);

/**
 * Array of the default search options.
 * @global array $GLOBALS['def_sidebar_options']
 * @name $def_sidebar_options
 */
$def_sidebar_options = array(
	'viz' 			=> 'show_image_text',
	'title' 		=> DEFAULT_SEARCH_TITLE,
	'maxItems' 		=> 25,
	'css'			=> DEFAULT_SEARCH_CSS,
	'renderStyle'	=> 'list',
    'itemsPerTableRow'	=> 4,
);

// Include other functionality
require_once dirname(__FILE__) . '/compat.php';
require_once dirname(__FILE__) . '/url.php';
require_once dirname(__FILE__) . '/book.php';
require_once dirname(__FILE__) . '/amazon.php';
require_once dirname(__FILE__) . '/admin.php';
require_once dirname(__FILE__) . '/default-filters.php';
require_once dirname(__FILE__) . '/template-functions.php';
require_once dirname(__FILE__) . '/widget.php';

/**
 * Checks if the install needs to be run by checking the NOW_READING_VERSIONS option,
 * which stores the current installed database, options and rewrite versions.
 */
function nr_check_versions()
{
    $versions = get_option(NOW_READING_VERSIONS);
    if (empty($versions) ||
		$versions['db'] < NOW_READING_DB_VERSION ||
		$versions['options'] < NOW_READING_OPTIONS_VERSION ||
		$versions['rewrite'] < NOW_READING_REWRITE_VERSION)
    {
		nr_install();
    }
}
add_action('init', 'nr_check_versions');
add_action('plugins_loaded', 'nr_check_versions');

function nr_check_api_key() {
    $options = get_option(NOW_READING_OPTIONS);
    $AWSAccessKeyId = $options['AWSAccessKeyId'];
    $SecretAccessKey = $options['SecretAccessKey'];

    if (empty($AWSAccessKeyId) || empty($SecretAccessKey)) {

        function nr_key_warning() {
            echo "
			<div id='nr_key_warning' class='updated fade'><p><strong>".__('Now Reading Redux has detected a problem.')."</strong> ".sprintf(__('You are missing one of both of your Amazon Web Services Access Key ID or Secret Access Key. Enter them <a href="%s">here</a>.'), "admin.php?page=nr_options")."</p></div>
			";
        }
        add_action('admin_notices', 'nr_key_warning');
        return;
    }
}
add_action('init','nr_check_api_key');

/**
 * Handler for the activation hook. Installs/upgrades the database table and adds/updates the nowReadingOptions option.
 */
function nr_install()
{
    global $wpdb, $wp_rewrite, $wp_version;

    if (version_compare('2.0', $wp_version) == 1 && strpos($wp_version, 'wordpress-mu') === false)
	{
        echo '
		<p>(Now Reading Redux only works with WordPress 2.0 and above, sorry!)</p>
		';
        return;
    }

    // WP's dbDelta function takes care of installing/upgrading our DB table.
    $upgrade_file = file_exists(ABSPATH . 'wp-admin/includes/upgrade.php') ? ABSPATH . 'wp-admin/includes/upgrade.php' : ABSPATH . 'wp-admin/upgrade-functions.php';
    require_once $upgrade_file;
	
    // Until the nasty bug with duplicate indexes is fixed, we should hide dbDelta output.
    ob_start();
    dbDelta("
	CREATE TABLE {$wpdb->prefix}now_reading (
	b_id bigint(20) NOT NULL auto_increment,
	b_added datetime,
	b_started datetime,
	b_finished datetime,
	b_title VARCHAR(100) NOT NULL,
	b_nice_title VARCHAR(100) NOT NULL,
	b_author VARCHAR(100) NOT NULL,
	b_nice_author VARCHAR(100) NOT NULL,
	b_image text,
	b_asin varchar(12) NOT NULL,
	b_status VARCHAR(8) NOT NULL default 'read',
	b_rating tinyint(4) default '0',
	b_review text,
	b_post bigint(20) default '0',
	b_post_op tinyint(4) default '0',
	b_visibility tinyint(1) default '1',
	b_reader tinyint(4) NOT NULL default '1',
	PRIMARY KEY  (b_id),
	INDEX permalink (b_nice_author, b_nice_title),
	INDEX title (b_title),
	INDEX author (b_author)
	);
	CREATE TABLE {$wpdb->prefix}now_reading_meta (
	m_id BIGINT(20) NOT NULL auto_increment,
	m_book BIGINT(20) NOT NULL DEFAULT '0',
	m_key VARCHAR(100) NOT NULL default '',
	m_value TEXT NOT NULL,
	PRIMARY KEY  (m_id),
	INDEX m_key (m_key)
	);
	CREATE TABLE {$wpdb->prefix}now_reading_tags (
	t_id BIGINT(20) NOT NULL auto_increment,
	t_name VARCHAR(100) NOT NULL DEFAULT '',
	PRIMARY KEY  (t_id),
	INDEX t_name (t_name)
	);
	CREATE TABLE {$wpdb->prefix}now_reading_books2tags (
	rel_id BIGINT(20) NOT NULL auto_increment,
	book_id BIGINT(20) NOT NULL DEFAULT '0',
	tag_id BIGINT(20) NOT NULL DEFAULT '0',
	PRIMARY KEY  (rel_id),
	INDEX book (book_id)
	);
        ");
    $log = ob_get_contents();
    ob_end_clean();

    $log_file = dirname(__FILE__) . '/install-log-' . date('Y-m-d') . '.txt';
    if ( is_writable($log_file) ) {
        $fh = @fopen( $log_file, 'w' );
        if ( $fh ) {
            fwrite($fh, strip_tags($log));
            fclose($fh);
        }
    }

    $defaultOptions = array(
        'formatDate'	=> 'jS F Y',
		'ignoreTime'	=> false,
		'hideAddedDate'	=>	false,
		'sidebarOptions' => $def_library_options,
		'libraryOptions' => $def_sidebar_options,
		'wishlistUrl'	=>  '',
        'associate'		=> 'thevoid0f-20',
        'domain'		=> '.com',
        'imageSize'		=> 'Medium',
        'httpLib'		=> 'snoopy',
        'useModRewrite'	=> false,
        'debugMode'		=> false,
        'menuLayout'	=> NR_MENU_SINGLE,
        'booksPerPage'  => 15,
        'permalinkBase' => 'library/'
    );
    add_option(NOW_READING_OPTIONS, $defaultOptions);

    // Merge any new options to the existing ones.
    $options = get_option(NOW_READING_OPTIONS);
    $options = array_merge($defaultOptions, $options);
    update_option(NOW_READING_OPTIONS, $options);

	// May be unset if called during plugins_loaded action.
	if (isset($wp_rewrite))
    {
		// Update our .htaccess file.
		$wp_rewrite->flush_rules();
	}

    // Update our nice titles/authors.
    $books = $wpdb->get_results("
	SELECT
		b_id AS id, b_title AS title, b_author AS author
	FROM
        {$wpdb->prefix}now_reading
	WHERE
		b_nice_title = '' OR b_nice_author = ''
        ");
    foreach ( (array) $books as $book ) {
        $nice_title = $wpdb->escape(sanitize_title($book->title));
        $nice_author = $wpdb->escape(sanitize_title($book->author));
        $id = intval($book->id);
        $wpdb->query("
		UPDATE
            {$wpdb->prefix}now_reading
		SET
			b_nice_title = '$nice_title',
			b_nice_author = '$nice_author'
		WHERE
			b_id = '$id'
            ");
    }

    // De-activate and attempt to delete the old widget.
    $active_plugins = get_option('active_plugins');
    foreach ( (array) $active_plugins as $key => $plugin ) {
        if ( $plugin == 'widgets/now-reading.php' ) {
            unset($active_plugins[$key]);
            sort($active_plugins);
            update_option('active_plugins', $active_plugins);
            break;
        }
    }
    $widget_file = ABSPATH . '/wp-content/plugins/widgets/now-reading.php';
    if ( file_exists($widget_file) ) {
        @chmod($widget_file, 0666);
        if ( !@unlink($widget_file) )
            die("Please delete your <code>wp-content/plugins/widgets/now-reading.php</code> file!");
    }

    // Set an option that stores the current installed versions of the database, options and rewrite.
    $versions = array('db' => NOW_READING_DB_VERSION, 'options' => NOW_READING_OPTIONS_VERSION, 'rewrite' => NOW_READING_REWRITE_VERSION);
    update_option(NOW_READING_VERSIONS, $versions);
}
register_activation_hook('now-reading-redux/now-reading.php', 'nr_install');

/**
 * Checks to see if the library/book permalink query vars are set and, if so, loads the appropriate templates.
 */
function library_init() {
    global $wp, $wpdb, $q, $query, $wp_query;

    $wp->parse_request();

    if ( is_now_reading_page() )
        add_filter('wp_title', 'nr_page_title');
    else
        return;

    if ( get_query_var('now_reading_library') ) {
    //filter by reader ?
        if (get_query_var('now_reading_reader')) {
            $GLOBALS['nr_reader'] = intval(get_query_var('now_reading_reader'));
        }
        // Library page:
        nr_load_template('library.php');
        die;
    }

    if ( get_query_var('now_reading_id') ) {
    // Book permalink:
        $GLOBALS['nr_id'] = intval(get_query_var('now_reading_id'));

        $load = nr_load_template('single.php');
        if ( is_wp_error($load) )
            echo $load->get_error_message();

        die;
    }

    if ( get_query_var('now_reading_tag') ) {
    // Tag permalink:
        $GLOBALS['nr_tag'] = get_query_var('now_reading_tag');

        $load = nr_load_template('tag.php');
        if ( is_wp_error($load) )
            echo $load->get_error_message();

        die;
    }

    if ( get_query_var('now_reading_page') ) {
    // get page name from query string:
        $nrr_page = get_query_var('now_reading_page');

        $load = nr_load_template($nrr_page);
        if ( is_wp_error($load) )
            echo $load->get_error_message();

        die;
    }

    if ( get_query_var('now_reading_search') ) {
    // Search page:
        $GLOBALS['query'] = $_GET['q'];
        unset($_GET['q']); // Just in case

        $load = nr_load_template('search.php');
        if ( is_wp_error($load) )
            echo $load->get_error_message();

        die;
    }

    if ( get_query_var('now_reading_author') && get_query_var('now_reading_title') ) {
    // Book permalink with title and author.
        $author				= $wpdb->escape(urldecode(get_query_var('now_reading_author')));
        $title				= $wpdb->escape(urldecode(get_query_var('now_reading_title')));
        $GLOBALS['nr_id']	= $wpdb->get_var("
		SELECT
			b_id
		FROM
            {$wpdb->prefix}now_reading
		WHERE
			b_nice_title = '$title'
			AND
			b_nice_author = '$author'
            ");

        $load = nr_load_template('single.php');
        if ( is_wp_error($load) )
            echo $load->get_error_message();

        die;
    }

    if ( get_query_var('now_reading_author') ) {
    // Author permalink.
        $author = $wpdb->escape(urldecode(get_query_var('now_reading_author')));
        $GLOBALS['nr_author'] = $wpdb->get_var("SELECT b_author FROM {$wpdb->prefix}now_reading WHERE b_nice_author = '$author'");

        if ( empty($GLOBALS['nr_author']) )
            die("Invalid author");

        $load = nr_load_template('author.php');
        if ( is_wp_error($load) )
            echo $load->get_error_message();

        die;
    }

	if ( get_query_var('now_reading_reader') ) {
               // Reader permalink.
               $reader = $wpdb->escape(urldecode(get_query_var('now_reading_reader')));
               $GLOBALS['nr_reader'] = $wpdb->get_var("SELECT b_reader FROM {$wpdb->prefix}now_reading WHERE b_reader = '$reader'");

               if ( empty($GLOBALS['nr_reader']) )
                       die("Invalid reader");

               $load = nr_load_template('reader.php');
               if ( is_wp_error($load) )
                       echo $load->get_error_message();

               die;
       }
}
add_action('template_redirect', 'library_init');

/**
 * Loads the given filename from either the current theme's now-reading directory or, if that doesn't exist, the Now Reading templates directory.
 * @param string $filename The filename of the template to load.
 */
function nr_load_template($filename, $require_once = true)
{
    $filename = basename($filename);
    $template = TEMPLATEPATH ."/now-reading-redux/$filename";

    //  check `now-reading` for backwards compatibility.
    if (!file_exists($template))
    {
        $template = TEMPLATEPATH . "/now-reading/$filename";
    }

    if (!file_exists($template))
    {
        $template = dirname(__FILE__) . "/templates/$filename";
    }

    if (!file_exists($template))
    {
        return new WP_Error('template-missing', sprintf(__("Oops! The template file %s could not be found in either the Now Reading template directory or your theme's Now Reading directory.", NRTD), "<code>$filename</code>"));
    }

    load_template($template, $require_once);
}

/**
 * Provides a simple API for themes to load the sidebar template.
 */
function nr_display() {
    nr_load_template('sidebar.php');
}

/**
 * Adds our details to the title of the page - book title/author, "Library" etc.
 */
function nr_page_title( $title ) {
    global $wp, $wp_query;
    $wp->parse_request();

    $title = '';

    if ( get_query_var('now_reading_library') )
        $title = 'Library';

    if ( get_query_var('now_reading_id') ) {
        $book = get_book(intval(get_query_var('now_reading_id')));
        $title = $book->title . ' by ' . $book->author;
    }

    if ( get_query_var('now_reading_tag') )
        $title = 'Books tagged with &ldquo;' . htmlentities(get_query_var('now_reading_tag'), ENT_QUOTES, 'UTF-8') . '&rdquo;';

    if ( get_query_var('now_reading_search') )
        $title = 'Library Search';

    if ( !empty($title) ) {
        $title = apply_filters('now_reading_page_title', $title);
        $separator = apply_filters('now_reading_page_title_separator', ' - ');
        return $separator.$title;
    }
    return '';
}

/**
 * Adds information to the header for future statistics purposes.
 */
function nr_header_stats() {
    echo '
	<meta name="now-reading-version" content="' . NOW_READING_VERSION . '" />
	';
}
add_action('wp_head', 'nr_header_stats');

if ( !function_exists('robm_dump') ) {
/**
 * Dumps a variable in a pretty way.
 */
    function robm_dump() {
        echo '<pre style="border:1px solid #000; padding:5px; margin:5px; max-height:150px; overflow:auto;" id="' . md5(serialize($object)) . '">';
        $i = 0; $args = func_get_args();
        foreach ( (array) $args as $object ) {
            if ( $i == 0 && count($args) > 1 && is_string($object) )
                echo "<h3>$object</h3>";
            var_dump($object);
            $i++;
        }
        echo '</pre>';
    }
}

function renderPhpToString($file, $vars=null)
{
    if (is_array($vars) && !empty($vars))
    {
        extract($vars);
    }

    ob_start();
    nr_load_template($file, true);
    return ob_get_clean();
}

// [nrr style="numbered" viz="show_text" status="all" num="-1" order="asc" finished_year="2011"]
function nrr_shortcode_func($atts)
{
    extract( shortcode_atts( array(
        'style' => 'list',  // list, numbered, table
        'status' => 'all',  // unread,  reading, onhold, read, all
        'orderby' => 'finished', // reading, read, onhold, finished
        'order' => 'desc',  // asc, desc
        'search' => '',
        'author' => '',
        'title' => '',
        'reader' => '',
        'started_year' => '',
        'started_month' => '',
        'finished_year' => '',
        'finished_month' => '',
        'num' => '-1',  // The maximum number of items to show. -1 for all.
        'viz' => 'show_text', //hide, show_text, show_image, show_image_text
        'items_per_row' => '1',
    ), $atts ) );

    global $book_query, $library_options, $shelf_title, $shelf_option;
    $shelf_option = array('viz' => $viz);
    $library_options = array('renderStyle' => $style, 'itemsPerTableRow' => $items_per_row);
    $book_query = "status={$status}&orderby={$orderby}&order={$order}&search={$search}&author={$author}&title={$title}&reader={$reader}&num={$num}&started_year={$started_year}&started_month={$started_month}&finished_year={$finished_year}&finished_month={$finished_month}";

    return renderPhpToString('shelf.php');
}
add_shortcode('nrr', 'nrr_shortcode_func');

?>