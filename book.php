<?php
/**
 * Book fetching/updating functions
 * @package now-reading
 */

/**
 * Fetches books from the database based on a given query.
 *
 * Example usage:
 * <code>
 * $books = get_books('status=reading&orderby=started&order=asc&num=-1&reader=user');
 * </code>
 * @param string $query Query string containing restrictions on what to fetch.
 * 		 	Valid variables: $num, $status, $orderby, $order, $search, $author, $title, $reader.
 * @param bool show_private If true, will show all readers' private books!
 * @return array Returns a numerically indexed array in which each element corresponds to a book.
 */
function get_books($query, $show_private = false) {

    global $wpdb;

    $options = get_option(NOW_READING_OPTIONS);

    parse_str($query);

    // We're fetching a collection of books, not just one.
    switch ( $status ) {
        case 'unread':
        case 'onhold':
        case 'reading':
        case 'read':
            break;
        default:
            $status = 'all';
            break;
    }
    if ( $status != 'all' )
        $status = "AND b_status = '$status'";
    else
        $status = '';

    if ( !empty($search) ) {
        $search = $wpdb->escape($search);
        $search = "AND ( b_author LIKE '%$search%' OR b_title LIKE '%$search%' OR m_value LIKE '%$search%')";
    } else
        $search = '';

    $order	= ( strtolower($order) == 'desc' ) ? 'DESC' : 'ASC';

    switch ( $orderby ) {
        case 'added':
            $orderby = 'b_added';
            break;
        case 'started':
            $orderby = 'b_started';
            break;
        case 'finished':
            $orderby = 'b_finished';
            break;
        case 'title':
            $orderby = 'b_title';
            break;
        case 'author':
            $orderby = 'b_author';
            break;
        case 'asin':
            $orderby = 'b_asin';
            break;
        case 'status':
            $orderby = "b_status $order, b_added";
            break;
        case 'rating':
            $orderby = 'b_rating';
            break;
        case 'random':
            $orderby = 'RAND()';
            break;
        default:
            $orderby = 'b_added';
            break;
    }

    if ( $num > -1 && $offset >= 0 ) {
        $offset	= intval($offset);
        $num 	= intval($num);
        $limit	= "LIMIT $offset, $num";
    } else
        $limit	= '';

    if ( !empty($author) ) {
        $author	= $wpdb->escape($author);
        $author	= "AND b_author = '$author'";
    }

    if ( !empty($title) ) {
        $title	= $wpdb->escape($title);
        $title	= "AND b_title = '$title'";
    }

    if ( !empty($tag) ) {
        $tag = $wpdb->escape($tag);
        $tag = "AND t_name = '$tag'";
    }

    $meta = '';
    if ( !empty($meta_key) ) {
        $meta_key = $wpdb->escape($meta_key);
        $meta = "AND meta_key = '$meta_key'";
        if ( !empty($meta_value )) {
            $meta_value = $wpdb->escape($meta_value);
            $meta .= " AND meta_value = '$meta_value'";
        }
    }

	$reader = get_reader_visibility_filter($reader, $show_private);

    $query = "
	SELECT
		COUNT(*) AS count,
		b_id AS id, b_title AS title, b_author AS author, b_image AS image, b_status AS status, b_nice_title AS nice_title, b_nice_author AS nice_author,
		b_added AS added, b_started AS started, b_finished AS finished,
		b_asin AS asin, b_rating AS rating, b_review AS review, b_post AS post, b_reader as reader, b_post_op as post_op
	FROM
        {$wpdb->prefix}now_reading
	LEFT JOIN {$wpdb->prefix}now_reading_meta
		ON m_book = b_id
	LEFT JOIN {$wpdb->prefix}now_reading_books2tags
		ON book_id = b_id
	LEFT JOIN {$wpdb->prefix}now_reading_tags
		ON tag_id = t_id
	WHERE
		1=1
        $status
        $id
        $search
        $author
        $title
        $tag
        $meta
	AND
        $reader
	GROUP BY
		b_id
	ORDER BY
        $orderby $order
        $limit
        ";
	$books = $wpdb->get_results($query);

    $books = apply_filters('get_books', $books);

    foreach ( (array) $books as $book ) {
        $book->added = ( nr_empty_date($book->added) )	? '' : $book->added;
        $book->started = ( nr_empty_date($book->started) )	? '' : $book->started;
        $book->finished = ( nr_empty_date($book->finished) )	? '' : $book->finished;
    }

    return $books;
}

/**
 * Fetches a single book with the given ID.
 * @param int $id The b_id of the book you want to fetch.
 */
function get_book($id) {
    global $wpdb;

    $options = get_option(NOW_READING_OPTIONS);

    $id = intval($id);

    $book = apply_filters('get_single_book', $wpdb->get_row("
	SELECT
		COUNT(*) AS count,
		b_id AS id, b_title AS title, b_author AS author, b_image AS image, b_status AS status, b_nice_title AS nice_title, b_nice_author AS nice_author,
		b_added AS added, b_started AS started, b_finished AS finished,
		b_asin AS asin, b_rating AS rating, b_review AS review, b_post AS post, b_reader as reader,
		b_post_op AS post_op, b_visibility AS visibility
	FROM {$wpdb->prefix}now_reading
	WHERE b_id = $id
	GROUP BY b_id
        "));

    $book->added = ( nr_empty_date($book->added) )	? '' : $book->added;
    $book->started = ( nr_empty_date($book->started) )	? '' : $book->started;
    $book->finished = ( nr_empty_date($book->finished) )	? '' : $book->finished;

    return $book;
}

/**
 * Returns a string to be used in a WHERE clause to
 * select books based on a reader (or all) and visibility.
 * @param int $userID Interested only in the given userID's books.
 * @param bool show_private If true, will show all readers' private books!
 */
function get_reader_visibility_filter($userID = 0, $show_private = false)
{
	global $user_ID;

	if (!$show_private)
	{
		// Show publics only.
		$visibility = "b_visibility = 1";
	}

    if (!empty($userID))
	{
		// we're only interested in this reader.
		$reader = "b_reader = '$userID'";

		if (is_user_logged_in())
		{
			get_currentuserinfo();
			if ($show_private || ($userID == $user_ID))
			{
				// Privates are shown, so don't filter them.
				return $reader;
			}
		}
    }
	else
	{
		// No specific reader, see if there is a logged-user, then get her private
		// books too, otherwise, get everyeone's publics.
		if (is_user_logged_in())
		{
			get_currentuserinfo();

			// Either it's the owner, or we get public books only.
			return "(b_reader = '$user_ID' OR b_visibility = 1)";
		}
	}

	if (!empty($reader) && !empty($visibility))
	{
		return $reader . ' AND ' . $visibility;
	}

	return !empty($reader) ? $reader : $visibility;
}

/**
 * Adds a book to the database.
 * @param string $query Query string containing the fields to add.
 * @return boolean True on success, false on failure.
 */
function add_book( $query ) {
    return update_book($query);
}

/**
 * Updates a given book's database entry
 * @param string $query Query string containing the fields to add.
 * @return boolean True on success, false on failure.
 */
function update_book( $query ) {
    global $wpdb, $query, $fields, $userdata;

    parse_str($query, $fields);

    $fields = apply_filters('add_book_fields', $fields);

    // If an ID is specified, we're doing an update; otherwise, we're doing an insert.
    $insert = empty($fields['b_id']);

    $valid_fields = array('b_id', 'b_added', 'b_started', 'b_finished', 'b_title', 'b_nice_title',
        'b_author', 'b_nice_author', 'b_image', 'b_asin', 'b_status', 'b_rating', 'b_review', 'b_post');

    if ( $insert ) {
        $colums = $values = '';
        foreach ( (array) $fields as $field => $value ) {
            if ( empty($field) || empty($value) || !in_array($field, $valid_fields) )
                continue;
            $value = $wpdb->escape($value);
            $columns .= ", $field";
            $values .= ", '$value'";
        }

        get_currentuserinfo();
        $reader_id = $userdata->ID;
        $columns .= ", b_reader";
        $values .= ", '$reader_id'";

        $columns = preg_replace('#^, #', '', $columns);
        $values = preg_replace('#^, #', '', $values);

        $wpdb->query("
		INSERT INTO {$wpdb->prefix}now_reading
		($columns)
		VALUES($values)
            ");

        $id = $wpdb->get_var("SELECT MAX(b_id) FROM {$wpdb->prefix}now_reading");


        if ( $id > 0 ) {
            do_action('book_added', $id);
            return $id;
        } else {
            return false;
        }
    } else {
        $id = intval($fields['b_id']);
        unset($fields['b_id']);

        $set = '';
        foreach ( (array) $fields as $field => $value ) {
            if ( empty($field) || empty($value) || !in_array($field, $valid_fields) )
                continue;
            $value = $wpdb->escape($value);
            $set .= ", $field = '$value'";
        }

        $set = preg_replace('#^, #', '', $set);

        $wpdb->query("
		UPDATE {$wpdb->prefix}now_reading
		SET $set
		WHERE b_id = $id
            ");

        do_action('book_updated', $id);

        return $id;
    }
}

/**
 * Adds a tag to the database.
 */
function add_library_tag( $tag ) {
    global $wpdb;

    $exists = library_tag_exists($tag);

    if ( $exists ) {
        return $exists;
    } else {
        $tag = $wpdb->escape(trim($tag));
        $wpdb->query("INSERT INTO {$wpdb->prefix}now_reading_tags (t_name) VALUES('$tag')");
        return $wpdb->insert_id;
    }
}

/**
 * Checks if a tag exists
 * @return int The tag's ID if it exists, 0 if it doesn't
 */
function library_tag_exists( $tag ) {
    global $wpdb;

    $tag = $wpdb->escape(trim($tag));
    $id = $wpdb->get_var("SELECT t_id FROM {$wpdb->prefix}now_reading_tags WHERE t_name = '$tag'");

    return intval($id);
}

/**
 * Gets the tags for the given book.
 */
function get_book_tags( $id ) {
    global $wpdb;

    if ( !$id )
        return array();

    $tags = $wpdb->get_results("
	SELECT t_name AS name FROM {$wpdb->prefix}now_reading_tags, {$wpdb->prefix}now_reading_books2tags
	WHERE book_id = $id AND tag_id = t_id
	ORDER BY t_name ASC
        ");

    $array = array();
    if ( count($tags) > 0 ) {
        foreach ( (array) $tags as $tag ) {
            $array[] = $tag->name;
        }
    }

    return $array;
}

/**
 * Tags the book with the given tag.
 */
function tag_book( $id, $tag ) {
    return set_book_tags($id, $tag, true);
}

/**
 * Sets the tags for the given book.
 * @param bool $append If true, add the given tags onto the existing ones; if false, replace current tags with new ones.
 */
function set_book_tags( $id, $tags, $append = false ) {
    global $wpdb;

    $id = intval($id);
    if ( !$id )
        return;

    if ( !is_array($tags) ) {
        $tags = (array) explode(',', $tags);
    }

    $old_tags = get_book_tags($id);

    // Tags to add
    $add = array_diff($tags, $old_tags);
    if ( $add ) {
        foreach ( (array) $add as $tag ) {
            $tid = library_tag_exists($tag);
            if ( !$tid ) // create the tag if it doesn't exist
                $tid = add_library_tag($tag);
            $wpdb->query("INSERT INTO {$wpdb->prefix}now_reading_books2tags (book_id, tag_id) VALUES($id, $tid)");
        }
    }

    // Tags to delete
    $delete = array_diff($old_tags, $tags);
    if ( $delete && !$append ) {
        foreach ( (array) $delete as $tag ) {
            $tid = library_tag_exists($tag);
            $wpdb->query("DELETE FROM {$wpdb->prefix}now_reading_books2tags WHERE book_id = $id AND tag_id = $tid");
        }
    }
}

/**
 * DEPRECATED: Fetches all the books tagged with the given tag.
 */
function get_books_by_tag( $tag, $query ) {
    return get_books("tag=$tag");
}

/**
 * Fetches meta-data for the given book.
 * @see print_book_meta()
 */
function get_book_meta( $id, $key = '' ) {
    global $wpdb;

    if ( !$id )
        return null;

    $id = intval($id);

    if ( !empty($key) )
        $key = 'AND m_key = "' . $wpdb->escape($key) . '"';
    else
        $key = '';

    $raws = $wpdb->get_results("SELECT m_key, m_value FROM {$wpdb->prefix}now_reading_meta WHERE m_book = '$id' $key");

    if ( !count($raws) )
        return null;

    $meta = null;
    if ( empty($key) ) {
        $meta = array();
        foreach ( (array) $raws as $raw ) {
            $meta[$raw->m_key] = $raw->m_value;
        }
        $meta = apply_filters('book_meta', $meta);
    } else {
        $meta = $raws[0]->m_value;
        $meta = apply_filters('book_meta_single', $meta);
    }

    return $meta;
}

/**
 * Adds a meta key-value pairing for the given book.
 */
function add_book_meta( $id, $key, $value ) {
    return update_book_meta($id, $key, $value);
}

/**
 * Updates the meta key-value pairing for the given book. If the key does not exist, it will be created.
 */
function update_book_meta( $id, $key, $value ) {
    global $wpdb;

    $key = $wpdb->escape($key);
    $value = $wpdb->escape($value);

    $existing = $wpdb->get_var("
	SELECT
		m_id AS id
	FROM
        {$wpdb->prefix}now_reading_meta
	WHERE
		m_book = '$id'
		AND
		m_key = '$key'
        ");

    if ( $existing != null ) {
        $result = $wpdb->query("
		UPDATE {$wpdb->prefix}now_reading_meta
		SET
			m_key = '$key',
			m_value = '$value'
		WHERE
			m_id = '$existing'
            ");
    } else {
        $result = $wpdb->query("
		INSERT INTO {$wpdb->prefix}now_reading_meta
			(m_book, m_key, m_value)
			VALUES('$id', '$key', '$value')
            ");
    }
    return $result;
}

/**
 * Deletes the meta key-value pairing for the given book with the given key.
 */
function delete_book_meta( $id, $key ) {
    global $wpdb;

    $id = intval($id);
    $key = $wpdb->escape($key);

    return $wpdb->query("
	DELETE FROM
    {$wpdb->prefix}now_reading_meta
	WHERE
		m_book = '$id'
		AND
		m_key = '$key'
    ");
}

?>