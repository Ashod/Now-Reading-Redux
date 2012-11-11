<?php
	global $book_query, $library_options, $shelf_title, $shelf_option;
	global $empty_shelf_message;

	//$book_count = total_books($book_status, false);
	if ($shelf_option['viz'] != 'hide')
	{
		echo $shelf_title;

		if (have_books($book_query))
		{
			echo '<div class="booklisting">';
			if ($library_options['renderStyle'] == 'list')
			{
				// Unordered List:
?>
				<ul>
				<?php while(have_books($book_query)) : the_book(); ?>
					<li>
						<?php if ($shelf_option['viz'] == 'show_image' || $shelf_option['viz'] == 'show_image_text') : ?>
							<a href="<?php book_permalink() ?>">
								<img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
                                <br />
						<?php endif; ?>
						<?php if ($shelf_option['viz'] == 'show_text' || $shelf_option['viz'] == 'show_image_text') : ?>
							<strong><a href="<?php book_permalink() ?>"><?php book_title() ?></a></strong> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
				</ul>
<?php
			}
			else
			if ($library_options['renderStyle'] == 'numbered')
			{
				// Ordered List:
?>
				<ol>
				<?php while(have_books($book_query)) : the_book(); ?>
					<li>
						<?php if ($shelf_option['viz'] == 'show_image' || $shelf_option['viz'] == 'show_image_text') : ?>
							<a href="<?php book_permalink() ?>">
								<img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
                                <br />
						<?php endif; ?>
						<?php if ($shelf_option['viz'] == 'show_text' || $shelf_option['viz'] == 'show_image_text') : ?>
							<strong><a href="<?php book_permalink() ?>"><?php book_title() ?></a></strong> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
				</ol>
<?php
			}
			else
			{
				// Table:
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
					
					echo "<colgroup>\n";
					for ($i = 0; $i < $books_per_row - 1; $i++)
					{
						$polarity = ($i % 2 == 0) ? 'odd' : 'even';
						echo "<col class='nr-shelf-slot-{$polarity}'/>\n";
					}

					echo "</colgroup>\n";

					$tr_closed = false;
					while (have_books($book_query))
					{
						the_book();
						$col_ctr++;
						if ($col_ctr%$books_per_row == 1)
						{
							$tr_closed = false;
							echo "<tr>\n";
						}
				?>
						<td>
						<?php if ($shelf_option['viz'] == 'show_image' || $shelf_option['viz'] == 'show_image_text') : ?>
							<a href="<?php book_permalink() ?>">
								<img src="<?php book_image() ?>" alt="<?php echo esc_attr(book_title(false)); ?>" title="<?php echo esc_attr(book_title(false)); ?> by <?php echo esc_attr(book_author(false)); ?>"/></a>
                                <br />
						<?php endif; ?>
						<?php if ($shelf_option['viz'] == 'show_text' || $shelf_option['viz'] == 'show_image_text') : ?>
							<strong><a href="<?php book_permalink() ?>"><?php book_title() ?></a></strong> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a>
						<?php endif; ?>
						</td>
				<?php
						if ($col_ctr%$books_per_row == 0)
						{
							$tr_closed = true;
							echo "</tr>\n";
						}
					}
					
					if (!$tr_closed)
					{
						echo "</tr>\n";
					}
					
					echo "</table>\n";
			}

			echo "</div>\n";
		}
		else
		{
			// No books.
			echo '<p><b>' .isset($empty_shelf_message) ? $empty_shelf_message : 'None' . '</b></p>';
		}
	}
?>
