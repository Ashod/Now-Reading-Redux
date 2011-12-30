<?php
	global $book_query, $library_options, $shelf_name, $shelf_option;
	$options = get_option(NOW_READING_OPTIONS);
	$library_options = $options['libraryOptions'];
?>

<?php wp_enqueue_script("jquery"); ?>

<?php get_header(); ?>

<div id="container">

<style type="text/css">
	<?php echo $library_options['css'] ?>
</style>

	<div class="now-reading nr_library primary narrowcolumn">

	<div class="post">

		<?php if (can_now_reading_admin()) : ?>
			<p>Admin: &raquo; <a href="<?php manage_library_url() ?>"><?php __('Manage Books', NRTD);?></a></p>
		<?php endif; ?>

		<p><?php print_book_stats() ?></p>

		<?php library_search_form() ?>
		
		<div class="booklisting">
		<?php
			$shelf_name = 'reading';
			$book_query = "status=reading&orderby=random";
			$shelf_option = $library_options['readingShelf'];
			nr_load_template('shelf.php', false);
		?>
		</div>
		
		<div class="booklisting">
		<?php
			$shelf_name = 'unread';
			$book_query = "status=unread&orderby=random";
			$shelf_option = $library_options['unreadShelf'];
			nr_load_template('shelf.php', false);
		?>
		</div>
		
		<div class="booklisting">
		<?php
			$shelf_name = 'onhold';
			$book_query = "status=onhold&orderby=random";
			$shelf_option = $library_options['onholdShelf'];
			nr_load_template('shelf.php', false);
		?>
		</div>
		
		<div class="booklisting">
		<?php
			$shelf_name = 'read';
			$book_query = "status=read&orderby=finished&order=desc";
			$shelf_option = $library_options['readShelf'];
			nr_load_template('shelf.php', false);
		?>
		</div>

		<?php do_action('nr_footer'); ?>

	</div>

	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
