<?php
/**
 * Library template for the Now Reading plugin.
 */
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
	<div id="container" class="now-reading nr_library">
		<div id="content" role="main">
			<article <?php post_class('post nr-post'); ?>>
				<h1 class="post-title entry-title">Library</h1>
			
				<div class="bookdata fix">
					
					<?php if (can_now_reading_admin()) : ?>
						<div class="manage">
							<span class="icon">&nbsp;</span>
							<p>Admin: &raquo; <a href="<?php manage_library_url() ?>">Manage Books</a></p>
						</div>
					<?php endif; ?>

					<?php if ($library_options['showStats']) : ?>
						<h3>Statistics</h3>
						<p><?php print_book_stats() ?></p>
					<?php endif; ?>

				</div>

				<div class="entry-content">
				<?php
					library_search_form();

					// Reading.
					$shelf_option = $library_options['readingShelf'];
					$shelf_title = "<h3>" . $shelf_option['title'] . " (" . total_books('reading', 0) . ")</h3>";
					$book_query = "status=reading&orderby=random";
					nr_load_template('shelf.php', false);
					
					// Unread.
					$shelf_option = $library_options['unreadShelf'];
					$shelf_title = "<h3>" . $shelf_option['title'] . " (" . total_books('unread', 0) . ")</h3>";
					$book_query = "status=unread&orderby=random";
					nr_load_template('shelf.php', false);
			
					// On Hold.
					$shelf_option = $library_options['onholdShelf'];
					$shelf_title = "<h3>" . $shelf_option['title'] . " (" . total_books('onhold', 0) . ")</h3>";
					$book_query = "status=onhold&orderby=random";
					nr_load_template('shelf.php', false);
			
					// Read.
					$shelf_option = $library_options['readShelf'];
					$shelf_title = "<h3>" . $shelf_option['title'] . " (" . total_books('read', 0) . ")</h3>";
					$book_query = "status=read&orderby=finished&order=desc";
					nr_load_template('shelf.php', false);

				?>
				</div><!-- .entry-content -->
			</article><!-- .post -->
		</div><!-- #content -->
	</div><!-- #container -->

	<?php get_sidebar() ?>
	
</div>

<?php get_footer(); ?>
