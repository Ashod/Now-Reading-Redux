<?php
/**
 * Functions for theming and templating.
 * @package now-reading
 */

/**
 * The array index of the current book in the {@link $books} array.
 * @global integer $GLOBALS['current_book']
 * @name $current_book
 */
$current_book = 0;
/**
 * The array of books for the current query.
 * @global array $GLOBALS['books']
 * @name $books
 */
$books = null;
/**
 * The current book in the loop.
 * @global object $GLOBALS['book']
 * @name $book
 */
$book = null;

/**
 * Formats a date according to the date format option.
 * @param string The date to format, in any string recogniseable by strtotime.
 */
function nr_format_date( $date ) {
    $options = get_option('nowReadingOptions');
    if ( !is_numeric($date) )
        $date = strtotime($date);
    if ( empty($date) )
        return '';
    return apply_filters('nr_format_date', date($options['formatDate'], $date));
}

/**
 * Returns true if the date is a valid one; false if it isn't.
 * @param string The date to check.
 */
function nr_empty_date( $date ) {
    return ( empty($date) || $date == "0000-00-00 00:00:00" );
}

/**
 * Prints the book's title.
 * @param bool $echo Whether or not to echo the results.
 */
function book_title( $echo = true ) {
    global $book;
    $title = stripslashes(apply_filters('book_title', $book->title));
    if ( $echo )
        echo $title;
    return $title;
}

/**
 * Prints the book's reader.
 * @param bool $echo Wether or not to echo the results.
 */
function book_reader( $echo=true ) {
    global $book;

    $user_info = get_userdata($book->reader);

    if ( $echo )
        echo $user_info->display_name;
    return $user_info->display_name;

}

/**
 * Prints the user name
 * @param int $reader_id Wordpress ID of the reader. If 0, prints the current user name.
 */
function print_reader( $echo=true, $reader_id = 0) {
    global $userdata;

    $username='';

    if (!$reader_id) {
        get_currentuserinfo();
        $username = $userdata->user_login;
    } else {
        $user_info = get_userdata($reader_id);
        $username = $user_info->user_login;
    }

    if ($echo)
        echo $username;
    return $username;
}

/**
 * Prints the author of the book.
 * @param bool $echo Whether or not to echo the results.
 */
function book_author( $echo = true ) {
    global $book;
    $author = apply_filters('book_author', $book->author);
    if ( $echo )
        echo $author;
    return $author;
}

/**
 * Prints a URL to the book's image, usually used within an HTML img element.
 * @param bool $echo Whether or not to echo the results.
 */
function book_image( $echo = true ) {
    global $book;
    $image = apply_filters('book_image', $book->image);
    if ( $echo )
        echo $image;
    return $image;
}

/**
 * Prints the date when the book was added to the database.
 * @param bool $echo Whether or not to echo the results.
 */
function book_added( $echo = true ) {
    global $book;
    $added = apply_filters('book_added', $book->added);
    if ( $echo )
        echo $added;
    return $added;
}

/**
 * Prints the date when the book's status was changed from unread to reading.
 * @param bool $echo Whether or not to echo the results.
 */
function book_started( $echo = true ) {
    global $book;
    if ( nr_empty_date($book->started) )
        $started = __('Not yet started.', NRTD);
    else
        $started = apply_filters('book_started', $book->started);
    if ( $echo )
        echo $started;
    return $started;

}

/**
 * Prints the date when the book's status was changed from reading to read.
 * @param bool $echo Whether or not to echo the results.
 */
function book_finished( $echo = true ) {
    global $book;
    if ( nr_empty_date($book->finished) )
        $finished = __('Not yet finished.', NRTD);
    else
        $finished = apply_filters('book_finished', $book->finished);
    if ( $echo )
        echo $finished;
    return $finished;
}

/**
 * Prints the current book's status with optional overrides for messages.
 * @param bool $echo Whether or not to echo the results.
 */
function book_status( $echo = true, $unread = '', $reading = '', $read = '', $onhold = '' ) {
    global $book, $nr_statuses;

    if ( empty($unread) )
        $unread = $nr_statuses['unread'];
    if ( empty($reading) )
        $reading = $nr_statuses['reading'];
    if ( empty($read) )
        $read = $nr_statuses['read'];
    if ( empty($onhold) )
        $onhold = $nr_statuses['onhold'];

    switch ( $book->status ) {
        case 'unread':
            $text = $unread;
            break;
        case 'onhold':
            $text = $onhold;
            break;
        case 'reading':
            $text = $reading;
            break;
        case 'read':
            $text = $read;
            break;
        default:
            return;
    }

    if ( $echo )
        echo $text;
    return $text;
}

/**
 * Prints the number of books started and finished within a given time period.
 * @param string $interval The time interval, eg  "1 year", "3 month"
 * @param bool $echo Whether or not to echo the results.
 */
function books_read_since( $interval, $echo = true ) {
    global $wpdb;

    $interval = $wpdb->escape($interval);
    $num = $wpdb->get_var("
	SELECT
		COUNT(*) AS count
	FROM
        {$wpdb->prefix}now_reading
	WHERE
		DATE_SUB(CURDATE(), INTERVAL $interval) <= b_finished
        ");

    if ( $echo )
        echo "$num book".($num != 1 ? 's' : '');
    return $num;
}

/**
 * Prints book reading statistics.
 * @param string $time_period The period to measure average over, eg "year", "month".
 */
function print_book_average($time_period = 'year')
{
	echo "There are ";
	total_books(0);
	echo " listed, of which ";
	books_read_since('1 year');
	echo " have been read in the last year, ";
	books_read_since('1 month');
	echo " read in the last month. That's ";
	average_books($time_period, true, false);
	echo ".";
}

/**
 * Prints book reading statistics per month.
 * @param int $duration_months The duration of the stats in number of months.
 * @param int $bar_width The width of the graph bars. Overriden by graph_width, if specified.
 * @param int $graph_height The height of the graph in pixels.
 * @param int $graph_width The width of the graph in pixels. <0 to calculate based on duration_months * default bar width.
 * @param bool $print_average Whether or not to print average books read per year.
 */
function print_book_stats($duration_months = 12, $bar_width = 40, $graph_height = 150, $graph_width = -1, $div_id = 'book_stats_graph', $print_average = true)
{
    global $wpdb;

	if ($graph_width < 0)
	{
		$graph_width = $bar_width * $duration_months;
	}

	// Print the statistics per month.
	$query = "
		SELECT YEAR(b_finished) AS Year, MONTH(b_finished) AS Month, COUNT(*) AS Count
		FROM wp_now_reading
		WHERE b_started > 0
		AND b_finished > DATE_SUB(CURDATE(), INTERVAL {$duration_months} MONTH)
		AND b_status != 'onhold'
		GROUP BY  YEAR(b_finished), MONTH(b_finished)
		ORDER BY YEAR(b_finished), MONTH(b_finished)
        ";
	$statistics = $wpdb->get_results($query);
    $statistics = apply_filters('get_books', $statistics);

	echo "
	<div class='graph-caption' style='margin: auto; text-align: center'><i>";
	printf(__("Number of Books read during the past %d months.", NRTD), $duration_months);
	echo "</i></div>
	";

    echo '
		<link rel="stylesheet" href="' . plugins_url('/css/tufte-graph.css', __FILE__) . '" type="text/css" />
		<script type="text/javascript" src="' . plugins_url('/js/jquery.enumerable.js', __FILE__) .'"></script>
		<script type="text/javascript" src="' . plugins_url('/js/jquery.tufte-graph.js', __FILE__) .'"></script>
		<script type="text/javascript" src="' . plugins_url('/js/raphael-min.js', __FILE__) .'"></script>
		<script type="text/javascript">
		jQuery(document).ready(function () {';
    echo "
		jQuery('#{$div_id}').tufteBar({
		data: [";

	// For each year/month, create an entry of the total finished count.
	// Entry format: [0, {label: '2005'}, {barLabel: '0 Books'}],
	foreach ((array)$statistics as $stat)
	{
		$label = date("M", mktime(0, 0, 0, $stat->Month, 1, 2000)) . ' ' . $stat->Year;
		echo "
			[{$stat->Count}, {label: '" . $label . "'}, {barLabel: '{$stat->Count}'}],";
	}

	echo "
		],
		barLabel:  function(index) {
			return this[2].barLabel;
		},
		axisLabel: function(index) { return this[1].label },
		});
		});
	</script>
	<div id='{$div_id}' class='graph' style='width: {$graph_width}px; height: {$graph_height}px;'></div>
	";

	if ($print_average)
	{
		echo "
		<div class='graph-caption' style='margin: auto; text-align: center; padding: 10px;'>";
		print_book_average();
		echo "</div>
		";
	}
}

/**
 * Prints the total number of books in the library.
 * @param string $status A comma-separated list of statuses to include in the count. If ommitted, all statuses will be counted.
 * @param bool $echo Whether or not to echo the results.
 * @param int $userID Counting only userID's books.
 */
function total_books($status = '', $echo = true , $userID = 0) {
    global $wpdb;

	$reader = get_reader_visibility_filter($userID, false);

    if ($status)
	{
        if (strpos($status, ',') === false)
		{
            $status = 'WHERE b_status = "' . $wpdb->escape($status) . '"';
        }
		else
		{
            $statuses = explode(',', $status);

            $status = 'WHERE 1=0';
            foreach ( (array) $statuses as $st )
			{
                $status .= ' OR b_status = "' . $wpdb->escape(trim($st)) . '" ';
            }
        }

		if (!empty($reader))
		{
			$status .= ' AND ' . $reader;
		}
	}
	else
	{
		if (!empty($reader))
		{
			$status = ' WHERE ' . $reader;
		}
    }

    $num = $wpdb->get_var("
	SELECT
		COUNT(*) AS count
	FROM
        {$wpdb->prefix}now_reading
        $status
        ");

    if ($echo)
    {
		echo "$num book".($num != 1 ? 's' : '');
	}

    return $num;
}

/**
 * Prints the average number of books read in the given time limit.
 * Unless $absolute is true, the average is computed based on the weighted average of
 * books read witin the last 365 days and those read within the last 30 days.
 * @param string $time_period The period to measure average over, eg "year", "month".
 * @param bool $echo Whether or not to echo the results.
 * @param bool $absolute If true, the average is computed based on the oldest finished date.
 */
function average_books($time_period = 'week', $echo = true, $absolute = true)
{
    global $wpdb;

	if ($absolute)
	{
		$books_per_day = $wpdb->get_var("
		SELECT
			( COUNT(*) / ( TO_DAYS(CURDATE()) - TO_DAYS(MIN(b_finished)) ) ) AS books_per_day_in_year
		FROM
			{$wpdb->prefix}now_reading
		WHERE
			b_status = 'read'
		AND b_finished > 0
			");
	}
	else
	{
		$books_per_day_in_year = $wpdb->get_var("
		SELECT
			( COUNT(*) / ( TO_DAYS(CURDATE()) - TO_DAYS(MIN(b_finished)) ) ) AS books_per_day_in_year
		FROM
			{$wpdb->prefix}now_reading
		WHERE
			b_status = 'read'
		AND TO_DAYS(b_finished) >= (TO_DAYS(CURDATE()) - 365)
			");

		$books_per_day_in_month = $wpdb->get_var("
		SELECT
			( COUNT(*) / ( TO_DAYS(CURDATE()) - TO_DAYS(MIN(b_finished)) ) ) AS books_per_day_in_month
		FROM
			{$wpdb->prefix}now_reading
		WHERE
			b_status = 'read'
		AND TO_DAYS(b_finished) >= (TO_DAYS(CURDATE()) - 30)
			");

		// Give twice the weight for the last month's average than the total of last year's.
		$books_per_day = ((2.0 * $books_per_day_in_month) + $books_per_day_in_year) / 3.0;
	}

    $average = 0;
    switch ( $time_period ) {
        case 'year':
            $average = round($books_per_day * 365);
            break;

        case 'month':
            $average = round($books_per_day * 31);
            break;

        case 'week':
            $average = round($books_per_day * 7);
			break;

        case 'day':
            $average = round($books_per_day * 1);
            break;

        default:
            return 0;
    }

    if($echo)
    {
		if ($absolute)
		{
			$type = __("an absolute");
		}
		else
		{
			$type = __("a current");
		}

		printf(__("%s average of %s book%s per %s", NRTD), $type, $average, ($average != 1 ? 's' : ''), $time_period);
	}

    return $average;
}

/**
 * Prints the URL to an internal page displaying data about the book.
 * @param bool $echo Whether or not to echo the results.
 * @param int $id The ID of the book to link to. If ommitted, the current book's ID will be used.
 */
function book_permalink( $echo = true, $id = 0 ) {
    global $book, $wpdb;
    $options = get_option('nowReadingOptions');

    if ( !empty($book) && empty($id) )
        $the_book = $book;
    elseif ( !empty($id) )
        $the_book = get_book(intval($id));

    if ( $the_book->id < 1 )
        return;

    $author = $the_book->nice_author;
    $title = $the_book->nice_title;



    if ( $options['useModRewrite'] )
        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase'])  . "/$author/$title/";
    else
        $url = get_option('home') . "/index.php?now_reading_author=$author&amp;now_reading_title=$title";

    $url = apply_filters('book_permalink', $url);
    if ( $echo )
        echo $url;
    return $url;
}

/**
 * Prints the URL to an internal page displaying books by a certain author.
 * @param bool $echo Whether or not to echo the results.
 * @param string $author The author to link to. If ommitted, the global book's author will be used.
 */
function book_author_permalink( $echo = true, $author = null ) {
    global $book, $wpdb;

    $options = get_option('nowReadingOptions');

    if ( !$author )
        $author = $book->author;

    if ( !$author )
        return;

    $nice_author = sanitize_title($author);

    if ( $options['useModRewrite'] )
        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase']) . "/$nice_author/";
    else
        $url = get_option('home') . "/index.php?now_reading_author=$nice_author";

    $url = apply_filters('book_author_permalink', $url);
    if ( $echo )
        echo $url;
    return $url;
}

/**
 * Prints the URL to an internal page displaying books by a certain reader.
 * @param bool $echo Wether or not to echo the results.
 * @param int $reader The reader id. If omitted, links to all books.
 */
function book_reader_permalink( $echo = true, $reader = 0) { //added by B. Spyckerelle for multiuser mode
    global $book, $wpdb;

    $options = get_option('nowReadingOptions');

    if ( !$reader )
        $reader = $book->reader;

    if ( !$reader )
        return;

    if ($options['multiuserMode']) {
        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase']) . "/reader/$reader/";
    } else {
        $url = get_option('home') . "/index.php?now_reading_library=1&now_reading_reader=$reader";
    }

    if ($echo)
        echo $url;
    return $url;
}

/**
 * Prints a URL to the book's Amazon detail page. If the book is a custom one, it will print a URL to the book's permalink page.
 * @param bool $echo Whether or not to echo the results.
 * @param string $domain The Amazon domain to link to. If ommitted, the default domain will be used.
 * @see book_permalink()
 * @see is_custom_book()
 */
function book_url( $echo = true, $domain = null ) {
    global $book;
    $options = get_option('nowReadingOptions');

    if ( empty($domain) )
        $domain = $options['domain'];

    if ( is_custom_book() )
        return book_permalink($echo);
    else {
        $url = apply_filters('book_url', "http://www.amazon{$domain}/exec/obidos/ASIN/{$book->asin}/ref=nosim/{$options['associate']}");
        if ( $echo )
            echo $url;
        return $url;
    }
}

/**
 * Returns true if the current book is linked to a post, false if it isn't.
 */
function book_has_post() {
    global $book;

    return ( $book->post > 0 );
}

/**
 * Returns or prints the permalink of the post linked to the current book.
 * @param bool $echo Whether or not to echo the results.
 */
function book_post_url( $echo = true ) {
    global $book;

    if ( !book_has_post() )
        return;

    $permalink = get_permalink($book->post);

    if ( $echo )
        echo $permalink;
    return $permalink;
}

/**
 * Returns or prints the title of the post linked to the current book.
 * @param bool $echo Whether or not to echo the results.
 */
function book_post_title( $echo = true ) {
    global $book;

    if ( !book_has_post() )
        return;

    $post = get_post($book->post);

    if ( $echo )
        echo $post->post_title;
    return $post->post_title;
}

/**
 * If the current book is linked to a post, prints an HTML link to said post.
 * @param bool $echo Whether or not to echo the results.
 */
function book_post_link( $echo = true ) {
    global $book;

    if ( !book_has_post() )
        return;

    $link = '<a href="' . book_post_url(0) . '">' . book_post_title(0) . '</a>';

    if ( $echo )
        echo $link;
    return $link;
}

/**
 * If the user has the correct permissions, prints a URL to the Manage -> Now Reading page of the WP admin.
 * @param bool $echo Whether or not to echo the results.
 */
function manage_library_url( $echo = true ) {
    global $nr_url;
    if ( can_now_reading_admin() )
        echo apply_filters('book_manage_url', $nr_url->urls['manage']);
}

/**
 * If the user has the correct permissions, prints a URL to the review-writing screen for the current book.
 * @param bool $echo Whether or not to echo the results.
 */
function book_edit_url( $echo = true ) {
    global $book, $nr_url;
    if ( can_now_reading_admin() )
        echo apply_filters('book_edit_url', $nr_url->urls['manage'] . '&amp;action=editsingle&amp;id=' . $book->id);
}

/**
 * Returns true if the book is a custom one or false if it is one from Amazon.
 */
function is_custom_book() {
    global $book;
    return empty($book->asin);
}

/**
 * Returns true if the user has the correct permissions to view the Now Reading admin panel.
 */
function can_now_reading_admin() {

//depends on multiuser mode (B. Spyckerelle)
    $options = get_option('nowReadingOptions');
    $nr_level = $options['multiuserMode'] ? 'level_2' : 'level_9';

    return current_user_can($nr_level);
}

/**
 * Returns true if the current book is owned by the current user
 * Meaningful only when a user is logged in.
 * Works for both multi-user and single-user modes.
 */
function is_my_book()
{
    global $book, $userdata;

	if (is_user_logged_in())
	{
        get_currentuserinfo();
        return $book->reader == $userdata->ID;
	}
	else
	{
		return false;
	}
}

/**
 * Prints a URL pointing to the main library page that respects the useModRewrite option.
 * @param bool $echo Whether or not to echo the results.
 */
function library_url( $echo = true ) {
    $options = get_option('nowReadingOptions');

    if ( $options['useModRewrite'] )
        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase']);
    else
        $url = get_option('home') . '/index.php?now_reading_library=true';

    $url = apply_filters('book_library_url', $url);

    if ( $echo )
        echo $url;
    return $url;
}

/**
 * Prints the book's rating or "Unrated" if the book is unrated.
 * @param bool $echo Whether or not to echo the results.
 */
function book_rating( $echo = true ) {
    global $book;
    if ( $book->rating )
        $rate = apply_filters('book_rating', $book->rating);
    else
        $rate = apply_filters('book_rating', __('Unrated', NRTD));

    if ( $echo )
        echo $rate;
    return $rate;
}

/**
 * Prints the book's review or "This book has not yet been reviewed" if the book is unreviewed.
 * @param bool $echo Whether or not to echo the results.
 */
function book_review( $echo = true ) {
    global $book;
    if ( $book->review )
        echo apply_filters('book_review', $book->review);
    else
        echo apply_filters('book_review', '<p>' . __('This book has not yet been reviewed.', NRTD) . '</p>');
}

/**
 * Prints the URL of the search page, ready to be appended with a query or simply used as the action of a GET form.
 * @param bool $echo Whether or not to echo the results.
 */
function search_url( $echo = true ) {
    $options = get_option('nowReadingOptions');

    if ( $options['useModRewrite'] )
        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase']) . "/search";
    else
        $url = get_option('home');

    $url = apply_filters('library_search_url', $url);

    if ( $echo )
        echo $url;
    return $url;
}

/**
 * Prints the current search query, if it exists.
 * @param bool $echo Whether or not to echo the results.
 */
function search_query( $echo = true ) {
    global $query;
    if ( empty($query) )
        return;
    $query = htmlentities(stripslashes($query));
    if ( $echo )
        echo $query;
    return $query;
}

/**
 * Prints a standard search form for users who don't want to create their own.
 * @param bool $echo Whether or not to echo the results.
 */
function library_search_form( $echo = true ) {
    $options = get_option('nowReadingOptions');

    $html = '
	<form method="get" action="' . search_url(0) . '">
	';

	if (!$options['useModRewrite'])
	{
        $html .= '<input type="hidden" name="now_reading_search" value="1" />';
    }

    $html .= '
		<input type="text" name="q" />
		<div align=center>
		<input type="submit" value="' . __("Search Library", NRTD) . '" />
		</div>
	</form>
	';

	if ($echo)
    {
		echo $html;
	}

    return $html;
}

/**
 * Prints the book's meta data in a definition list.
 * @see get_book_meta()
 * @param bool $new_list Whether to start a new list (creating new <dl> tags).
 */
function print_book_meta( $new_list = true ) {
    global $book;

    $meta = get_book_meta($book->id);

    if ( count($meta) < 1 )
        return;

    if ( $new_list )
        echo '<dl>';

    foreach ( (array) $meta as $key => $value ) {
        $key = apply_filters('book_meta_key', $key);
        $value = apply_filters('book_meta_val', $value);

        echo '<dt>';
        if ( strtolower($key) == $key )
            echo ucwords($key);
        else
            echo $key;
        echo '</dt>';

        echo "<dd>$value</dd>";
    }

    if ( $new_list )
        echo '</dl>';
}

/**
 * Prints a single book meta value.
 * @param $key The meta key to fetch
 * @param $echo Whether to echo the result or just return it
 * @returns string The meta value for the given $key.
 */
function book_meta( $key, $echo = true ) {
    global $book;

    $meta = get_book_meta($book->id, $key);

    if ( empty($meta) )
        return;

    $meta = apply_filters('book_meta_val', $meta);

    if ( $echo )
        echo $meta;
    return $meta;
}

/**
 * Prints a comma-separated list of tags for the current book.
 * @param bool $echo Whether or not to echo the results.
 */
function print_book_tags( $echo = true ) {
    global $book;

    $tags = get_book_tags($book->id);

    if ( count($tags) < 1 )
        return;

    $i = 0;
    $string = '';
    foreach ( (array) $tags as $tag ) {
        if ( $i++ != 0 )
            $string .= ', ';
        $link = book_tag_url($tag, 0);
        $string .= "<a href='$link'>$tag</a>";
    }

    if ( $echo )
        echo $string;
    return $string;
}

/**
 * Returns a URL to the permalink for a given tag.
 * @param bool $echo Whether or not to echo the results.
 */
function book_tag_url( $tag, $echo = true ) {
    $options = get_option('nowReadingOptions');

    if ( $options['useModRewrite'] )
        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase']) . "/tag/" . urlencode($tag);
    else
        $url = get_option('home') . '/index.php?now_reading_tag=' . urlencode($tag);

    $url = apply_filters('library_tag_url', $url);

    if ( $echo )
        echo $url;
    return $url;
}

/**
 * Returns a URL to the permalink for a given (custom) page.
 * @param string $page Page name (e.g. custom.php) to create URL for.
 * @param bool $echo Whether or not to echo the results.
 */
function library_page_url( $page, $echo = true ) {
    $options = get_option('nowReadingOptions');

    if ( $options['useModRewrite'] )
        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase']) . "/page/" . urlencode($page);
    else
        $url = get_option('home') . '/index.php?now_reading_page=' . urlencode($page);

    $url = apply_filters('library_page_url', $url);

    if ( $echo )
        echo $url;
    return $url;
}

/**
 * Returns or prints the currently viewed tag.
 * @param bool $echo Whether or not to echo the results.
 */
function the_tag( $echo = true ) {
    $tag = htmlentities(stripslashes($GLOBALS['nr_tag']));
    if ( $echo )
        echo $tag;
    return $tag;
}

/**
 * Returns or prints the currently viewed author.
 * @param bool $echo Whether or not to echo the results.
 */
function the_book_author( $echo = true ) {
    $author = htmlentities(stripslashes($GLOBALS['nr_author']));
    $author = apply_filters('the_book_author', $author);
    if ( $echo )
        echo $author;
    return $author;
}

/**
 * Use in the main template loop; if un-fetched, fetches books for given $query and returns true whilst there are still books to loop through.
 * @param string $query The query string to pass to get_books()
 * @return boolean True if there are still books to loop through, false at end of loop.
 */
function have_books( $query ) {
    global $books, $current_book;
    if ( !$books ) {
        if ( is_numeric($query) )
            $GLOBALS['books'] = get_book($query);
        else
            $GLOBALS['books'] = get_books($query);
    }
    if (is_a($books, 'stdClass'))
        $books = array($books);
    $have_books = ( !empty($books[$current_book]) );
    if ( !$have_books ) {
        $GLOBALS['books']			= null;
        $GLOBALS['current_book']	= 0;
    }
    return $have_books;
}

/**
 * Advances counter used by have_books(), and sets the global variable $book used by the template functions. Be sure to call it each template loop to avoid infinite loops.
 */
function the_book() {
    global $books, $current_book;
    $GLOBALS['book'] = $books[$current_book];
    $GLOBALS['current_book']++;
}

?>