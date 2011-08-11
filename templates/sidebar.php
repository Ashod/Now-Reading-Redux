<div class="now-reading">

	<h3>Current books:</h3>

	<?php if( have_books('status=reading&orderby=random') ) : ?>

		<ul>

		<?php while( have_books('status=reading&orderby=random') ) : the_book(); ?>

			<li>
				<p><a href="<?php book_permalink() ?>"><img src="<?php book_image() ?>" alt="<?php book_title() ?>" /></a></p>
				<p><strong><?php book_title() ?></strong> by <?php book_author() ?></p>
			</li>

		<?php endwhile; ?>

		</ul>

	<?php else : ?>

		<p>None</p>

	<?php endif; ?>

	<h3>Planned books:</h3>

	<?php if( have_books('status=unread&orderby=random') ) : ?>

		<ul>

		<?php while( have_books('status=unread&orderby=random') ) : the_book(); ?>

			<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a> by <?php book_author() ?></li>

		<?php endwhile; ?>

		</ul>

	<?php else : ?>

		<p>None</p>

	<?php endif; ?>

	<h3>Recent books:</h3>

	<?php if( have_books('status=read&orderby=finished&order=desc') ) : ?>

		<ul>

		<?php while( have_books('status=read&orderby=finished&order=desc') ) : the_book(); ?>

			<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a> by <?php book_author() ?></li>

		<?php endwhile; ?>

		</ul>

	<?php else : ?>

		<p>None</p>

	<?php endif; ?>

	<p><a href="<?php library_url() ?>">View full Library</a></p>

</div>
