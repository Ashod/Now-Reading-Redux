<?php
/**
 * Adds our admin menus, and some stylesheets and JavaScript to the admin head.
 * @package now-reading
 */

/**
 * Adds our stylesheets and JS to admin pages.
 */
function nr_add_head() {

    wp_enqueue_script('nowreading', plugins_url('/js/manage.js', __FILE__), array('jquery'));

}
add_action('admin_print_scripts', 'nr_add_head');

require_once dirname(__FILE__) . '/admin/admin-add.php';
require_once dirname(__FILE__) . '/admin/admin-manage.php';
require_once dirname(__FILE__) . '/admin/admin-options.php';

/**
 * Manages the various admin pages Now Reading uses.
 */
function nr_add_pages() {
    $options = get_option('nowReadingOptions');

    //B. Spyckerelle
    //changing NR level access in order to let blog authors to add books in multiuser mode
    $nr_level = $options['multiuserMode'] ? 2 : 9 ;

    if ( $options['menuLayout'] == NR_MENU_SINGLE ) {
        add_menu_page('Now Reading', 'Now Reading', 9, 'add_book', 'now_reading_add');

		add_submenu_page('add_book', 'Add a Book', 'Add a Book',$nr_level , 'add_book', 'now_reading_add');
		add_submenu_page('add_book', 'Manage Books', 'Manage Books', $nr_level, 'manage_books', 'nr_manage');
		add_submenu_page('add_book', 'Options', 'Options', 9, 'nr_options', 'nr_options');

    } else {
        if ( file_exists( ABSPATH . '/wp-admin/post-new.php' ) )
            add_submenu_page('post-new.php', 'Add a Book', 'Add a Book', $nr_level, 'add_book', 'now_reading_add');
        else
            add_submenu_page('post.php', 'Add a Book', 'Add a Book', $nr_level, 'add_book', 'now_reading_add');

        add_management_page('Now Reading', 'Manage Books', $nr_level, 'manage_books', 'nr_manage');
        add_options_page('Now Reading', 'Now Reading', 9, 'nr_options', 'nr_options');
    }
}
add_action('admin_menu', 'nr_add_pages');

?>
