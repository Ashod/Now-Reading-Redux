<?php
	global $book_query, $library_options, $shelf_title, $shelf_option;
	$options = get_option(NOW_READING_OPTIONS);
	$library_options = $options['libraryOptions'];
?>

<?php wp_enqueue_script("jquery"); ?>

<?php get_header(); ?>

<style type="text/css">
	<?php echo $library_options['css'] ?>
</style>

<div id="main-col">
	<div class="now-reading nr_library">
		<div class="post fix nr-post">
			<h1 class="posttitle">Library</h1>
			
			<div class="bookdata fix">
				
				<?php if (can_now_reading_admin()) : ?>
					<div class="manage">
						<span class="icon">&nbsp;</span>
						<p>Admin: &raquo; <a href="<?php manage_library_url() ?>">Manage Books</a></p>
					</div>
				<?php endif; ?>
				
				<?php if ($library_options['showStats']) : ?>
					<h2>Statistics</h2>
					<p><?php print_book_stats() ?></p>
				<?php endif; ?>
				
			</div>
	
			<div class="entry">	
			<?php
				library_search_form();
				
				// Reading.
				$shelf_option = $library_options['readingShelf'];
				$shelf_title = "<h2>" . $shelf_option['title'] . " (" . total_books('reading', 0) . ")</h2>";
				$book_query = "status=reading&orderby=random";
				nr_load_template('shelf.php', false);
				
				// Unread.
				$shelf_option = $library_options['unreadShelf'];
				$shelf_title = "<h2>" . $shelf_option['title'] . " (" . total_books('unread', 0) . ")</h2>";
				$book_query = "status=unread&orderby=random";
				nr_load_template('shelf.php', false);
		
				// On Hold.
				$shelf_option = $library_options['onholdShelf'];
				$shelf_title = "<h2>" . $shelf_option['title'] . " (" . total_books('onhold', 0) . ")</h2>";
				$book_query = "status=onhold&orderby=random";
				nr_load_template('shelf.php', false);
		
				// Read.
				$shelf_option = $library_options['readShelf'];
				$shelf_title = "<h2>" . $shelf_option['title'] . " (" . total_books('read', 0) . ")</h2>";
				$book_query = "status=read&orderby=finished&order=desc";
				nr_load_template('shelf.php', false);
		
				do_action('nr_footer');
			?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
