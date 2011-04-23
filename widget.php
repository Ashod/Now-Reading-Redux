<?php
/**
 * Adds our widget.
 * @package now-reading
 */

function nr_widget($args) {
    extract($args);

    $options = get_option('nowReadingWidget');
    $title = $options['title'];

    echo $before_widget . $before_title . $title . $after_title;
    if( !defined('NOW_READING_VERSION') || floatval(NOW_READING_VERSION) < 4.2 ) {
        echo "<p>You don't appear to have the Now Reading plugin installed, or have an old version; you'll need to install or upgrade before this widget can display your data.</p>";
    } else {
        nr_load_template('sidebar.php');
    }
    echo $after_widget;
}

function nr_widget_control() {
    $options = get_option('nowReadingWidget');

    if ( !is_array($options) )
        $options = array('title' => 'Now Reading');

    if ( $_POST['nowReadingSubmit'] ) {
        $options['title'] = htmlspecialchars(stripslashes($_POST['nowReadingTitle']), ENT_QUOTES, 'UTF-8');
        update_option('nowReadingWidget', $options);
    }

    $title = htmlspecialchars($options['title'], ENT_QUOTES, 'UTF-8');

    echo '
		<p style="text-align:right;">
			<label for="nowReadingTitle">Title:
				<input style="width: 200px;" id="nowReadingTitle" name="nowReadingTitle" type="text" value="'.$title.'" />
			</label>
		</p>
	<input type="hidden" id="nowReadingSubmit" name="nowReadingSubmit" value="1" />
	';
}

function nr_widget_init() {
    if ( !function_exists('register_sidebar_widget') )
        return;

    register_sidebar_widget(__('Now Reading', NRTD), 'nr_widget', null, 'now-reading');
    register_widget_control(__('Now Reading', NRTD), 'nr_widget_control', 300, 100, 'now-reading');
}

add_action('plugins_loaded', 'nr_widget_init');

?>
