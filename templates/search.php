<?php get_header(); ?>

<div id="container">

	<style type="text/css">
	<?php echo library_css() ?>
	</style>

	<div id="content" class="now-reading nr_library primary narrowcolumn">

	<div class="post">

		<?php if (can_now_reading_admin()) : ?>
			<p>Admin: &raquo; <a href="<?php manage_library_url() ?>"><?php __('Manage Books', NRTD);?></a></p>
		<?php endif; ?>

		<p><a href="<?php library_url() ?>">&larr; Back to library</a></p>

		<?php library_search_form() ?>

		<p>Search results for <?php search_query(); ?>:</p>
		
		<?php if( have_books("status=all&num=-1&search={$GLOBALS['query']}") ) : ?>
			<ul>
			<?php while( have_books("status=all&num=-1&search={$GLOBALS['query']}") ) : the_book(); ?>
				<li>
					<a href="<?php book_permalink() ?>">
						<img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
						<br /><strong><a href="<?php book_permalink() ?>"><?php book_title() ?></a></strong> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a>
				</li>
			<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<p>Sorry, but there were no search results for your query.</p>
		<?php endif; ?>

		<?php do_action('nr_footer'); ?>

	</div>
	</div>
</div>

<?php get_sidebar() ?>

<?php get_footer() ?>
