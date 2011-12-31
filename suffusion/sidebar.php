<?php
/**
 * Widget template for the Now Reading plugin.
 *
 * @package Suffusion
 * @subpackage NowReading
 */

global $nr_book_query, $suf_nr_wid_order, $suf_nr_wid_search_show, $suf_nr_wid_curr_show, $suf_nr_wid_curr_title, $suf_nr_no_books_text, $suf_nr_wid_unread_show, $suf_nr_wid_unread_title, $suf_nr_wid_completed_show, $suf_nr_wid_completed_title;

$lib_order = suffusion_get_entity_order($suf_nr_wid_order, 'nr');
$lib_order = explode(',', $lib_order);

?>

<?php
	global $book_query, $library_options, $shelf_title, $shelf_option;
	$options = get_option(NOW_READING_OPTIONS);
	$library_options = $options['sidebarOptions'];
?>

<style type="text/css">
	<?php echo $library_options['css'] ?>
</style>


<div class="now-reading">

<?php
$cururl = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$cururl = rtrim($cururl, '/');
$liburl = library_url(false);
$liburl = rtrim($liburl, '/');
if ($cururl != $liburl)
{
?>
	<div style="text-align:center"><b><a href="<?php echo $liburl ?>">View Full Library</a></b></div>
<?php
}
?>

<?php
if ($suf_nr_wid_search_show == 'top') {
	library_search_form();
}
foreach ($lib_order as $entity) {
	if ($entity == 'current' && $suf_nr_wid_curr_show == 'show') {
?>
	<h4><?php echo stripslashes($suf_nr_wid_curr_title)  . " (" . total_books('reading', 0) . ")" ?></h4>
<?php
		$shelf_option = $library_options['readingShelf'];
		if( have_books('status=reading&orderby=random&num=' . $shelf_option['maxItems']) ) {
			while(have_books('status=reading&orderby=random&num=' . $shelf_option['maxItems'])) {
				the_book();
?>
	<a href="<?php book_permalink() ?>"><img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
<?php
			}
		}
		else {
?>
				<p>
			<?php
				$strip = stripslashes($suf_nr_no_books_text);
				$strip = wp_specialchars_decode($strip, ENT_QUOTES);
				echo $strip;
			?>
				</p>
<?php
		}
	}
	else if ($entity == 'unread' && $suf_nr_wid_unread_show == 'show') {
?>
	<h4><?php echo stripslashes($suf_nr_wid_unread_title)  . " (" . total_books('unread', 0) . ")" ?></h4>
<?php
		$shelf_option = $library_options['unreadShelf'];
		if( have_books('status=unread&orderby=random&num=' . $shelf_option['maxItems']) ) {
			while(have_books('status=unread&orderby=random&num=' . $shelf_option['maxItems'])) {
				the_book();
?>
	<a href="<?php book_permalink() ?>"><img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
<?php
			}
		}
		else {
?>
				<p>
				<?php
					$strip = stripslashes($suf_nr_no_books_text);
					$strip = wp_specialchars_decode($strip, ENT_QUOTES);
					echo $strip;
				?>
				</p>
<?php
		}
	}
	else if ($entity == 'completed' && $suf_nr_wid_completed_show == 'show') {
?>
	<h4><?php echo stripslashes($suf_nr_wid_completed_title)  . " (" . total_books('read', 0) . ")" ?></h4>
<?php
		$shelf_option = $library_options['readShelf'];
		if(have_books('status=read&orderby=finished&order=desc&num=' . $shelf_option['maxItems'])) {
			while(have_books('status=read&orderby=finished&order=desc&num=' . $shelf_option['maxItems'])) {
				the_book();
?>
	<a href="<?php book_permalink() ?>"><img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
<?php
			}
		}
		else {
?>
				<p>
				<?php
					$strip = stripslashes($suf_nr_no_books_text);
					$strip = wp_specialchars_decode($strip, ENT_QUOTES);
					echo $strip;
				?>
				</p>
<?php
		}
	}
}

if ($suf_nr_wid_search_show == 'bottom')
{
	library_search_form();
}

?>

<?php if (have_wishlist_url()) : ?>
	<div class="nr_wishlist"><b><a href="<?php wishlist_url() ?>">Buy me a gift!</a></b></div>
<?php endif; ?>

	<div style="display:none; text-align:center; font-size:120%; padding: 4px; text-shadow: 0 0 0.1em grey;" class="nr_ads">
		<a href="http://blog.ashodnakashian.com/projects/now-reading-redux" target="_blank" style="text-decoration: none">
		<i>Now Reading</i>
		<div style="font-weight:bold; font-family: arial; position:relative; top:-7px; left:42px; font-size:140%; color:#999; text-shadow: 0 0 0.1em #FFFFCC;">Redux</div>
		</a>
	</div>

</div>
