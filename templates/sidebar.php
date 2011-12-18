
<style type="text/css">
.nr_widget img {
	padding: 5px 5px 5px 5px;
	width: 65px;
	height: 100px;
}
.nr_widget h4 {
	border: 1px solid #c0c0c0;
	padding: 5px 5px 5px 5px;
	font: bold 100%/100% Arial, Helvetica, sans-serif;
	margin: 20px 0 5px 0;
	clear: both;
}
.nr_widget ul {
    list-style-type: none;
    padding: 0px;
    margin: 0px;
}
.nr_widget li {
    list-style-type: none;
    padding: 0px;
    margin: 0px;
    display: -moz-inline-box;
    -moz-box-orient: vertical;
    display: inline-block;
    vertical-align:top;
    word-wrap: break-word;
}
* html .nr_widget li {
    display: inline;
}
* + html .nr_widget li {
    display: inline;
    }
.nr_widget li > * {
    display: table;
    table-layout: fixed;
    overflow: hidden;
}
* html .nr_widget li { /* for IE 6 */
    width: 80px;
}
.nr_widget li > * { /* for all other browser */
    width: 80px;
}
}
</style>

<div class="now-reading nr_widget">

	<div align=center><br /><p><b><i><a href="<?php library_url() ?>">View Full Library</a></i></b></p></div>

	<h4>Current books:</h4>
	<?php if (have_books('status=reading&orderby=random')) : ?>
		<ul>
		<?php while(have_books('status=reading&orderby=random')) : the_book(); ?>
			<li>
                <a href="<?php book_permalink() ?>">
                    <img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
                <?php if (!sidebar_images_only()) : ?>
                    <p><strong><?php book_title() ?></strong> by <?php book_author() ?></p>
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
                    <p><strong><?php book_title() ?></strong> by <?php book_author() ?></p>
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
                    <p><strong><?php book_title() ?></strong> by <?php book_author() ?></p>
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
