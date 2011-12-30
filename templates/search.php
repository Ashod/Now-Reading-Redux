<?php
	global $book_query, $library_options, $shelf_title, $shelf_option;
	global $empty_shelf_message;
	$options = get_option(NOW_READING_OPTIONS);
	$search_options = $options['searchOptions'];
?>

<?php get_header(); ?>

<style type="text/css">
	<?php echo $search_options['css'] ?>
</style>

<div id="main-col">
	<div id="container" class="now-reading nr_library">
		<div id="content" role="main">
			<div class="post fix nr-post">
				<h1 class="post-title entry-title">Library</h1>
			
				<div class="bookdata fix">
					
					<?php if (can_now_reading_admin()) : ?>
						<div class="manage">
							<span class="icon">&nbsp;</span>
							<p>Admin: &raquo; <a href="<?php manage_library_url() ?>">Manage Books</a></p>
						</div>
					<?php endif; ?>
					
					<p><a href="<?php library_url() ?>">&larr; Back to library</a></p>
				</div>

				<div class="entry-content">
				<?php
					library_search_form();

					$shelf_title = "<h2>" . __("Search results for ") . "<i>" . search_query(false) . "</i></h2>";
					$shelf_option = $search_options;
					$book_query = "status=all&search={$GLOBALS['query']}&num=" . $shelf_option['maxItems'];
					$empty_shelf_message = __("Sorry, but there were no search results for your query.");
					nr_load_template('shelf.php', false);
		
					do_action('nr_footer');
				?>
				</div><!-- .entry-content -->
			</div><!-- .post -->
		</div><!-- #content -->
	</div><!-- #container -->

	<?php get_sidebar() ?>

</div>

<?php get_footer(); ?>
