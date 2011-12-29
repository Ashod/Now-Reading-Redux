<style type="text/css">
	<?php echo sidebar_css(); ?>
</style>

<?php
	global $book_query, $library_options, $shelf_name, $shelf_option;
	$options = get_option(NOW_READING_OPTIONS);
	$library_options = $options['sidebarOptions'];
?>

<div class="now-reading nr_widget">

	<div align=center>
		<br /><b><a href="<?php library_url() ?>">View Full Library</a></b>
	</div>

	<div class="booklisting">
	<?php
		$shelf_name = 'reading';
		$book_query = "status=reading&orderby=random";
		$shelf_option = $options['sidebarOptions']['readingShelf'];
		nr_load_template('shelf.php', false);
	?>
	</div>

	<div class="booklisting">
	<?php
		$shelf_name = 'unread';
		$book_query = "status=unread&orderby=random";
		$shelf_option = $options['sidebarOptions']['unreadShelf'];
		nr_load_template('shelf.php', false);
	?>
	</div>

	<div class="booklisting">
	<?php
		$shelf_name = 'onhold';
		$book_query = "status=onhold&orderby=random";
		$shelf_option = $options['sidebarOptions']['onholdShelf'];
		nr_load_template('shelf.php', false);
	?>
	</div>

	<div class="booklisting">
	<?php
		$shelf_name = 'read';
		$book_query = "status=read&orderby=finished&order=desc";
		$shelf_option = $options['sidebarOptions']['readShelf'];
		nr_load_template('shelf.php', false);
	?>
	</div>
    
	<?php if (have_wishlist_url()) : ?>
		<div align=center><b><a href="<?php wishlist_url() ?>">Buy me a gift!</a></b></div>
	<?php endif; ?>
	
	<div align=center><?php library_search_form(); ?></div>	
</div>
