<?php
/**
 * Updates our plugin options.
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

if (!current_user_can('manage_options'))
{
	die (__('Cheatin&#8217; uh?'));
}

check_admin_referer('now-reading-update-options');

$_POST = stripslashes_deep($_POST);

if (!empty($_POST['update']))
{
    $append = '';

    $options['libraryOptions']['readingShelf']['viz'] = trim($_POST['libraryReadingShelfViz']);
    $options['libraryOptions']['readingShelf']['title'] = trim($_POST['libraryReadingShelfTitle']);
    $options['libraryOptions']['unreadShelf']['viz'] = trim($_POST['libraryUnreadShelfViz']);
    $options['libraryOptions']['unreadShelf']['title'] = trim($_POST['libraryUnreadShelfTitle']);
    $options['libraryOptions']['onholdShelf']['viz'] = trim($_POST['libraryOnholdShelfViz']);
    $options['libraryOptions']['onholdShelf']['title'] = trim($_POST['libraryOnholdShelfTitle']);
    $options['libraryOptions']['readShelf']['viz'] = trim($_POST['libraryReadShelfViz']);
    $options['libraryOptions']['readShelf']['title'] = trim($_POST['libraryReadShelfTitle']);
    $options['libraryOptions']['css'] = trim($_POST['libraryCss']);
	$options['libraryOptions']['renderStyle'] = trim($_POST['libraryRenderStyle']);
    $options['libraryOptions']['itemsPerTableRow'] = trim($_POST['libraryItemsPerTableRow']);
    $options['libraryOptions']['showStats'] = trim($_POST['libraryShowStats']);

    $options['sidebarOptions']['readingShelf']['viz'] = trim($_POST['sidebarReadingShelfViz']);
    $options['sidebarOptions']['readingShelf']['title'] = trim($_POST['sidebarReadingShelfTitle']);
    $options['sidebarOptions']['readingShelf']['maxItems'] = trim($_POST['sidebarReadingShelfMaxItems']);
    $options['sidebarOptions']['unreadShelf']['viz'] = trim($_POST['sidebarUnreadShelfViz']);
    $options['sidebarOptions']['unreadShelf']['title'] = trim($_POST['sidebarUnreadShelfTitle']);
    $options['sidebarOptions']['unreadShelf']['maxItems'] = trim($_POST['sidebarUnreadShelfMaxItems']);
    $options['sidebarOptions']['onholdShelf']['viz'] = trim($_POST['sidebarOnholdShelfViz']);
    $options['sidebarOptions']['onholdShelf']['title'] = trim($_POST['sidebarOnholdShelfTitle']);
    $options['sidebarOptions']['onholdShelf']['maxItems'] = trim($_POST['sidebarOnholdShelfMaxItems']);
    $options['sidebarOptions']['readShelf']['viz'] = trim($_POST['sidebarReadShelfViz']);
    $options['sidebarOptions']['readShelf']['title'] = trim($_POST['sidebarReadShelfTitle']);
    $options['sidebarOptions']['readShelf']['maxItems'] = trim($_POST['sidebarReadShelfMaxItems']);
    $options['sidebarOptions']['css'] = trim($_POST['sidebarCss']);
	$options['sidebarOptions']['renderStyle'] = trim($_POST['sidebarRenderStyle']);
    $options['sidebarOptions']['itemsPerTableRow'] = trim($_POST['sidebarItemsPerTableRow']);

	$options['searchOptions']['viz'] = trim($_POST['searchViz']);
	$options['searchOptions']['title'] = trim($_POST['searchTitle']);
	$options['searchOptions']['maxItems'] = trim($_POST['searchMaxItems']);
	$options['searchOptions']['css'] = trim($_POST['searchCss']);
	$options['searchOptions']['renderStyle'] = trim($_POST['searchRenderStyle']);
	$options['searchOptions']['itemsPerTableRow'] = trim($_POST['searchItemsPerTableRow']);

	$options['AWSAccessKeyId']  = trim($_POST['AWSAccessKeyId']);
    $options['SecretAccessKey'] = trim($_POST['SecretAccessKey']);
    $options['formatDate']		= trim($_POST['format_date']);
    $options['wishlistUrl']		= trim($_POST['wishlist_url']);
    $options['associate']		= trim($_POST['associate']);
    $options['ignoreTime']		= trim($_POST['ignore_time']);
    $options['hideAddedDate']	= trim($_POST['hide_added_date']);
    $options['domain']			= trim($_POST['domain']);
    $options['debugMode']		= trim($_POST['debug_mode']);
    $options['useModRewrite']   = trim($_POST['use_mod_rewrite']);
    $options['menuLayout']		= ( trim($_POST['menu_layout']) == 'single' ) ? NR_MENU_SINGLE : NR_MENU_MULTIPLE;
    $options['proxyHost']		= trim($_POST['proxy_host']);
    $options['proxyPort']		= trim($_POST['proxy_port']);
    $options['booksPerPage']    = trim($_POST['books_per_page']);
    $options['permalinkBase']   = trim($_POST['permalink_base']);
    $options['multiuserMode']   = trim($_POST['multiuser_mode']);

    $nr_url->load_scheme($options['menuLayout']);

    switch ( $_POST['image_size'] ) {
        case 'Small':
        case 'Medium':
        case 'Large':
            $options['imageSize'] = $_POST['image_size'];
            break;
        default:
            $append .= '&imagesize=1';
            $options['imageSize'] = 'Medium';
            break;
    }

    if ( $_POST['http_lib'] == 'curl' ) {
        if ( !function_exists('curl_init') ) {
            $options['httpLib'] = 'snoopy';
            $append .= '&curl=1';
        } else {
            $options['httpLib'] = 'curl';
        }
    } else {
        $options['httpLib'] = 'snoopy';
    }

    update_option(NOW_READING_OPTIONS, $options);

    global $wp_rewrite;
    if ($wp_rewrite->using_mod_rewrite_permalinks() ) {
        nr_mod_rewrite($wp_rewrite->rewrite_rules() );
        $wp_rewrite->flush_rules();
    }

    wp_redirect($nr_url->urls['options'] . "&updated=1$append");
    die;
}

?>
