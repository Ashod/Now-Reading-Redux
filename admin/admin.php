<?php
/**
 * NowReadingRedux class for admin actions.
 *
 * This class contains all functions and actions required for Now Reading Redux to work in the admin of WordPress.
 *
 * @package now-reading-redux
 * @subpackage admin
 * @since 6.0.4.0
 */
class NowReadingReduxAdmin extends NowReadingRedux {

    /**
     * Plugin Version
     *
     * @since 6.0.0.0
     * @var int
     */
    var $version = '6.0.4.0';

    /**
     * Full file system path to the main plugin file
     *
     * @since 6.0.0.0
     * @var string
     */
    var $plugin_file;

    /**
     * Path to the main plugin file relative to wp-content/plugins
     *
     * @since 6.0.0.0
     * @var string
     */
    var $plugin_basename;

    /**
     * Name of options page hook
     *
     * @since 6.0.0.0
     * @var string
     */
    var $options_page_hookname;

    /**
     * PHP 4 Style constructor which calls the below PHP5 Style Constructor
     *
     * @since 6.0.0.0
     * @return none
     */
    function NowReadingReduxAdmin () {
        $this->__construct();
    }

    /**
     * Setup backend functionality in WordPress
     *
     * @return none
     * @since 3.0.0.0
     */
    function __construct () {
        NowReadingRedux::__construct ();

        if ( version_compare ( $this->get_option ( 'version' ) , $this->version , '!=' ) && $this->get_option ( 'version' ) !== false )
            $this->check_upgrade ();

        // Full path and plugin basename of the main plugin file
        $this->plugin_file = dirname ( dirname ( __FILE__ ) ) . '/now-reading.php';
        $this->plugin_basename = plugin_basename ( $this->plugin_file );

        // Activation hook
        register_activation_hook ( $this->plugin_file , array ( &$this , 'init' ) );

        // Load localizations if available
        load_plugin_textdomain ( 'now-reading-redux' , false , 'now-reading-redux/translations' );

        // Whitelist options
        add_action ( 'admin_init' , array ( &$this , 'register_settings' ) );

        // add mod-rewrite rules on init
        add_action( 'init' , array ( &$this , 'nr_mod_rewrite' )  );

        // Activate the options page
        add_action ( 'admin_menu' , array ( &$this , 'add_page' ) ) ;

        //if ( function_exists ( 'wp_enqueue_scripts' ) ) {
        //    add_action ( "in_plugin_update_message-{$this->plugin_basename}" , array ( &$this , 'changelog' ) );
        //}
    }

    /**
     * Whitelist the Now Reading Redux options
     *
     * @since 6.0.0.0
     * @return none
     */
    function register_settings () {
        register_setting ( 'nowReadingOptions' , 'nowReadingOptions' , array ( &$this , 'update' ) );
    }

    /**
     * Enqueue javascript into the admin to hide/show the advanced settings
     *
     * @return none
     * @since 6.0.0.0
     */
    function admin_js () {
        //wp_enqueue_script ( 'jquery' );
    }


    /**
     * Return the default options
     *
     * @return array
     * @since 2.0.3
     */
    function defaults () {
        $defaults = array (
                'version'			=>	$this->version,
                //'AWSAccessKeyId'	=>	null,
                //'SecretAccessKey'	=>	null,
                'formatDate'		=>	'jS F Y',
                'associate'			=>	'amodcon-20',
				'ignoreTime'		=>	false,
				'hideAddedDate'		=>	false,
                'debugMode'			=>	'false',
                'useModRewrite'		=>	'true',
                'proxyHost'			=>	null,
                'proxyPort'			=>	null,
                'booksPerPage'		=>	15,
                'booksPerPage'		=>	5,
                'multiuserMode'     =>	'true',
                'imageSize'         =>  'medium',
                'httpLib'           =>  'snoopy',
                'permalinkBase'     =>  'library/'
        );
        return $defaults;
    }

    /**
     * Initialize the default options during plugin activation
     *
     * @return none
     * @since 6.0.0.0
     */
    function init () {
        if ( ! get_option ( 'nowReadingOptions' ) )
            add_option ( 'nowReadingOptions' , $this->defaults () );
        else
            $this->check_upgrade();
    }

    /**
     * Check if an upgraded is needed
     *
     * @return none
     * @since 6.0.0.0
     */
    function check_upgrade () {
        if ( version_compare ( $this->get_option ( 'version' ) , $this->version , '<' ) )
            $this->upgrade ( $this->version );
    }

    /**
     * Upgrade options
     *
     * @return none
     * @since 6.0.0.0
     */
    function upgrade ( $ver ) {
        if ( $ver == '6.0.0.0' ) {
            $shadowbox = get_option ( 'shadowbox' );
            $newopts = array (
                    'version'			=>	$this->version ,
                    'smartLoad'			=>	'false' ,
                    'enableFlv'			=>	'false' ,
                    'tubeWidth'			=>	640 ,
                    'tubeHeight'		=>	385 ,
                    'players'			=>	$this->players () ,
                    'autoDimensions'            =>	'false' ,
                    'showOverlay'		=>	'true' ,
                    'skipSetup'			=>	'false' ,
                    'flashParams'		=>	'{bgcolor:"#000000", allowFullScreen:true}' ,
                    'flashVars'			=>	'{}' ,
                    'flashVersion'		=>	'9.0.0'
            );
            unset ( $shadowbox['ie8hack'] , $shadowbox['skin'] );
            update_option ( 'shadowbox' , array_merge ( $shadowbox , $newopts ) );
        }
    }

    /**
     * Update/validate the options in the options table from the POST
     *
     * @since 6.0.0.0
     * @return none
     */
    function update ( $options ) {
        return $options;
    }

    /**
     * Add the options and management pages
     *
     * @return none
     * @since 6.0.0.0
     */
    function add_page () {

        //changing NR level access in order to let blog authors to add books in multiuser mode (B. Spyckerelle)
        $nr_level = $this->get_option('multiuserMode') ? 2 : 9 ;

        add_menu_page('Now Reading', 'Now Reading', 9, 'add_book', 'now_reading_add');

        add_submenu_page('add_book', 'Add a Book', 'Add a Book',$nr_level , 'add_book', 'now_reading_add');
        add_submenu_page('add_book', 'Manage Books', 'Manage Books', $nr_level, 'manage_books', 'nr_manage');

        if ( current_user_can ( 'manage_options' ) ) {
            $this->options_page_hookname = add_options_page ( __( 'Now Reading' , 'now-reading-redux' ) , __( 'Now Reading' , 'now-reading-redux' ) , 'manage_options' , 'now-reading-redux' , array ( &$this , 'admin_page' ) );
        }
    }


    /**
     * Add a settings link to the plugin actions
     *
     * @param array $links Array of the plugin action links
     * @return array
     * @since 6.0.0.0
     */
    function filter_plugin_actions ( $links ) {
        $settings_link = '<a href="options-general.php?page=now-reading-redux">' . __( 'Options' ) . '</a>';
        array_unshift ( $links, $settings_link );
        return $links;
    }


    /**
     * Adds our rewrite rules for the library and book permalinks to the regular WordPress ones.
     * @param array $rules The existing array of rewrite rules we're filtering
     * @return none
     * @since 6.0.0.0
     */
    function nr_mod_rewrite() {
        $cleanBase = preg_quote($this->get_option( 'permalinkBase' ));

        add_rewrite_rule($cleanBase . '([0-9]+)/?$', 'index.php?now_reading_id=$matches[1]', 'top');
        add_rewrite_rule($cleanBase . 'tag/([^/]+)/?$', 'index.php?now_reading_tag=$matches[1]', 'top');
        add_rewrite_rule($cleanBase . 'page/([^/]+)/?$', 'index.php?now_reading_page=$matches[1]', 'top');
        add_rewrite_rule($cleanBase . 'search/?$', 'index.php?now_reading_search=true', 'top');
        add_rewrite_rule($cleanBase . 'reader/([^/]+)/?$', 'index.php?now_reading_reader=$matches[1]', 'top');
        add_rewrite_rule($cleanBase . '([^/]+)/([^/]+)/?$', 'index.php?now_reading_author=$matches[1]&now_reading_title=$matches[2]', 'top');
        add_rewrite_rule($cleanBase . '([^/]+)/?$', 'index.php?now_reading_author=$matches[1]', 'top');
        add_rewrite_rule($cleanBase . '?$', 'index.php?now_reading_library=1', 'top');

        global $wp_rewrite;
        $wp_rewrite->flush_rules();

    }



    /**
     * Output the options page
     *
     * @return none
     * @since 2.0.3
     */
    function admin_page () {
        global $wpdb;
        ?>
<div class="wrap">
    <h2><?php _e( 'Now Reading Redux' ); ?></h2>
    <form action="options.php" method="post">
                <?php settings_fields('nowReadingOptions'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                            <?php _e('Amazon Web Services Access Key ID', 'now-reading-redux')?>
                </th>
                <td>
                    <input type="text" size="50" name="nowReadingOptions[AWSAccessKeyId]" value="<?php echo $this->get_option('AWSAccessKeyId'); ?>" />
                    <p>
                        <span class="description">
                                    <?php _e('Required to add books from Amazon.  It\'s free to sign up. Register <a href="https://aws-portal.amazon.com/gp/aws/developer/registration/index.html">here</a>', 'now-reading-redux')?>
                        </span>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                            <?php _e('Amazon Web Services Secret Access Key', 'now-reading-redux')?>
                </th>
                <td>
                    <input type="text" size="50" name="nowReadingOptions[SecretAccessKey]" value="<?php echo $this->get_option('SecretAccessKey'); ?>" />
                    <p>
                        <span class="description">
                                    <?php _e('Required to add books from Amazon.  It\'s free to sign up. Register <a href="https://aws-portal.amazon.com/gp/aws/developer/registration/index.html">here</a>', 'now-reading-redux')?>
                        </span>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                            <?php _e('Date format string', 'now-reading-redux')?>
                </th>
                <td>
                    <input type="text" size="50" name="nowReadingOptions[formatDate]" value="<?php echo $this->get_option('formatDate'); ?>" />
                    <p>
                        <span class="description">
                                    <?php _e('How to format the book\'s <code>added</code>, <code>started</code> and <code>finished</code> dates. Acceptable variables can be found <a href="http://php.net/date">here</a>', 'now-reading-redux')?>
                        </span>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                            <?php _e('Your Amazon Associates ID', 'now-reading-redux')?>
                </th>
                <td>
                    <input type="text" size="50" name="nowReadingOptions[associate]" value="<?php echo $this->get_option('associate'); ?>" />
                    <p>
                        <span class="description">
                                    <?php _e('If you choose to link to your book\'s product page on Amazon.com using the <code>book_url()</code> template tag - as the default template does - then you can earn commission if your visitors then purchase products.', 'now-reading-redux')?>
                        </span>
                    </p>
                    <p>
                        <span class="description">
                                    <?php sprintf(__('If you don\'t have an Amazon Associates ID, you can either <a href=\'%s\'>get one</a>, or consider entering mine - <strong>%s</strong> - if you\'re feeling generous.', 'now-reading-redux'), "http://associates.amazon.com", "amodcon-20")?>
                        </span>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                            <?php _e('Amazon Domain to Use', 'now-reading-redux')?>
                </th>
                <td>
                    <select name="nowReadingOptions[domain]">
                                <?php
                                foreach ( (array) $this->nr_domains as $domain => $country ) {
                                    ?>
                        <option value="<?php echo $domain; ?>" <?php selected ( $domain , $this->get_option ( 'domain' ) ); ?>><?php echo $country; ?> (Amazon<?php echo $domain; ?>)</option>
                                    <?php } ?>
                    </select>
                    <p>
                        <span class="description">
                                    <?php _e('If you choose to link to your book\'s product page on Amazon.com using the <code>book_url()</code> template tag, you can specify which country-specific Amazon site to link to. Now Reading will also use this domain when searching.', 'now-reading-redux'); ?>
                        </span>
                    </p>
                    <p>
                        <span class="description">
                                    <?php _e('NB: If you have country-specific books in your catalogue and then change your domain setting, some old links might stop working.', 'now-reading-redux'); ?>
                        </span>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                            <?php _e('Image size to Use', 'now-reading-redux')?>
                </th>
                <td>
                    <select name="nowReadingOptions[imageSize]">
                        <option value="Small" <?php selected ( 'Small' , $this->get_option ( 'imageSize' ) ); ?>><?php _e('Small','now-reading-redux'); ?></option>
                        <option value="Medium" <?php selected ( 'Medium' , $this->get_option ( 'imageSize' ) ); ?>><?php _e('Medium','now-reading-redux'); ?></option>
                        <option value="Large" <?php selected ( 'Large' , $this->get_option ( 'imageSize' ) ); ?>><?php _e('Large','now-reading-redux'); ?></option>
                    </select>
                    <p>
                        <span class="description">
                                    <?php _e('NB: This change will only be applied to books you add from this point onwards.', 'now-reading-redux'); ?>
                        </span>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                    <th scope="row">
                        <label for="nowReadingOptions[booksPerPage]"><?php _e('Books per page', 'now-reading-redux'); ?></label>
                    </th>
                    <td>
                         <input type="text" name="nowReadingOptions[booksPerPage]" value="<?php echo $this->get_option('booksPerPage'); ?>" />
                    </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Use <code>mod_rewrite</code> enhanced library?', 'now-reading-redux'); ?>
                </th>
                <td>
                    <input type="checkbox" name="nowReadingOptions[useModRewrite]" id="nowReadingOptions[useModRewrite]"  <?php checked ( 'on' , $this->get_option ( 'useModRewrite' ) ); ?> />
                    <p>
                            <?php _e("If you have an Apache webserver with <code>mod_rewrite</code>, you can enable this option to have your library use prettier URLs. Compare:", 'now-reading-redux'); ?>
                    </p>
                    <p>
                            <code>/index.php?now_reading_single=true&now_reading_author=albert-camus&now_reading_title=the-stranger</code>
                    </p>
                    <p>
                            <code>/library/albert-camus/the-stranger/</code>
                    </p>
                    <p>
                            <?php sprintf(__("If you choose this option, be sure you have a custom permalink structure set up at your <a href='%s'>Options &rarr; Permalinks</a> page.", 'now-reading-redux'), 'options-permalink.php'); ?>
                    </p>
                    <p>
                    <?php _e("Permalink base") . ': ' . get_option('home') . '/' ; ?>
                    <input type="text" name="nowReadingOptions[permalinkBase]" id="nowReadingOptions[permalinkBase]" value=" <?php echo $this->get_option ( 'permalinkBase' ); ?>" />
                    </p>
                </td>
        </tr>

        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>
        <?php
    }
}

?>
