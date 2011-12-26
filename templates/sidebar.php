
<style type="text/css">
<?php echo sidebar_css() ?>
</style>

<div class="now-reading nr_widget">

	<div align=center><br /><b><a href="<?php library_url() ?>">View Full Library</a></b></div>

	<h4>Current books:</h4>
	<?php if (have_books('status=reading&orderby=random')) : ?>
		<ul>
		<?php while(have_books('status=reading&orderby=random')) : the_book(); ?>
			<li>
                <a href="<?php book_permalink() ?>">
                    <img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
                <?php if (!sidebar_images_only()) : ?>
						<br /><strong><a href="<?php book_permalink() ?>"><?php book_title() ?></a></strong> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a>
            	<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
	<?php else : ?>
		<p>None</p>
	<?php endif; ?>

	<h4>Planned books:</h4>
	<?php if (have_books('status=unread&orderby=random')) : ?>
		<ul>
		<?php while(have_books('status=unread&orderby=random')) : the_book(); ?>
			<li>
                <a href="<?php book_permalink() ?>">
                    <img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
                <?php if (!sidebar_images_only()) : ?>
						<br /><strong><a href="<?php book_permalink() ?>"><?php book_title() ?></a></strong> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a>
            	<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
	<?php else : ?>
		<p>None</p>
	<?php endif; ?>

	<h4>Recent books:</h4>
	<?php if (have_books('status=read&orderby=finished&order=desc')) : ?>
		<ul>
		<?php while(have_books('status=read&orderby=finished&order=desc')) : the_book(); ?>
			<li>
                <a href="<?php book_permalink() ?>">
                    <img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
                <?php if (!sidebar_images_only()) : ?>
						<br /><strong><a href="<?php book_permalink() ?>"><?php book_title() ?></a></strong> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a>
            	<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
	<?php else : ?>
		<p>None</p>
	<?php endif; ?>
    
    <?php if (have_wishlist_url()) : ?>
    	<div align=center><b><a href="<?php wishlist_url() ?>">Buy me a gift!</a></b></div>
	<?php endif; ?>

    <div align=center><?php library_search_form() ?></div>	

</div>
