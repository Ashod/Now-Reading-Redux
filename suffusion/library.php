<?php
/**
 * Library template for the Now Reading plugin.
 *
 * @package Suffusion
 * @subpackage NowReading
 */

global $nr_book_query, $suf_nr_lib_order, $suf_nr_lib_title, $suf_nr_lib_curr_show, $suf_nr_lib_curr_title, $suf_nr_lib_curr_text, $suf_nr_lib_unread_show, $suf_nr_lib_unread_title;
global $suf_nr_lib_unread_text, $suf_nr_lib_completed_show, $suf_nr_lib_completed_title, $suf_nr_lib_completed_text;

$lib_order = suffusion_get_entity_order($suf_nr_lib_order, 'nr');
$lib_order = explode(',', $lib_order);

global $book_query, $library_options, $shelf_title, $shelf_option;
$options = get_option(NOW_READING_OPTIONS);
$library_options = $options['libraryOptions'];
get_header();
?>

<?php wp_enqueue_script("jquery"); ?>

<style type="text/css">
	<?php echo $library_options['css'] ?>
</style>

<div id="main-col">
<?php suffusion_before_begin_content(); ?>
	<div id="content">
<?php suffusion_after_begin_content(); ?>
		<article <?php post_class('post nr-post'); ?>>
			<header class="post-header">
				<h1 class="posttitle"><?php echo stripslashes($suf_nr_lib_title); ?></h1>

				<div class="bookdata fix">
<?php
				if (can_now_reading_admin())
				{
?>
					<div class="manage">
						<span class="icon">&nbsp;</span>
						<a href="<?php manage_library_url(); ?>"><?php _e('Manage Books', 'suffusion');?></a>
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

			<div class="entry">
<?php
		foreach ($lib_order as $entity) {
			if ($entity == 'current' && $suf_nr_lib_curr_show == 'show') {
?>
				<h3><?php echo stripslashes($suf_nr_lib_curr_title);?> (<?php echo total_books('reading', 0) ?>)</h3>
<?php
				echo stripslashes($suf_nr_lib_curr_text);
?>
				<div class="booklisting">
				<?php
				$nr_book_query = "status=reading&orderby=random&num=-1";
				get_template_part('now-reading/nr-shelf');
				?>
				</div>
<?php
			}
			else if ($entity == 'unread' && $suf_nr_lib_unread_show == 'show') {
?>
				<h3><?php echo stripslashes($suf_nr_lib_unread_title); ?> (<?php echo total_books('unread', 0) ?>)</h3>
<?php
				echo stripslashes($suf_nr_lib_unread_text);
?>
				<div class="booklisting">
				<?php
				$nr_book_query = "status=unread&orderby=random&num=-1";
				get_template_part('now-reading/nr-shelf');
				?>
				</div>
<?php
			}
			else if ($entity == 'completed' && $suf_nr_lib_completed_show == 'show') {
?>
				<h3><?php echo stripslashes($suf_nr_lib_completed_title); ?> (<?php echo total_books('read', 0) ?>)</h3>
<?php
				echo stripslashes($suf_nr_lib_completed_text);
?>
				<div class="booklisting">
				<?php
				$nr_book_query = "status=read&orderby=finished&order=desc&num=-1";
				get_template_part('now-reading/nr-shelf');
				?>
				</div>
<?php
			}
		}
?>
			</div><!-- /.booklisting -->
		</article><!-- /.nr-post -->
	</div><!-- /#content -->
</div><!-- /#main-col -->
<?php
get_footer();
?>
