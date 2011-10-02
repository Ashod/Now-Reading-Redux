<?php
/**
 * Library template for the Now Reading plugin.
 *
 * @package Suffusion
 * @subpackage NowReading
 */

get_header();
global $nr_book_query, $suffusion_unified_options;
foreach ($suffusion_unified_options as $id => $value) {
	$$id = $value;
}
$lib_order = suffusion_get_entity_order($suf_nr_lib_order, 'nr');
$lib_order = explode(',', $lib_order);
?>

<div id="main-col">
<?php suffusion_before_begin_content(); ?>
	<div id="content">
<?php suffusion_after_begin_content(); ?>
		<div class="post fix nr-post">
			<h1 class="posttitle"><?php echo stripslashes($suf_nr_lib_title); ?></h1>
			<div class="bookdata fix">
<?php
		if( can_now_reading_admin()) {
?>
				<div class="manage">
					<span class="icon">&nbsp;</span>
					<a href="<?php manage_library_url(); ?>"><?php _e('Manage Books', 'suffusion');?></a>
				</div>
<?php
		}
?>
			</div>
			<div class="entry">
<?php print_book_stats() ?>
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
			</div>
		</div>
	</div><!-- /#content -->
</div><!-- /#main-col -->
<?php
get_footer();
?>
