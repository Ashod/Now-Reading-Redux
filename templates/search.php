<?php
	global $book_query, $library_options, $shelf_title, $shelf_option;
	global $empty_shelf_message;
	$options = get_option(NOW_READING_OPTIONS);
	$library_options = $options['libraryOptions'];
?>

<?php get_header(); ?>

<div id="container">

	<style type="text/css">
		<?php echo $library_options['css'] ?>
	</style>

	<div id="content" class="now-reading nr_library primary narrowcolumn">

	<div class="post">

		<?php if (can_now_reading_admin()) : ?>
			<p>Admin: &raquo; <a href="<?php manage_library_url() ?>"><?php __('Manage Books', NRTD);?></a></p>
		<?php endif; ?>

		<p><a href="<?php library_url() ?>">&larr; Back to library</a></p>

		<?php library_search_form() ?>

		<p>Search results for <i><?php search_query(); ?></i>:</p>
		
		<?php
			$shelf_title = "<h3>" . __("Search Results") . "</h3>";
			$shelf_option = $library_options['readingShelf'];
			$book_query = "status=all&num=-1&search={$GLOBALS['query']}";
			$empty_shelf_message = __("Sorry, but there were no search results for your query.");
			nr_load_template('shelf.php', false);
		
			do_action('nr_footer');
		?>

	</div>
	</div>
</div>

<?php get_sidebar() ?>

<?php get_footer() ?>
