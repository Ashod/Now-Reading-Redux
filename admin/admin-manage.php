<?php
/**
 * The admin interface for managing and editing books.
 * @package now-reading
 */

/**
 * Creates the manage admin page, and deals with the creation and editing of reviews.
 */
function nr_manage() {

    global $wpdb, $nr_statuses, $nr_post_options, $userdata;

    get_currentuserinfo();

    $_POST = stripslashes_deep($_POST);

    $options = get_option('nowReadingOptions');

    if (!$nr_url)
	{
        $nr_url = new nr_url();
        $nr_url->load_scheme($options['menuLayout']);
    }

    if (!empty($_GET['updated']))
	{
        $updated = intval($_GET['updated']);

        if ( $updated == 1 )
            $updated .= ' book';
        else
            $updated .= ' books';

        echo '
		<div id="message" class="updated fade">
			<p><strong>' . $updated . ' updated.</strong></p>
		</div>
		';
    }

    if (!empty($_GET['deleted']))
	{
        $deleted = intval($_GET['deleted']);

        if ($deleted == 1)
            $deleted .= ' book';
        else
            $deleted .= ' books';

        echo '
		<div id="message" class="updated fade">
			<p><strong>' . $deleted . ' deleted.</strong></p>
		</div>
		';
    }

    $action = $_GET['action'];
    nr_reset_vars(array('action'));

	$options = get_option('nowReadingOptions');
	$dateTimeFormat = 'Y-m-d H:i:s';
	if ($options['ignoreTime'])
	{
		$dateTimeFormat = 'Y-m-d';
	}

    switch ($action)
	{
		// Edit Book.
        case 'editsingle':
        {
			$id = intval($_GET['id']);
            $existing = get_book($id);
            $meta = get_book_meta($existing->id);
            $tags = join(get_book_tags($existing->id), ',');

            echo '
			<div class="wrap">
				<h2>' . __("Edit Book", NRTD) . '</h2>

				<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/now-reading-redux/admin/edit.php">
			';

            if ( function_exists('wp_nonce_field') )
                wp_nonce_field('now-reading-edit');
            if ( function_exists('wp_referer_field') )
                wp_referer_field();

            echo '
				<div class="book-image">
					<img style="float:left; margin-right: 10px;" id="book-image-0" alt="Book Cover" src="' . $existing->image . '" />
				</div>

				<h3>' . __("Book", NRTD) . ' ' . $existing->id . ':<br /> <cite>' . $existing->title . '</cite><br /> by ' . $existing->author . '</h3>

				<table class="form-table" cellspacing="2" cellpadding="5">

				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="count" value="1" />
				<input type="hidden" name="id[]" value="' . $existing->id . '" />

				<tbody>
				';

			// Title.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="title-0">' . __("Title", NRTD) . '</label>
					</th>
					<td>
						<input type="text" class="main" id="title-0" name="title[]" value="' . $existing->title . '" />
					</td>
				</tr>
				';

			// Author.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="author-0">' . __("Author", NRTD) . '</label>
					</th>
					<td>
						<input type="text" class="main" id="author-0" name="author[]" value="' . $existing->author . '" />
					</td>
				</tr>
				';

			// ASIN.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
					<label for="asin-0">' . __("ASIN", NRTD) . '</label>
					</th>
					<td>
					<input type="text" class="main" id="asin-0" name="asin[]" value="' . $existing->asin . '" />
					</td>
				</tr>
				';

			// Status.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="status-0">' . __("Status", NRTD) . '</label>
					</th>
					<td>
						<select name="status[]" id="status-0">
							';
				foreach ( (array) $nr_statuses as $status => $name ) {
					$selected = '';
					if ( $existing->status == $status )
						$selected = ' selected="selected"';

					echo '
									<option value="' . $status . '"' . $selected . '>' . $name . '</option>
								';
				}

				echo '
						</select>
					</td>
				</tr>';

			// Visibility.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="visibility-0">' . __("Visibility", NRTD) . '</label>
					</th>
					<td>
						<select name="visibility[]" id="visibility-0">
							';

					if ($existing->visibility)
					{
						// Public.
						echo '
									<option value="0">Private</option>
									<option value="1" selected="selected">Public</option>
								';
					}
					else
					{
						// Private.
						echo '
									<option value="0" selected="selected">Private</option>
									<option value="1">Public</option>
								';
					}

				echo '
						</select>
					</td>
				</tr>';

			// Added Date.
			if (!$options['hideAddedDate'])
			{
				$added = ( nr_empty_date($existing->added) ) ? '' : date($dateTimeFormat, strtotime($existing->added));
				echo '
					<tr class="form-field">
						<th valign="top" scope="row">
							<label for="added[]">' . __("Added", NRTD) . '</label>
						</th>
						<td>
							<input type="text" id="added-0" name="added[]" value="' . htmlentities($added, ENT_QUOTES, "UTF-8") . '" />
						</td>
					</tr>
					';
			}

			// Started Reading Date.
			$started = ( nr_empty_date($existing->started) ) ? '' : date($dateTimeFormat, strtotime($existing->started));
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="started[]">' . __("Started", NRTD) . '</label>
					</th>
					<td>
						<input type="text" id="started-0" name="started[]" value="' . htmlentities($started, ENT_QUOTES, "UTF-8") . '" />
					</td>
				</tr>

				';

			// Finished Reading Date.
			$finished = ( nr_empty_date($existing->finished) ) ? '' : date($dateTimeFormat, strtotime($existing->finished));
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="finished[]">' . __("Finished", NRTD) . '</label>
					</th>
					<td>
						<input type="text" id="finished-0" name="finished[]" value="' . htmlentities($finished, ENT_QUOTES, "UTF-8") . '" />
					</td>
				</tr>

				';

			// Image URL.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="image-0">' . __("Image", NRTD) . '</label>
					</th>
					<td>
						<input type="text" class="main" id="image-0" name="image[]" value="' . htmlentities($existing->image) . '" />
					</td>
				</tr>

				';

			// Tags.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="tags[]">' . __("Tags", NRTD) . '</label>
					</th>
					<td>
						<input type="text" name="tags[]" value="' . htmlspecialchars($tags, ENT_QUOTES, "UTF-8") . '" /><br />
						<small>' . __("A comma-separated list of keywords that describe the book.", NRTD) . '</small>
					</td>
				</tr>

				';

			// Link to Post.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="posts[]">' . __("Link to post", NRTD) . '</label>
					</th>
					<td>
						<input type="text" name="posts[]" value="' . intval($existing->post) . '" /><br />
						<small>' . __("If you wish, you can link this book to a blog entry by entering that entry's ID here. The entry will be linked to from the book's library page.", NRTD) . '</small>
					</td>
				</tr>';

				// Post Option.
				echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="post_op">' . __("Post Option", NRTD) . '</label>
					</th>
					<td>
						<select name="post_op[]" id="post_op">
							';
				$post_op_idx = 0;
				foreach ( (array) $nr_post_options as $post_op => $name ) {
					$selected = '';
					if ($existing->post_op == $post_op_idx)
					{
						$selected = ' selected="selected"';
					}

					echo '
									<option value="' . $post_op_idx . '"' . $selected . '>' . $name . '</option>
								';
					$post_op_idx++;
				}
				echo '
						</select>
					</td>
				</tr>';

				// Meta Data.
				echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						Meta Data
					</th>
					<td>
						<p><a href="#" onclick="addMeta(\'0\'); return false;">' . __("Add another field", NRTD) . ' +</a></p>
								<table>
									<thead>
										<tr>
											<th scope="col">' . __("Key", NRTD) . ':</th>
											<th scope="col">' . __("Value", NRTD) . ':</th>
											<th scope="col"></th>
										</tr>
									</thead>
									<tbody id="book-meta-table-0" class="book-meta-table">
										';
					foreach ( (array) $meta as $key => $val ) {
						$url = get_option('siteurl') . "/wp-content/plugins/now-reading-redux/admin/edit.php?action=deletemeta&id={$existing->id}&key=" . urlencode($key);
						if ( function_exists('wp_nonce_url') )
							$url = wp_nonce_url($url, 'now-reading-delete-meta_' . $existing->id . $key);

						echo '
												<tr>
													<td><textarea name="keys-0[]" class="key">' . htmlspecialchars($key, ENT_QUOTES, "UTF-8") . '</textarea></td>
													<td><textarea name="values-0[]" class="value">' . htmlspecialchars($val, ENT_QUOTES, "UTF-8") . '</textarea></td>
													<td><a href="' . $url . '">' . __("Delete", NRTD) . '</a></td>
												</tr>
											';
					}
					echo '
										<tr>
											<td><textarea name="keys-0[]" class="key"></textarea></td>
											<td><textarea name="values-0[]" class="value"></textarea></td>
										</tr>
									</tbody>
								</table>

					</td>
				</tr>
				';

			// Rating.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="rating[]"><label for="rating">' . __("Rating", NRTD) . '</label></label>
					</th>
					<td>
						<select name="rating[]" id="rating-' . $i . '" style="width:100px;">
							<option value="unrated">&nbsp;</option>
							';
            for ($i = 10; $i >=1; $i--) {
                $selected = ($i == $existing->rating) ? ' selected="selected"' : '';
                echo "
										<option value='$i'$selected>$i</option>";
            }
            echo '
						</select>
					</td>
				</tr>
				';

			// Review.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="review-0">' . __("Review", NRTD) . '</label>
					</th>
					<td>
						<textarea name="review[]" id="review-' . $i . '" cols="50" rows="10" style="width:97%;height:200px;">' . htmlentities($existing->review, ENT_QUOTES, "UTF-8") . '</textarea>
						<small>
								<a accesskey="i" href="#" onclick="reviewBigger(\'' . $i . '\'); return false;">' . __("Increase size", NRTD) . ' (Alt + I)</a>
								&middot;
								<a accesskey="d" href="#" onclick="reviewSmaller(\'' . $i . '\'); return false;">' . __("Decrease size", NRTD) . ' (Alt + D)</a>
							</small>
					</td>
				</tr>

				</tbody>
				</table>

				<p class="submit">
					<input class="button" type="submit" value="' . __("Save", NRTD) . '" />
				</p>

				</form>

			</div>


			';
		}
		break;

		// Book Manager.
		default:
		{
			//depends on multiusermode (B. Spyckerelle)
			if ($options['multiuserMode']) {
				$count = total_books(0, 0, $userdata->ID); //counting only current users books
			} else {
				$count = total_books(0, 0); //counting all books
			}


			if ( $count ) {
				if ( !empty($_GET['q']) )
					$search = '&search=' . urlencode($_GET['q']);
				else
					$search = '';

				if ( empty($_GET['p']) )
					$page = 1;
				else
					$page = intval($_GET['p']);

				if ( empty($_GET['o']) )
					$order = 'desc';
				else
					$order = urlencode($_GET['o']);

				if ( empty($_GET['s']) )
					$orderby = 'started';
				else
					$orderby = urlencode($_GET['s']);

				// Filter by Author.
				if (empty($_GET['author']))
					$author = '';
				else
					$author = "&author=" . urlencode($_GET['author']);

				// Filter by Status.
				if (empty($_GET['status']))
					$status = '';
				else
					$status = "&status=" . urlencode($_GET['status']);

				$perpage = $options['booksPerPage'];
				$offset = ($page * $perpage) - $perpage;
				$num = $perpage;
				$pageq = "&num=$num&offset=$offset";

				// Depends on multiuser mode.
				if ($options['multiuserMode']) {
					$reader = "&reader=".$userdata->ID;
				} else {
					$reader = '';
				}

				$books = get_books("num=-1&status=all&orderby={$orderby}&order={$order}{$search}{$pageq}{$reader}{$author}{$status}");
				$count = count($books);

				$numpages = ceil(total_books(0, 0, $userdata->ID) / $perpage);

				$pages = '<span class="displaying-num">' . __("Pages", NRTD) . '</span>';

				if ( $page > 1 ) {
					$previous = $page - 1;
					$pages .= " <a class='page-numbers prev' href='{$nr_url->urls['manage']}&p=$previous&s=$orderby&o=$order'>&laquo;</a>";
				}

				for ( $i = 1; $i <= $numpages; $i++) {
					if ( $page == $i )
						$pages .= "<span class='page-numbers current'>$i</span>";
					else
						$pages .= " <a class='page-numbers' href='{$nr_url->urls['manage']}&p=$i&s=$orderby&o=$order'>$i</a>";
				}

				if ( $numpages > $page ) {
					$next = $page + 1;
					$pages .= " <a class='page-numbers next' href='{$nr_url->urls['manage']}&p=$next&s=$orderby&o=$order'>&raquo;</a>";
				}

				echo '
				<div class="wrap">

					<h2>Now Reading Redux</h2>

						<form method="get" action="" onsubmit="location.href += \'&q=\' + document.getElementById(\'q\').value; return false;">
							<p class="search-box"><label class="hidden" for="q">' . __("Search Books", NRTD) . ':</label> <input type="text" name="q" id="q" value="' . htmlentities($_GET['q']) . '" /> <input class="button" type="submit" value="' . __('Search Books', NRTD) . '" /></p>
						</form>

							<ul>
				';
				if (!empty($_GET['q']) || !empty($_GET['author']) || !empty($_GET['status']))
				{
					echo '
								<li><a href="' . $nr_url->urls['manage'] . '">' . __('Show all books', NRTD) . '</a></li>
					';
				}

				echo '
								<li><a href="' . library_url(0) . '">' . __('View library', NRTD) . '</a></li>
							</ul>

						<div class="tablenav">
							<div class="tablenav-pages">
								' . $pages . '
							</div>
						</div>


					<br style="clear:both;" />

					<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/now-reading-redux/admin/edit.php">
				';

				if ( function_exists('wp_nonce_field') )
					wp_nonce_field('now-reading-edit');
				if ( function_exists('wp_referer_field') )
					wp_referer_field();

				echo '
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="count" value="' . $count . '" />
				';

				$i = 0;

				if ( $order == 'desc' )
					$new_order = 'asc';
				else
					$new_order = 'desc';

				$title_sort_link = "{$nr_url->urls['manage']}&p=$page&s=book&o=$new_order$author";
				$author_sort_link = "{$nr_url->urls['manage']}&p=$page&s=author&o=$new_order$author";
				$added_sort_link = "{$nr_url->urls['manage']}&p=$page&s=added&o=$new_order$author";
				$started_sort_link = "{$nr_url->urls['manage']}&p=$page&s=started&o=$new_order$author";
				$finished_sort_link = "{$nr_url->urls['manage']}&p=$page&s=finished&o=$new_order$author";
				$status_sort_link = "{$nr_url->urls['manage']}&p=$page&s=status&o=$new_order$author";

				echo '
					<table class="widefat post fixed" cellspacing="0">
						<thead>
							<tr>
								<th></th>
								<th class="manage-column column-title"><a class="manage_books" href="'. $title_sort_link .'">Book</a></th>
								<th class="manage-column column-author"><a class="manage_books" href="'. $author_sort_link .'">Author</a></th>
								<th><a class="manage_books" href="'. $status_sort_link .'">Status</a></th>
								<th><a class="manage_books" href="'. $started_sort_link .'">Started</a></th>
								<th><a class="manage_books" href="'. $finished_sort_link .'">Finished</a></th>';

				if (!$options['hideAddedDate'])
				{
					echo '
								<th><a class="manage_books" href="'. $added_sort_link .'">Added</a></th>';
				}

				echo '
							</tr>
						</thead>
						<tbody>
				';

				foreach ((array)$books as $book)
				{

					$meta = get_book_meta($book->id);
					$tags = join(get_book_tags($book->id), ',');

					$alt = ( $i % 2 == 0 ) ? ' alternate' : '';

					$delete = get_option('siteurl') . '/wp-content/plugins/now-reading-redux/admin/edit.php?action=delete&id=' . $book->id;
					$delete = wp_nonce_url($delete, 'now-reading-delete-book_' .$book->id);


					echo '
						<tr class="manage-book' . $alt . '">

							<input type="hidden" name="id[]" value="' . $book->id . '" />
							<input type="hidden" name="title[]" value="' . $book->title . '" />
							<input type="hidden" name="author[]" value="' . $book->author . '" />

							<td>
								<img style="max-width:100px;" id="book-image-' . $i . '" class="small" alt="' . __('Book Cover', NRTD) . '" src="' . $book->image . '" />
							</td>

							<td class="post-title column-title">
								<strong>' . stripslashes($book->title) . '</strong>
								<div class="row-actions">
									<a href="' . book_permalink(0, $book->id) . '">' . __('View', NRTD) . '</a> |
										<a href="' . $nr_url->urls['manage'] . '&amp;action=editsingle&amp;id=' . $book->id . '">' . __('Edit', NRTD) . '</a> | <a href="' . $delete . '" onclick="return confirm(\'' . __("Are you sure you wish to delete this book permanently?", NRTD) . '\')">' . __("Delete", NRTD) . '</a>
								</div>
							</td>

							<td>
								<a href="' . $nr_url->urls['manage'] . '&amp;author=' . $book->author . '">' . $book->author . '</a>
							</td>

							<td>
								<a href="' . $nr_url->urls['manage'] . '&amp;status=' . $book->status . '">' . $book->status . '</a>
							</td>

							<td>
							' . ( ( nr_empty_date($book->started) ) ? '' : date($dateTimeFormat, strtotime($book->started)) ) . '
							</td>

							<td>
							' .( ( nr_empty_date($book->finished) ) ? '' : date($dateTimeFormat, strtotime($book->finished)) ) . '
							</td>';

						if (!$options['hideAddedDate'])
						{
							echo '
							<td>
							' . ( ( nr_empty_date($book->added) ) ? '' : date($dateTimeFormat, strtotime($book->added)) ) . '
							</td>';
						}

					echo '
						</tr>
					';

					$i++;

				}

				echo '
					</tbody>
					</table>

					</form>
				';

			} else {
				echo '
				<div class="wrap">
					<h2>' . __("Manage Books", NRTD) . '</h2>
					<p>' . sprintf(__("No books to display. To add some books, head over <a href='%s'>here</a>.", NRTD), $nr_url->urls['add']) . '</p>
				</div>
				';
			}

			echo '
			</div>
			';
		}
		break;
    }
}
?>
