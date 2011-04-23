<?php
/**
 * URL/mod_rewrite functions
 * @package now-reading
 */

/**
 * Handles our URLs, depending on what menu layout we're using
 * @package now-reading
 */
class nr_url {
/**
 * The current URL scheme.
 * @access public
 * @var array
 */
    var $urls;

    /**
     * The scheme for a multiple menu layout.
     * @access private
     * @var array
     */
    var $multiple;
    /**
     * The scheme for a single menu layout.
     * @access private
     * @var array
     */
    var $single;

    /**
     * Constructor. Populates {@link $multiple} and {@link $single}.
     */
    function nr_url() {
        $this->multiple = array(
            'add'		=> '',
            'manage'	=> get_option('siteurl') . '/wp-admin/admin.php?page=manage_books',
            'options'	=> get_option('siteurl') . '/wp-admin/options-general.php?page=nr_options'
        );
        $this->single = array(
            'add'		=> get_option('siteurl') . '/wp-admin/admin.php?page=add_book',
            'manage'	=> get_option('siteurl') . '/wp-admin/admin.php?page=manage_books',
            'options'	=> get_option('siteurl') . '/wp-admin/admin.php?page=nr_options'
        );
    }

    /**
     * Loads the given scheme, populating {@link $urls}
     * @param integer $scheme The scheme to use, either NR_MENU_SINGLE or NR_MENU_MULTIPLE
     */
    function load_scheme( $option ) {
        if ( file_exists( ABSPATH . '/wp-admin/post-new.php' ) )
            $this->multiple['add'] = get_option('siteurl') . '/wp-admin/post-new.php?page=add_book';
        else
            $this->multiple['add'] = get_option('siteurl') . '/wp-admin/post.php?page=add_book';

        if ( $option == NR_MENU_SINGLE )
            $this->urls = $this->single;
        else
            $this->urls = $this->multiple;
    }
}
/**
 * Global singleton to access our current scheme.
 * @global nr_url $GLOBALS['nr_url']
 * @name $nr_url
 */
$nr_url		= new nr_url();
$options	= get_option('nowReadingOptions');
$nr_url->load_scheme($options['menuLayout']);

/**
 * Registers our query vars so we can redirect to the library and book permalinks.
 * @param array $vars The existing array of query vars
 * @return array The modified array of query vars with our additions.
 */
function nr_query_vars( $vars ) {
    $vars[] = 'now_reading_library';
    $vars[] = 'now_reading_id';
    $vars[] = 'now_reading_tag';
    $vars[] = 'now_reading_page';   
    $vars[] = 'now_reading_search';
    $vars[] = 'now_reading_title';
    $vars[] = 'now_reading_author';
    $vars[] = 'now_reading_reader'; //in order to filter books by reader
    return $vars;
}
add_filter('query_vars', 'nr_query_vars');

/**
 * Adds our rewrite rules for the library and book permalinks to the regular WordPress ones.
 * @param array $rules The existing array of rewrite rules we're filtering
 * @return array The modified rewrite rules with our additions.
 */
function nr_mod_rewrite( $rules ) {
    $options = get_option('nowReadingOptions');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . '([0-9]+)/?$', 'index.php?now_reading_id=$matches[1]', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . 'tag/([^/]+)/?$', 'index.php?now_reading_tag=$matches[1]', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . 'page/([^/]+)/?$', 'index.php?now_reading_page=$matches[1]', 'top');   
    add_rewrite_rule(preg_quote($options['permalinkBase']) . 'search/?$', 'index.php?now_reading_search=true', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . 'reader/([^/]+)/?$', 'index.php?now_reading_reader=$matches[1]', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . '([^/]+)/([^/]+)/?$', 'index.php?now_reading_author=$matches[1]&now_reading_title=$matches[2]', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . '([^/]+)/?$', 'index.php?now_reading_author=$matches[1]', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . '?$', 'index.php?now_reading_library=1', 'top');
}
add_action('init', 'nr_mod_rewrite');

/**
 * Returns true if we're on a Now Reading page.
 */
function is_now_reading_page() {
    global $wp;
    $wp->parse_request();

    return (
    get_query_var('now_reading_library') ||
        get_query_var('now_reading_search')  ||
        get_query_var('now_reading_id')      ||
        get_query_var('now_reading_tag')     ||
        get_query_var('now_reading_page')    ||        
        get_query_var('now_reading_title')   ||
        get_query_var('now_reading_author')  ||
		get_query_var('now_reading_reader')
	);  
}

?>