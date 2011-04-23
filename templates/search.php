<?php get_header(); ?>

<div class="content">
	
	<div id="content" class="narrowcolumn primary now-reading">
	
	<div class="post">
		
		<?php if( can_now_reading_admin() ) : ?>
			
			<p>Admin: &raquo; <a href="<?php manage_library_url() ?>">Manage Books</a></p>
			
		<?php endif; ?>
		
		<p><a href="<?php library_url() ?>">&larr; Back to library</a></p>
		
		<?php library_search_form() ?>
		
		<p>Search results for <?php search_query(); ?>:</p>
		
		<?php if( have_books("status=all&num=-1&search={$GLOBALS['query']}") ) : ?>
			
			<ul>
			
			<?php while( have_books("status=all&num=-1&search={$GLOBALS['query']}") ) : the_book(); ?>
				
				<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a></li>
				
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
