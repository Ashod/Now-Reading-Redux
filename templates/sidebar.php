<?php
	global $book_query, $library_options, $shelf_title, $shelf_option;
	$options = get_option(NOW_READING_OPTIONS);
	$library_options = $options['sidebarOptions'];
?>

<style type="text/css">
	<?php echo $library_options['css'] ?>
</style>

<div class="now-reading nr_widget">

	<div align=center>
		<br /><b><a href="<?php library_url() ?>">View Full Library</a></b>
	</div>

	<?php
		// Reading.
		$shelf_option = $library_options['readingShelf'];
		$shelf_title = "<h4>" . $shelf_option['title'] . " (" . total_books('reading', 0) . ")</h4>";
		$book_query = "status=reading&orderby=random&num=" . $shelf_option['maxItems'];
		nr_load_template('shelf.php', false);

		// Unread.
		$shelf_option = $library_options['unreadShelf'];
		$shelf_title = "<h4>" . $shelf_option['title'] . " (" . total_books('unread', 0) . ")</h4>";
		$book_query = "status=unread&orderby=random&num=" . $shelf_option['maxItems'];
		nr_load_template('shelf.php', false);

		// On Hold.
		$shelf_option = $library_options['onholdShelf'];
		$shelf_title = "<h4>" . $shelf_option['title'] . " (" . total_books('onhold', 0) . ")</h4>";
		$book_query = "status=onhold&orderby=random&num=" . $shelf_option['maxItems'];
		nr_load_template('shelf.php', false);

		// Read.
		$shelf_option = $library_options['readShelf'];
		$shelf_title = "<h4>" . $shelf_option['title'] . " (" . total_books('read', 0) . ")</h4>";
		$book_query = "status=read&orderby=finished&order=desc&num=" . $shelf_option['maxItems'];
		nr_load_template('shelf.php', false);
	?>

	</div>
    
	<?php if (have_wishlist_url()) : ?>
		<div align=center><b><a href="<?php wishlist_url() ?>">Buy me a gift!</a></b></div>
	<?php endif; ?>
	
	<div align=center><?php library_search_form(); ?></div>	
</div>
