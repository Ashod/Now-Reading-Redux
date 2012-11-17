<?php
/**
 * Admin interface for managing options.
 * @package now-reading
 */

if( !isset($_SERVER['REQUEST_URI']) ) {
    $arr = explode("/", $_SERVER['PHP_SELF']);
    $_SERVER['REQUEST_URI'] = "/" . $arr[count($arr) - 1];
    if ( !empty($_SERVER['argv'][0]) )
        $_SERVER['REQUEST_URI'] .= "?{$_SERVER['argv'][0]}";
}

/**
 * Creates the options admin page and manages the updating of options.
 */
function nr_options()
{
    global $wpdb, $nr_domains;

    $options = get_option(NOW_READING_OPTIONS);

    if ( !empty($_GET['curl']) ) {
        echo '
			<div id="message" class="error fade">
				<p><strong>Oops!</strong></p>
				<p>You don\'t appear to have cURL installed!</p>
				<p>Since you can\'t use cURL, I\'ve switched your HTTP Library setting to <strong>Snoopy</strong> instead, which should work.</p>
			</div>
		';
    }

    if ( !empty($_GET['imagesize']) ) {
        echo '
			<div id="message" class="error fade">
				<p><strong>Oops!</strong></p>
				<p>Naughty naughty! That wasn\'t a valid value for the image size setting!</p>
				<p>Don\'t worry, I\'ve set it to medium for you.</p>
			</div>
		';
    }

    if( !strstr($_SERVER['REQUEST_URI'], 'wp-admin/options') && $_GET['updated'] ) {
        echo '
			<div id="message" class="updated fade">
				<p><strong>Options saved.</strong></p>
			</div>
		';
    }

    echo '
	<div class="wrap">

		<h2>Now Reading Redux</h2>
		<i>Version: ' . NOW_READING_VERSION . '</i>
	';

    echo '
		<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/now-reading-redux/admin/options.php">
	';

    if ( function_exists('wp_nonce_field') )
        wp_nonce_field('now-reading-update-options');

    echo '
		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
		
			<tr valign="top">
				<th scope="row"> <h3>Library Options</h3><hr /></th>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryTitle"><b>' . __('Library title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="libraryTitle" id="libraryTitle" value="' . text_or_default($options['libraryOptions']['title'], DEFAULT_LIBRARY_TITLE) . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'libraryTitle\').value=\'' . DEFAULT_LIBRARY_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryReadingShelfTitle"><b>' . __('Reading shelf title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="libraryReadingShelfTitle" id="libraryReadingShelfTitle" value="' . text_or_default($options['libraryOptions']['readingShelf']['title'], '') . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'libraryReadingShelfTitle\').value=\'' . DEFAULT_READING_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryReadingShelfViz"><b>' . __('Reading shelf visual', NRTD) . ':</b></label></th>
				<td>
					<select name="libraryReadingShelfViz" id="libraryReadingShelfViz">
						<option' . ( ($options['libraryOptions']['readingShelf']['viz'] == 'hide') ? ' selected="selected"' : '' ) . ' value="hide">' . __("Hide", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['readingShelf']['viz'] == 'show_image') ? ' selected="selected"' : '' ) . ' value="show_image">' . __("Show image only", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['readingShelf']['viz'] == 'show_text') ? ' selected="selected"' : '' ) . ' value="show_text">' . __("Show text only", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['readingShelf']['viz'] == 'show_image_text') ? ' selected="selected"' : '' ) . ' value="show_image_text">' . __("Show both image and text", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryUnreadShelfTitle"><b>' . __('Unread shelf title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="libraryUnreadShelfTitle" id="libraryUnreadShelfTitle" value="' . text_or_default($options['libraryOptions']['unreadShelf']['title'], '') . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'libraryUnreadShelfTitle\').value=\'' . DEFAULT_UNREAD_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryUnreadShelfViz"><b>' . __('Unread shelf visual', NRTD) . ':</b></label></th>
				<td>
					<select name="libraryUnreadShelfViz" id="libraryUnreadShelfViz">
						<option' . ( ($options['libraryOptions']['unreadShelf']['viz'] == 'hide') ? ' selected="selected"' : '' ) . ' value="hide">' . __("Hide", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['unreadShelf']['viz'] == 'show_image') ? ' selected="selected"' : '' ) . ' value="show_image">' . __("Show image only", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['unreadShelf']['viz'] == 'show_text') ? ' selected="selected"' : '' ) . ' value="show_text">' . __("Show text only", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['unreadShelf']['viz'] == 'show_image_text') ? ' selected="selected"' : '' ) . ' value="show_image_text">' . __("Show both image and text", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryOnholdShelfTitle"><b>' . __('On Hold shelf title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="libraryOnholdShelfTitle" id="libraryOnholdShelfTitle" value="' . text_or_default($options['libraryOptions']['onholdShelf']['title'], '') . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'libraryOnholdShelfTitle\').value=\'' . DEFAULT_ONHOLD_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryOnholdShelfViz"><b>' . __('On Hold shelf visual', NRTD) . ':</b></label></th>
				<td>
					<select name="libraryOnholdShelfViz" id="libraryOnholdShelfViz">
						<option' . ( ($options['libraryOptions']['onholdShelf']['viz'] == 'hide') ? ' selected="selected"' : '' ) . ' value="hide">' . __("Hide", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['onholdShelf']['viz'] == 'show_image') ? ' selected="selected"' : '' ) . ' value="show_image">' . __("Show image only", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['onholdShelf']['viz'] == 'show_text') ? ' selected="selected"' : '' ) . ' value="show_text">' . __("Show text only", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['onholdShelf']['viz'] == 'show_image_text') ? ' selected="selected"' : '' ) . ' value="show_image_text">' . __("Show both image and text", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryReadShelfTitle"><b>' . __('Finished shelf title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="libraryReadShelfTitle" id="libraryReadShelfTitle" value="' . text_or_default($options['libraryOptions']['readShelf']['title'], '') . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'libraryReadShelfTitle\').value=\'' .DEFAULT_READ_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryReadShelfViz"><b>' . __('Finished shelf visual', NRTD) . ':</b></label></th>
				<td>
					<select name="libraryReadShelfViz" id="libraryReadShelfViz">
						<option' . ( ($options['libraryOptions']['readShelf']['viz'] == 'hide') ? ' selected="selected"' : '' ) . ' value="hide">' . __("Hide", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['readShelf']['viz'] == 'show_image') ? ' selected="selected"' : '' ) . ' value="show_image">' . __("Show image only", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['readShelf']['viz'] == 'show_text') ? ' selected="selected"' : '' ) . ' value="show_text">' . __("Show text only", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['readShelf']['viz'] == 'show_image_text') ? ' selected="selected"' : '' ) . ' value="show_image_text">' . __("Show both image and text", NRTD) . '</option>
					</select>
					<p>
					' . __('Also used for Tag and Author pages.', NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryCss"><b>' . __("Library CSS code", NRTD) . ':</b></label></th>
				<td>
					<textarea name="libraryCss" id="libraryCss" rows="6" cols="75">' . $options['libraryOptions']['css'] . '</textarea>
					<br />
					<button type="button" onclick="document.getElementById(\'libraryCss\').value=\'' . str_replace("\r", "", str_replace("\n", "", DEFAULT_LIBRARY_CSS)) . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryRenderStyle"><b>' . __('Rendering style', NRTD) . ':</b></label></th>
				<td>
					<select name="libraryRenderStyle" id="libraryRenderStyle">
						<option' . ( ($options['libraryOptions']['renderStyle'] == 'list') ? ' selected="selected"' : '' ) . ' value="list">' . __("List", NRTD) . '</option>
						<option' . ( ($options['libraryOptions']['renderStyle'] == 'table') ? ' selected="selected"' : '' ) . ' value="table">' . __("Table", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryItemsPerTableRow"><b>' . __("Items per table row", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="libraryItemsPerTableRow" id="libraryItemsPerTableRow" style="width:4em;" value="' . ( intval($options['libraryOptions']['itemsPerTableRow']) ) . '" />
					<p>
					' . __("Number of table columns used to render the sidebar. Only meaningful when \"Rendering style\" is \"Table\". For \"List\" this is 1 by default but automatically rearanged via CSS.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="libraryShowStats"><b>' . __("Show statistics", NRTD) . ':</b></label></th>
				<td>
					<input type="checkbox" name="libraryShowStats" id="libraryShowStats"' . ( ($options['libraryOptions']['showStats']) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("With this option set, Now Reading will generate a graph showing the number of books read per month during the past year and a summary of the anual average.", NRTD) . '
					</p>
				</td>
			</tr>			
			<tr valign="top">
				<th scope="row"><label for="use_mod_rewrite"><b>' . __("Use <code>mod_rewrite</code> enhanced library", NRTD) . ':</b></label></th>
				<td>
					<input type="checkbox" name="use_mod_rewrite" id="use_mod_rewrite"' . ( ($options['useModRewrite']) ? ' checked="checked"' : '' ) . ' />
					<p>
						' . __("If you have an Apache webserver with <code>mod_rewrite</code>, you can enable this option to have your library use prettier URLs. Compare:", NRTD) . '
					</p>
					<p>
						<code>/index.php?now_reading_single=true&now_reading_author=albert-camus&now_reading_title=the-stranger</code>
					</p>
					<p>
						<code>/library/albert-camus/the-stranger/</code>
					</p>
					<p>
						' . sprintf(__("If you choose this option, be sure you have a custom permalink structure set up at your <a href='%s'>Options &rarr; Permalinks</a> page.", NRTD), 'options-permalink.php') . '
					</p>
					<p>
					' . __("Permalink base:") . ' ' . htmlentities(get_option('home')) . '/
					<input type="text" name="permalink_base" id="permalink_base" value="' . htmlentities($options['permalinkBase']) . '" /></p>
				</td>
			</tr>
		
			<tr valign="top">
				<th scope="row"> <h3>Sidebar Widget Options</h3><hr /></th>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarReadingShelfTitle"><b>' . __('Reading shelf title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="sidebarReadingShelfTitle" id="sidebarReadingShelfTitle" value="' . text_or_default($options['sidebarOptions']['readingShelf']['title'], '') . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'sidebarReadingShelfTitle\').value=\'' . DEFAULT_READING_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarReadingShelfViz"><b>' . __('Reading shelf visual', NRTD) . ':</b></label></th>
				<td>
					<select name="sidebarReadingShelfViz" id="sidebarReadingShelfViz">
						<option' . ( ($options['sidebarOptions']['readingShelf']['viz'] == 'hide') ? ' selected="selected"' : '' ) . ' value="hide">' . __("Hide", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['readingShelf']['viz'] == 'show_image') ? ' selected="selected"' : '' ) . ' value="show_image">' . __("Show image only", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['readingShelf']['viz'] == 'show_text') ? ' selected="selected"' : '' ) . ' value="show_text">' . __("Show text only", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['readingShelf']['viz'] == 'show_image_text') ? ' selected="selected"' : '' ) . ' value="show_image_text">' . __("Show both image and text", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarReadingShelfMaxItems"><b>' . __("Reading shelf items limit", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="sidebarReadingShelfMaxItems" id="sidebarReadingShelfMaxItems" style="width:4em;" value="' . ( intval($options['sidebarOptions']['readingShelf']['maxItems']) ) . '" />
					<p>' . __("This controls the maximum number of items shown on this shelf. Negative value (-1, for example) to show all.") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarUnreadShelfTitle"><b>' . __('Unread shelf title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="sidebarUnreadShelfTitle" id="sidebarUnreadShelfTitle" value="' . text_or_default($options['sidebarOptions']['unreadShelf']['title'], '') . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'sidebarUnreadShelfTitle\').value=\'' . DEFAULT_UNREAD_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarUnreadShelfViz"><b>' . __('Unread shelf visual', NRTD) . ':</b></label></th>
				<td>
					<select name="sidebarUnreadShelfViz" id="sidebarUnreadShelfViz">
						<option' . ( ($options['sidebarOptions']['unreadShelf']['viz'] == 'hide') ? ' selected="selected"' : '' ) . ' value="hide">' . __("Hide", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['unreadShelf']['viz'] == 'show_image') ? ' selected="selected"' : '' ) . ' value="show_image">' . __("Show image only", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['unreadShelf']['viz'] == 'show_text') ? ' selected="selected"' : '' ) . ' value="show_text">' . __("Show text only", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['unreadShelf']['viz'] == 'show_image_text') ? ' selected="selected"' : '' ) . ' value="show_image_text">' . __("Show both image and text", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarUnreadShelfMaxItems"><b>' . __("Unread shelf items limit", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="sidebarUnreadShelfMaxItems" id="sidebarUnreadShelfMaxItems" style="width:4em;" value="' . ( intval($options['sidebarOptions']['unreadShelf']['maxItems']) ) . '" />
					<p>' . __("This controls the maximum number of items shown on this shelf. Negative value (-1, for example) to show all.") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarOnholdShelfTitle"><b>' . __('On Hold shelf title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="sidebarOnholdShelfTitle" id="sidebarOnholdShelfTitle" value="' . text_or_default($options['sidebarOptions']['onholdShelf']['title'], '') . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'sidebarOnholdShelfTitle\').value=\'' . DEFAULT_ONHOLD_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarOnholdShelfViz"><b>' . __('On Hold shelf visual', NRTD) . ':</b></label></th>
				<td>
					<select name="sidebarOnholdShelfViz" id="sidebarOnholdShelfViz">
						<option' . ( ($options['sidebarOptions']['onholdShelf']['viz'] == 'hide') ? ' selected="selected"' : '' ) . ' value="hide">' . __("Hide", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['onholdShelf']['viz'] == 'show_image') ? ' selected="selected"' : '' ) . ' value="show_image">' . __("Show image only", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['onholdShelf']['viz'] == 'show_text') ? ' selected="selected"' : '' ) . ' value="show_text">' . __("Show text only", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['onholdShelf']['viz'] == 'show_image_text') ? ' selected="selected"' : '' ) . ' value="show_image_text">' . __("Show both image and text", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarOnholdShelfMaxItems"><b>' . __("On Hold shelf items limit", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="sidebarOnholdShelfMaxItems" id="sidebarOnholdShelfMaxItems" style="width:4em;" value="' . ( intval($options['sidebarOptions']['onholdShelf']['maxItems']) ) . '" />
					<p>' . __("This controls the maximum number of items shown on this shelf. Negative value (-1, for example) to show all.") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarReadShelfTitle"><b>' . __('Finished shelf title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="sidebarReadShelfTitle" id="sidebarReadShelfTitle" value="' . text_or_default($options['sidebarOptions']['readShelf']['title'], '') . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'sidebarReadShelfTitle\').value=\'' . DEFAULT_READ_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarReadShelfViz"><b>' . __('Finished shelf visual', NRTD) . ':</b></label></th>
				<td>
					<select name="sidebarReadShelfViz" id="sidebarReadShelfViz">
						<option' . ( ($options['sidebarOptions']['readShelf']['viz'] == 'hide') ? ' selected="selected"' : '' ) . ' value="hide">' . __("Hide", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['readShelf']['viz'] == 'show_image') ? ' selected="selected"' : '' ) . ' value="show_image">' . __("Show image only", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['readShelf']['viz'] == 'show_text') ? ' selected="selected"' : '' ) . ' value="show_text">' . __("Show text only", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['readShelf']['viz'] == 'show_image_text') ? ' selected="selected"' : '' ) . ' value="show_image_text">' . __("Show both image and text", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarReadShelfMaxItems"><b>' . __("Finished shelf items limit", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="sidebarReadShelfMaxItems" id="sidebarReadShelfMaxItems" style="width:4em;" value="' . ( intval($options['sidebarOptions']['readShelf']['maxItems']) ) . '" />
					<p>' . __("This controls the maximum number of items shown on this shelf. Negative value (-1, for example) to show all.") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarCss"><b>' . __("Sidebar CSS code", NRTD) . ':</b></label></th>
				<td>
					<textarea name="sidebarCss" id="sidebarCss" rows="6" cols="75">' . $options['sidebarOptions']['css'] . '</textarea>
					<br />
					<button type="button" onclick="document.getElementById(\'sidebarCss\').value=\'' . str_replace("\r", "", str_replace("\n", "", DEFAULT_SIDEBAR_CSS)) . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarRenderStyle"><b>' . __('Rendering style', NRTD) . ':</b></label></th>
				<td>
					<select name="sidebarRenderStyle" id="sidebarRenderStyle">
						<option' . ( ($options['sidebarOptions']['renderStyle'] == 'list') ? ' selected="selected"' : '' ) . ' value="list">' . __("List", NRTD) . '</option>
						<option' . ( ($options['sidebarOptions']['renderStyle'] == 'table') ? ' selected="selected"' : '' ) . ' value="table">' . __("Table", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="sidebarItemsPerTableRow"><b>' . __("Items per table row", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="sidebarItemsPerTableRow" id="sidebarItemsPerTableRow" style="width:4em;" value="' . ( intval($options['sidebarOptions']['itemsPerTableRow']) ) . '" />
					<p>
					' . __("Number of table columns used to render the sidebar. Only meaningful when \"Rendering style\" is \"Table\". For \"List\" this is 1 by default but automatically rearanged via CSS.", NRTD) . '
					</p>
					<p>
					' . __("Note: The sidebar width is limited and, depending on the image width (which can be controlled by the CSS,) a wide table may be problematic. Try 2 or 3.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="wishlistTitle"><b>' . __('Wishlist Title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="wishlistTitle" id="wishlistTitle" size="75" value="' . text_or_default($options['wishlistTitle'], DEFAULT_WISHLIST_TITLE) . '" />
					<p>
					' . __("The link title to a wishlist page. Will not show unless Wishlist URL exists.", NRTD) . '
					</p>
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'wishlistTitle\').value=\'' . DEFAULT_WISHLIST_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="wishlistUrl"><b>' . __('Wishlist URL', NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="wishlistUrl" id="wishlistUrl" size="75" value="' . text_or_default($options['wishlistUrl'], ENT_QUOTES, "UTF-8") . '" />
					<p>
					' . __("An optional link shown as <i>Wishlist Title</i>. Typically used to link to an Amazon wishlist page, but can be any page.", NRTD) . '
					</p>
					<p>
					' . __("Add 'http://' to make the URL absolute and not relative to the current page.", NRTD) . '
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"> <h3>Search Options</h3><hr /></th>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="searchTitle"><b>' . __('Search page title', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="30" style="vertical-align:middle;" name="searchTitle" id="searchTitle" value="' . text_or_default($options['searchOptions']['title'], '') . '" />
					<button type="button" style="vertical-align:middle; height: 25px; width: 100px" onclick="document.getElementById(\'searchTitle\').value=\'' . DEFAULT_SEARCH_TITLE . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="searchViz"><b>' . __('Search page visual', NRTD) . ':</b></label></th>
				<td>
					<select name="searchViz" id="searchViz">
						<option' . ( ($options['searchOptions']['viz'] == 'show_image') ? ' selected="selected"' : '' ) . ' value="show_image">' . __("Show image only", NRTD) . '</option>
						<option' . ( ($options['searchOptions']['viz'] == 'show_text') ? ' selected="selected"' : '' ) . ' value="show_text">' . __("Show text only", NRTD) . '</option>
						<option' . ( ($options['searchOptions']['viz'] == 'show_image_text') ? ' selected="selected"' : '' ) . ' value="show_image_text">' . __("Show both image and text", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="searchMaxItems"><b>' . __("Search items limit", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="searchMaxItems" id="searchMaxItems" style="width:4em;" value="' . ( intval($options['searchOptions']['maxItems']) ) . '" />
					<p>' . __("This controls the maximum number of items shown on this shelf. Negative value (-1, for example) to show all.") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="searchCss"><b>' . __("search CSS code", NRTD) . ':</b></label></th>
				<td>
					<textarea name="searchCss" id="searchCss" rows="6" cols="75">' . $options['searchOptions']['css'] . '</textarea>
					<br />
					<button type="button" onclick="document.getElementById(\'searchCss\').value=\'' . str_replace("\r", "", str_replace("\n", "", DEFAULT_SEARCH_CSS)) . '\'">' . __("Default", NRTD) . '</button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="searchRenderStyle"><b>' . __('Rendering style', NRTD) . ':</b></label></th>
				<td>
					<select name="searchRenderStyle" id="searchRenderStyle">
						<option' . ( ($options['searchOptions']['renderStyle'] == 'list') ? ' selected="selected"' : '' ) . ' value="list">' . __("List", NRTD) . '</option>
						<option' . ( ($options['searchOptions']['renderStyle'] == 'table') ? ' selected="selected"' : '' ) . ' value="table">' . __("Table", NRTD) . '</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="searchItemsPerTableRow"><b>' . __("Items per table row", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="searchItemsPerTableRow" id="searchItemsPerTableRow" style="width:4em;" value="' . ( intval($options['searchOptions']['itemsPerTableRow']) ) . '" />
					<p>
					' . __("Number of table columns used to render the search. Only meaningful when \"Rendering style\" is \"Table\". For \"List\" this is 1 by default but automatically rearanged via CSS.", NRTD) . '
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"> <h3>Manage Page Options</h3><hr /></th>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="format_date"><b>' . __('Date format string', NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="format_date" id="format_date" value="' . htmlentities($options['formatDate'], ENT_QUOTES, "UTF-8") . '" />
					<p>
					' . sprintf(__("How to format the book's <code>added</code>, <code>started</code> and <code>finished</code> dates. Default is <code>jS F Y</code>. Acceptable variables can be found <a href='%s'>here</a>.", NRTD), "http://php.net/date") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="ignore_time"><b>' . __('No time in timestamps', NRTD) . ':</b></label></th>
				<td>
					<input type="checkbox" name="ignore_time" id="ignore_time"' . ( ($options['ignoreTime']) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("When checked <code>added</code>, <code>started</code> and <code>finished</code> dates will be displayed with day precision only, however when time is set, it will be saved.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="hide_added_date"><b>' . __('Hide Added Date', NRTD) . ':</b></label></th>
				<td>
					<input type="checkbox" name="hide_added_date" id="hide_added_date"' . ( ($options['hideAddedDate']) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("When checked <code>added</code> date will be hidden in the Manager and Book pages", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="books_per_page"><b>' . __("Books per page", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="books_per_page" id="books_per_page" style="width:4em;" value="' . ( intval($options['booksPerPage']) ) . '" />
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"> <h3>Amazon Options</h3><hr /></th>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="AWSAccessKeyId"><b>' . __('Amazon Web Services Access Key ID', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="70" name="AWSAccessKeyId" id="AWSAccessKeyId" value="' . htmlentities($options['AWSAccessKeyId'], ENT_QUOTES, "UTF-8") . '" />
					<p>
					' . sprintf(__("Required to add books from Amazon.  It's free to sign up. Register <a href='%s'>here</a>.", NRTD), "https://aws-portal.amazon.com/gp/aws/developer/registration/index.html") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="SecretAccessKey"><b>' . __('Amazon Web Services Secret Access Key', NRTD) . ':</b></label></th>
				<td>
					<input type="text" size="70" name="SecretAccessKey" id="SecretAccessKey" value="' . htmlentities($options['SecretAccessKey'], ENT_QUOTES, "UTF-8") . '" />
					<p>
					' . sprintf(__("Required to add books from Amazon.  Found at the same site as above. Register <a href='%s'>here</a>.", NRTD), "https://aws-portal.amazon.com/gp/aws/developer/registration/index.html") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="associate"><b>' . __('Your Amazon Associates ID', NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="associate" id="associate" value="' . htmlentities($options['associate'], ENT_QUOTES, "UTF-8") . '" />
					<p>
					' . __("If you choose to link to your book's product page on Amazon.com using the <code>book_url()</code> template tag - as the default template does - then you can earn commission if your visitors then purchase products.", NRTD) . '
					</p>
					<p>
					' . sprintf(__("If you don't have an Amazon Associates ID, you can either <a href='%s'>get one</a>, or consider entering mine - <strong>%s</strong> - if you're feeling generous.", NRTD), "http://associates.amazon.com", "thevoid0f-20") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="domain"><b>' . __('Amazon domain to use', NRTD) . ':</b></label></th>
				<td>
					<select name="domain" id="domain">
	';
					foreach ( (array) $nr_domains as $domain => $country )
					{
						$selected = ($domain == $options['domain']) ? ' selected="selected"' : '';
						echo "<option value='$domain'$selected>$country (Amazon$domain)</option>";
					}	
			echo '
					</select>
					<p>
					' . __("If you choose to link to your book's product page on Amazon.com using the <code>book_url()</code> template tag, you can specify which country-specific Amazon site to link to. Now Reading will also use this domain when searching.", NRTD) . '
					</p>
					<p>
					' . __("NB: If you have country-specific books in your catalogue and then change your domain setting, some old links might stop working.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="image_size"><b>' . __('Image size to use', NRTD) . ':</b></label></th>
				<td>
					<select name="image_size" id="image_size">
						<option' . ( ($options['imageSize'] == 'Small') ? ' selected="selected"' : '' ) . ' value="Small">' . __("Small", NRTD) . '</option>
						<option' . ( ($options['imageSize'] == 'Medium') ? ' selected="selected"' : '' ) . ' value="Medium">' . __("Medium", NRTD) . '</option>
						<option' . ( ($options['imageSize'] == 'Large') ? ' selected="selected"' : '' ) . ' value="Large">' . __("Large", NRTD) . '</option>
					</select>
					<p>
					' . __("NB: This change will only be applied to books you add from this point onwards.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="http_lib"><b>' . __("HTTP Library", NRTD) . ':</b></label></th>
				<td>
					<select name="http_lib" id="http_lib">
						<option' . ( ($options['httpLib'] == 'snoopy') ? ' selected="selected"' : '' ) . ' value="snoopy">Snoopy</option>
						<option' . ( ($options['httpLib'] == 'curl') ? ' selected="selected"' : '' ) . ' value="curl">cURL</option>
					</select>
					<p>
					' . __("Don't worry if you don't understand this; unless you're having problems searching for books, the default setting will be fine.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="proxy_host"><b>' . __("Proxy hostname and port", NRTD) . ':</b></label></th>
				<td>
					<input type="text" name="proxy_host" id="proxy_host" size="50" value="' . $options['proxyHost'] . '" />:<input type="text" name="proxy_port" id="proxy_port" style="width:4em;" value="' . $options['proxyPort'] . '" />
					<p>
					' . __("Don't worry if you don't understand this; unless you're having problems searching for books, the default setting will be fine.", NRTD) . '
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"> <h3>Admin Options</h3><hr /></th>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="multiuser_mode"><b>' . __("Multiuser mode", NRTD) . ':</b></label></th>
				<td>
					<input type="checkbox" name="multiuser_mode" id="multiuser_mode"' . ( ($options['multiuserMode']) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("If you have a multi-user blog, setting this option will enable you to specify which user is reading which book.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><b>' . __('Admin menu layout', NRTD) . ':</th>
				<td>
					<label for="menu_layout_single">' . __('Single', NRTD) . '</label>
					<input type="radio" name="menu_layout" id="menu_layout_single" value="single"' . ( ( $options['menuLayout'] == NR_MENU_SINGLE ) ? ' checked="checked"' : '' ) . ' />
					<br />
					<label for="menu_layout_multiple">' . __('Multiple', NRTD) . '</label>
					<input type="radio" name="menu_layout" id="menu_layout_multiple" value="multiple"' . ( ( $options['menuLayout'] == NR_MENU_MULTIPLE ) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("When set to 'Single', Now Reading will add a top-level menu with submenus containing the 'Add a Book', 'Manage Books' and 'Options' screens.", NRTD) . '
					</p>
					<p>
					' . __("When set to 'Multiple', Now Reading will insert those menus under 'Write', 'Manage' and 'Options' respectively.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="debug_mode"><b>' . __("Debug mode", NRTD) . ':</b></label></th>
				<td>
					<input type="checkbox" name="debug_mode" id="debug_mode"' . ( ($options['debugMode']) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("With this option set, Now Reading will produce debugging output that might help you solve problems or at least report bugs.", NRTD) . '
					</p>
				</td>
			</tr>

		</table>

		<input type="hidden" name="update" value="yes" />
		<p class="submit">
			<input type="submit" value="' . __("Update Options", NRTD) . '" />
		</p>
		</form>
	</div>
	';
}

?>
