<?php
/**
 * Handles the adding of new books.
 * @package now-reading-redux
 */

// Load wp_config.php needed for current_user_can().
$wp_config = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-config.php';
if (file_exists($wp_config))
{
    // The config file is in its default folder.
    require($wp_config);
}
elseif (file_exists(dirname($wp_config) . '/wp-config.php') &&
        !file_exists(dirname($wp_config) . '/wp-settings.php'))
{
    // The config file is one level above its default folder but isn't part of another install.
    require(dirname($wp_config) . '/wp-config.php');
}

if (!current_user_can('publish_posts'))
{
    die (__('Cheatin&#8217; uh?'));
}

$_POST = stripslashes_deep($_POST);

if (!empty($_POST['amazon_data']))
{
    $data = unserialize(stripslashes($_POST['amazon_data']));

    $b_author = $data['author'];
    $b_title = $data['title'];
    $b_image = $data['image'];
    $b_asin = $data['asin'];
    $b_added = date('Y-m-d H:i:s');
    $b_status = 'unread';
    $b_nice_title = sanitize_title($data['title']);
    $b_nice_author = sanitize_title($data['author']);

    check_admin_referer('now-reading-add');

    $query = '';
    foreach ( (array) compact('b_author', 'b_title', 'b_image', 'b_asin', 'b_added', 'b_status', 'b_nice_title', 'b_nice_author') as $field => $value )
        $query .= "$field=$value&";
    $query = apply_filters('add_book_query', $query);

    $redirect = $nr_url->urls['add'];

    $id = add_book($query);
    if ( $id > 0 ) {
        wp_redirect("$redirect&added=$id");
        die;
    } else {
        wp_redirect("$redirect&error=true");
        die;
    }
} elseif ( !empty($_POST['custom_title']) ) {

    check_admin_referer('now-reading-manual-add');

    $b_author = $wpdb->escape($_POST['custom_author']);
    $b_title = $wpdb->escape($_POST['custom_title']);
    if ( !empty($_POST['custom_image']) )
        $b_image = $wpdb->escape($_POST['custom_image']);
    else
        $b_image = get_option('siteurl') . '/' . PLUGINDIR . '/now-reading-redux/no-image.png';
    $b_asin = '';
    $b_added = date('Y-m-d H:i:s');
    $b_status = 'unread';
    $b_nice_title = $wpdb->escape(sanitize_title($_POST['custom_title']));
    $b_nice_author = $wpdb->escape(sanitize_title($_POST['custom_author']));

    foreach ( (array) compact('b_author', 'b_title', 'b_image', 'b_asin', 'b_added', 'b_status', 'b_nice_title', 'b_nice_author') as $field => $value )
        $query .= "$field=$value&";

    $id = add_book($query);
    if ( $id > 0 ) {
        wp_redirect($nr_url->urls['add'] . '&added=' . intval($id));
        die;
    } else {
        wp_redirect($nr_url->urls['add'] . '&error=true');
        die;
    }
}

?>
