<?php
/**
 * Book tag template for the Now Reading Redux plugin.
 */

global $book_query, $library_options, $shelf_title, $shelf_option;
$options = get_option(NOW_READING_OPTIONS);
$library_options = $options['libraryOptions'];
$shelf_option = $library_options['readShelf'];
get_header();
?>

<div id="primary">
	<div id="content" role="main" class="narrowcolumn primary now-reading">

		<article <?php post_class('post nr-post'); ?>>
			<header class="post-header">
				<h1 class="posttitle"><?php _e('Books tagged with', 'now-reading-redux');?> <i><?php the_tag(); ?></i>:</h1>

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
					<div class="library">
						<span class="icon">&nbsp;</span>
						<a href="<?php library_url(); ?>"><?php _e('Back to library', 'now-reading-redux');?></a>
					</div>
				</div>
			</header>

			<div class="booklisting">
<?php
				$shelf_title = "<h4></h4>";
				$book_query = "tag=" . the_tag(false) . "&num=-1";
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
