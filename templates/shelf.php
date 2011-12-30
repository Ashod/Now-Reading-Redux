<?php

	global $book_query, $library_options, $shelf_name, $shelf_option;
	
	//$book_count = total_books($book_status, false);
	if ($shelf_option['viz'] != 'hide')
	{
?>
		<h4><?php echo $shelf_option['title'] ?> (<?php echo total_books($shelf_name, 0) ?>)</h4>
<?php
		if (have_books($book_query))
		{
			if ($library_options['renderStyle'] != 'table')
			{
				// List
?>
				<ul>
				<?php while(have_books($book_query)) : the_book(); ?>
					<li>
						<?php if ($shelf_option['viz'] != 'show_text') : ?>
							<a href="<?php book_permalink() ?>">
								<img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
						<?php endif; ?>
						<?php if ($shelf_option['viz'] != 'show_image') : ?>
							<br /><strong><a href="<?php book_permalink() ?>"><?php book_title() ?></a></strong> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
				</ul>
<?php
			}
			else
			{
				// Table
				//TODO: Control image/text visibility via options.
?>
				<table class='nr-shelf'>
				<?php
					$col_ctr = 0;
					$books_per_row = (int)$library_options['itemsPerTableRow'];
					if ($books_per_row <= 0)
					{
						$books_per_row = 1;
					}
					
					for ($i = 0; $i < $books_per_row - 1; $i++)
					{
				?>
						<col class='nr-shelf-slot'/>
				<?php
					}
				?>
						<col/>
				<?php
					$tr_closed = false;
					while( have_books($book_query) ) {
						the_book();
						$col_ctr++;
						if ($col_ctr%$books_per_row == 1) {
							$tr_closed = false;
				?>
					<tr>
				<?php
						}
				?>
						<td>
							<a href="<?php echo book_permalink(false); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"><img src="<?php echo book_image(false); ?>" alt="<?php echo book_title(false); ?> by <?php echo book_author(false); ?>" /></a>
						</td>
				<?php
						if ($col_ctr%$books_per_row == 0) {// || $col_ctr == $book_count) {
							$tr_closed = true;
				?>
					</tr>
				<?php
						}
					}
					if (!$tr_closed) {
						echo "</tr>\n";
					}
				?>
				</table>
			<?php
			}
		}
		else
		{
			// No books.
		?>
			<p>None</p>
		<?php
		}
	}
?>
