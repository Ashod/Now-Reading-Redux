<?php
/**
 * Library template for the Now Reading plugin.
 */
global $book_query, $library_options, $shelf_title, $shelf_option;
$options = get_option(NOW_READING_OPTIONS);
$library_options = $options['libraryOptions'];
get_header();
?>

<?php wp_enqueue_script("jquery"); ?>

<style type="text/css">
	<?php echo $library_options['css'] ?>
</style>

<div id="primary">
	<div id="content" role="main" class="narrowcolumn primary now-reading">
		<article <?php post_class('post nr-post'); ?>>
			<header class="post-header">
				<h1 class="posttitle">Library</h1>

				<div class="bookdata fix">
<?php
				if (can_now_reading_admin())
				{
?>
					<div class="manage">
						<span class="icon">&nbsp;</span>
						<a href="<?php manage_library_url(); ?>"><?php _e('Manage Books', 'now-reading-redux');?></a>
					</div>
<?php
				}
?>
				</div>
			</header>

			<?php if ($library_options['showStats']) : ?>
				<h3>Statistics</h3>
				<p><?php print_book_stats() ?></p>
			<?php endif; ?>

			<div class="booklisting">
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
			</div><!-- /.booklisting -->
		</article><!-- /.nr-post -->
	</div><!-- /#content -->
</div><!-- /#main-col -->

<?php get_sidebar(); ?>

<?php
get_footer();
?>
