<?php
/**
 * Search template for the Now Reading plugin.
 *
 * @package Suffusion
 * @subpackage NowReading
 */

global $nr_book_query;
get_header();
?>

<style type="text/css">
	<?php echo $search_options['css'] ?>
</style>

<div id="main-col">
<?php suffusion_before_begin_content(); ?>
	<div id="content">
<?php suffusion_after_begin_content(); ?>
		<article <?php post_class('post nr-post'); ?>>
			<header class="post-header">
				<h1 class="posttitle"><?php _e('Search Results for', 'suffusion');?> <i><?php search_query(); ?></i>:</h1>
			
				<div class="bookdata fix">
<?php
if( can_now_reading_admin() ) {
?>
					<div class="manage">
						<span class="icon">&nbsp;</span>
						<a href="<?php manage_library_url(); ?>"><?php _e('Manage Books', 'suffusion');?></a>
					</div>
<?php
}
?>
					<div class="library">
						<span class="icon">&nbsp;</span>
						<a href="<?php library_url(); ?>"><?php _e('Back to library', 'suffusion');?></a>
					</div>
				</div>
			</header>

			<div class="booklisting">
<?php
$nr_book_query = "status=all&num=-1&search=" . search_query(false);
get_template_part('now-reading/nr-shelf');
?>
			</div><!-- /.booklisting -->
		</article><!-- /.nr-post -->
	</div><!-- /#content -->
</div><!-- /#main-col -->
<?php
get_footer();
?>
