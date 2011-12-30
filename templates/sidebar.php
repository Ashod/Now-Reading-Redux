<?php
	global $book_query, $library_options, $shelf_name, $shelf_option;
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

	<div class="booklisting">
	<?php
		$shelf_name = 'reading';
		$shelf_option = $library_options['readingShelf'];
		$book_query = "status=reading&orderby=random&num=" . $shelf_option['maxItems'];
		nr_load_template('shelf.php', false);
	?>
	</div>

	<div class="booklisting">
	<?php
		$shelf_name = 'unread';
		$shelf_option = $library_options['unreadShelf'];
		$book_query = "status=unread&orderby=random&num=" . $shelf_option['maxItems'];
		nr_load_template('shelf.php', false);
	?>
	</div>

	<div class="booklisting">
	<?php
		$shelf_name = 'onhold';
		$shelf_option = $library_options['onholdShelf'];
		$book_query = "status=onhold&orderby=random&num=" . $shelf_option['maxItems'];
		nr_load_template('shelf.php', false);
	?>
	</div>

	<div class="booklisting">
	<?php
		$shelf_name = 'read';
		$shelf_option = $library_options['readShelf'];
		$book_query = "status=read&orderby=finished&order=desc&num=" . $shelf_option['maxItems'];
		nr_load_template('shelf.php', false);
	?>
	</div>
    
	<?php if (have_wishlist_url()) : ?>
		<div align=center><b><a href="<?php wishlist_url() ?>">Buy me a gift!</a></b></div>
	<?php endif; ?>
	
	<div align=center><?php library_search_form(); ?></div>	
</div>
