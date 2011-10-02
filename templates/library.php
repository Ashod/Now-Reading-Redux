<?php wp_enqueue_script("jquery"); ?>

<?php get_header() ?>

<div class="content">

	<div id="content" class="now-reading primary narrowcolumn">

	<div class="post">

		<?php if( can_now_reading_admin() ) : ?>

			<p>Admin: &raquo; <a href="<?php manage_library_url() ?>"><?php __('Manage Books', NRTD);?></a></p>

		<?php endif; ?>

		<p><?php print_book_stats() ?></p>

		<?php library_search_form() ?>

		<h2>Currently Reading (<?php echo total_books('reading', 0) ?>):</h2>

		<?php if( have_books('status=reading&orderby=random&num=-1') ) : ?>

			<ul>

			<?php while( have_books('status=reading&orderby=random&num=-1') ) : the_book(); ?>

				<li>
					<p><a href="<?php book_permalink() ?>"><img src="<?php book_image() ?>" alt="<?php book_title() ?>" /></a></p>
					<p><a href="<?php book_permalink() ?>"><?php book_title() ?></a> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a></p>
				</li>

			<?php endwhile; ?>

			</ul>

		<?php else : ?>

			<p>None</p>

		<?php endif; ?>

		<h2>Planned books (<?php echo total_books('unread', 0) ?>):</h2>

		<?php if( have_books('status=unread&orderby=random&num=-1') ) : ?>

			<ul>

			<?php while( have_books('status=unread&orderby=random&num=-1') ) : the_book(); ?>

				<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a></li>

			<?php endwhile; ?>

			</ul>

		<?php else : ?>

			<p>None</p>

		<?php endif; ?>

		<h2>Finished Reading (<?php echo total_books('read', 0) ?>):</h2>

		<?php if( have_books('status=read&orderby=finished&order=desc&num=-1') ) : ?>

			<ul>

			<?php while( have_books('status=read&orderby=finished&order=desc&num=-1') ) : the_book(); ?>

				<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a></li>

			<?php endwhile; ?>

			</ul>

		<?php else : ?>

			<p>None</p>

		<?php endif; ?>

		<?php do_action('nr_footer'); ?>

	</div>

	</div>

</div>

<?php get_sidebar() ?>

<?php get_footer() ?>
