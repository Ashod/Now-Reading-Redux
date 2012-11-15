<?php
/**
 * Book search template for the Now Reading Redux plugin.
 */

global $book_query, $library_options, $shelf_title, $shelf_option;
global $empty_shelf_message;
$options = get_option(NOW_READING_OPTIONS);
$search_options = $options['searchOptions'];
get_header();
?>

<style type="text/css">
	<?php echo $search_options['css'] ?>
</style>

<div id="primary">
	<div id="content" role="main" class="narrowcolumn primary now-reading">

		<article <?php post_class('post nr-post'); ?>>
			<header class="post-header">
				<h1 class="posttitle"><?php _e('Search Results for', 'now-reading-redux');?> <i><?php search_query(); ?></i>:</h1>
			
				<div class="bookdata fix">
<?php
if( can_now_reading_admin() ) {
?>
					<div class="manage">
						<span class="icon">&nbsp;</span>
						<a href="<?php manage_library_url(); ?>"><?php _e('Manage Books', 'now-reading-redux');?></a>
					</div>
<?php
}
?>
					<div class="library">
						<span class="icon">&nbsp;</span>
						<a href="<?php library_url(); ?>"><?php _e('Back to library', 'now-reading-redux');?></a>
					</div>
				</div>
			</header>

			<div class="booklisting">
<?php
				library_search_form();

				$shelf_title = "<h2></h2>";
				$shelf_option = $search_options;
				$book_query = "status=all&search=" . search_query(false) . "&num=" . $shelf_option['maxItems'];
				$empty_shelf_message = __("Sorry, but there were no search results for your query.");
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
