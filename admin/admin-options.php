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
function nr_options() {

    global $wpdb, $nr_domains;

    $options = get_option('nowReadingOptions');

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
	';

    echo '
		<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/now-reading-redux/admin/options.php">
	';

    if ( function_exists('wp_nonce_field') )
        wp_nonce_field('now-reading-update-options');

    echo '
		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
			<tr valign="top">
				<th scope="row">' . __('Amazon Web Services Access Key ID', NRTD) . '</th>
				<td>
					<input type="text" size="50" name="AWSAccessKeyId" value="' . htmlentities($options['AWSAccessKeyId'], ENT_QUOTES, "UTF-8") . '" />
					<p>
					' . sprintf(__("Required to add books from Amazon.  It's free to sign up. Register <a href='%s'>here</a>.", NRTD), "https://aws-portal.amazon.com/gp/aws/developer/registration/index.html") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __('Amazon Web Services Secret Access Key', NRTD) . '</th>
				<td>
					<input type="text" size="50" name="SecretAccessKey" value="' . htmlentities($options['SecretAccessKey'], ENT_QUOTES, "UTF-8") . '" />
					<p>
					' . sprintf(__("Required to add books from Amazon.  Found at the same site as above. Register <a href='%s'>here</a>.", NRTD), "https://aws-portal.amazon.com/gp/aws/developer/registration/index.html") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __('Date format string', NRTD) . '</th>
				<td>
					<input type="text" name="format_date" value="' . htmlentities($options['formatDate'], ENT_QUOTES, "UTF-8") . '" />
					<p>
					' . sprintf(__("How to format the book's <code>added</code>, <code>started</code> and <code>finished</code> dates. Acceptable variables can be found <a href='%s'>here</a>.", NRTD), "http://php.net/date") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __('No time in timestamps', NRTD) . '</th>
				<td>
					<input type="checkbox" name="ignore_time" id="ignore_time"' . ( ($options['ignoreTime']) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("When checked <code>added</code>, <code>started</code> and <code>finished</code> dates will be displayed with day precision only, however when time is set, it will be saved.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __('Hide Added Date', NRTD) . '</th>
				<td>
					<input type="checkbox" name="hide_added_date" id="hide_added_date"' . ( ($options['hideAddedDate']) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("When checked <code>added</code> date will be hidden in the Manager and Book pages", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __('Your Amazon Associates ID', NRTD) . ':</th>
				<td>
					<input type="text" name="associate" value="' . htmlentities($options['associate'], ENT_QUOTES, "UTF-8") . '" />
					<p>
					' . __("If you choose to link to your book's product page on Amazon.com using the <code>book_url()</code> template tag - as the default template does - then you can earn commission if your visitors then purchase products.", NRTD) . '
					</p>
					<p>
					' . sprintf(__("If you don't have an Amazon Associates ID, you can either <a href='%s'>get one</a>, or consider entering mine - <strong>%s</strong> - if you're feeling generous.", NRTD), "http://associates.amazon.com", "amodcon-20") . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __('Amazon domain to use', NRTD) . ':</th>
				<td>
					<select name="domain">
	';

    foreach ( (array) $nr_domains as $domain => $country ) {
        if ( $domain == $options['domain'] )
            $selected = ' selected="selected"';
        else
            $selected = '';

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
				<th scope="row">' . __('Image size to use', NRTD) . ':</th>
				<td>
					<select name="image_size">
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
				<th scope="row">' . __('Admin menu layout', NRTD) . ':</th>
				<td>
					<label for="menu_layout_single">' . __('Single', NRTD) . '</label>
					<input type="radio" name="menu_layout" id="menu_layout_single" value="single"' . ( ( $options['menuLayout'] == NR_MENU_SINGLE ) ? ' checked="checked"' : '' ) . ' />
					<br />
					<label for="menu_layout_single">' . __('Multiple', NRTD) . '</label>
					<input type="radio" name="menu_layout" id="menu_layout_single" value="multiple"' . ( ( $options['menuLayout'] == NR_MENU_MULTIPLE ) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("When set to 'Single', Now Reading will add a top-level menu with submenus containing the 'Add a Book', 'Manage Books' and 'Options' screens.", NRTD) . '
					</p>
					<p>
					' . __("When set to 'Multiple', Now Reading will insert those menus under 'Write', 'Manage' and 'Options' respectively.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="books_per_page">' . __("Books per page", NRTD) . '</label></th>
				<td>
					<input type="text" name="books_per_page" id="books_per_page" style="width:4em;" value="' . ( intval($options['booksPerPage']) ) . '" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="def_book_count">' . __("Default number of books displayed (used by the sidebar)", NRTD) . '</label></th>
				<td>
					<input type="text" name="def_book_count" id="def_book_count" style="width:4em;" value="' . ( intval($options['defBookCount']) ) . '" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __("Use <code>mod_rewrite</code> enhanced library?", NRTD) . '</th>
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
				<th scope="row">' . __("HTTP Library", NRTD) . '</th>
				<td>
					<select name="http_lib">
						<option' . ( ($options['httpLib'] == 'snoopy') ? ' selected="selected"' : '' ) . ' value="snoopy">Snoopy</option>
						<option' . ( ($options['httpLib'] == 'curl') ? ' selected="selected"' : '' ) . ' value="curl">cURL</option>
					</select>
					<p>
					' . __("Don't worry if you don't understand this; unless you're having problems searching for books, the default setting will be fine.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __("Proxy hostname and port", NRTD) . '</th>
				<td>
					<input type="text" name="proxy_host" id="proxy_host" value="' . $options['proxyHost'] . '" />:<input type="text" name="proxy_port" id="proxy_port" style="width:4em;" value="' . $options['proxyPort'] . '" />
					<p>
					' . __("Don't worry if you don't understand this; unless you're having problems searching for books, the default setting will be fine.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __("Debug mode", NRTD) . '</th>
				<td>
					<input type="checkbox" name="debug_mode" id="debug_mode"' . ( ($options['debugMode']) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("With this option set, Now Reading will produce debugging output that might help you solve problems or at least report bugs.", NRTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __("Multiuser mode", NRTD) . '</th>
				<td>
					<input type="checkbox" name="multiuser_mode" id="multiuser_mode"' . ( ($options['multiuserMode']) ? ' checked="checked"' : '' ) . ' />
					<p>
					' . __("If you have a multi-user blog, setting this option will enable you to specify which user is reading which book.", NRTD) . '
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
