<?php
if (!defined('ABSPATH'))
    exit;

class almasder4wordpress {

    /**
     * The single instance of almasder4wordpress.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($file = '', $version = '1.0.0') {
        $this->_version = $version;
        $this->_token = 'almasder4wordpress';

        // Load plugin environment variables
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

        $this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        register_activation_hook($this->file, array($this, 'install'));

        // Load frontend JS & CSS
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 10);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 10);

        // Load admin JS & CSS
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_styles'), 10, 1);



        // Load API for generic admin functions
        if (is_admin()) {
            $this->admin = new almasder4wordpress_Admin_API();
        }

        // Handle localisation
        $this->load_plugin_textdomain();
        add_action('init', array($this, 'load_localisation'), 0);

        //adding metaboxes
        add_action('add_meta_boxes', array($this, 'almasder4wordpress_addsourcemetabox'));
    }

// End __construct ()

    /**
     * Wrapper function to register a new post type
     * @param  string $post_type   Post type name
     * @param  string $plural      Post type item plural name
     * @param  string $single      Post type item single name
     * @param  string $description Description of post type
     * @return object              Post type class object
     */
    public function register_post_type($post_type = '', $plural = '', $single = '', $description = '', $options = array()) {

        if (!$post_type || !$plural || !$single)
            return;

        $post_type = new almasder4wordpress_Post_Type($post_type, $plural, $single, $description, $options);

        return $post_type;
    }

    /**
     * Wrapper function to register a new taxonomy
     * @param  string $taxonomy   Taxonomy name
     * @param  string $plural     Taxonomy single name
     * @param  string $single     Taxonomy plural name
     * @param  array  $post_types Post types to which this taxonomy applies
     * @return object             Taxonomy class object
     */
    public function register_taxonomy($taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array()) {

        if (!$taxonomy || !$plural || !$single)
            return;

        $taxonomy = new almasder4wordpress_Taxonomy($taxonomy, $plural, $single, $post_types, $taxonomy_args);

        return $taxonomy;
    }

    /**
     * Load frontend CSS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_styles() {
        wp_register_style($this->_token . '-frontend', esc_url($this->assets_url) . 'css/frontend.css', array(), $this->_version);
        wp_enqueue_style($this->_token . '-frontend');
    }

// End enqueue_styles ()

    /**
     * Load frontend Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function enqueue_scripts() {
        wp_register_script($this->_token . '-frontend', esc_url($this->assets_url) . 'js/frontend' . $this->script_suffix . '.js', array('jquery'), $this->_version);
        wp_enqueue_script($this->_token . '-frontend');
    }

// End enqueue_scripts ()

    /**
     * Load admin CSS.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_styles($hook = '') {
        wp_register_style($this->_token . '-admin', esc_url($this->assets_url) . 'css/admin.css', array(), $this->_version);
        wp_enqueue_style($this->_token . '-admin');
    }

// End admin_enqueue_styles ()

    /**
     * Load admin Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_scripts($hook = '') {
        wp_register_script($this->_token . '-admin', esc_url($this->assets_url) . 'js/admin' . $this->script_suffix . '.js', array('jquery'), $this->_version);
        wp_enqueue_script($this->_token . '-admin');
    }

// End admin_enqueue_scripts ()

    /**
     * Load plugin localisation
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_localisation() {
        load_plugin_textdomain('almasder4wordpress', false, dirname(plugin_basename($this->file)) . '/lang/');
    }

// End load_localisation ()

    /**
     * Load plugin textdomain
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain() {
        $domain = 'almasder4wordpress';

        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, dirname(plugin_basename($this->file)) . '/lang/');
    }

// End load_plugin_textdomain ()

    /**
     * Main almasder4wordpress Instance
     *
     * Ensures only one instance of almasder4wordpress is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see almasder4wordpress()
     * @return Main almasder4wordpress instance
     */
    public static function instance($file = '', $version = '1.0.0') {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

// End instance ()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone() {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

// End __clone ()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

// End __wakeup ()

    /**
     * Installation. Runs on activation.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install() {
        $this->_log_version_number();
        //setup default settings
        $this->_install_settings();
    }

// End install ()

    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number() {
        update_option($this->_token . '_version', $this->_version);
    }

// End _log_version_number ()

    /**
     * Install the plugin default settings.
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function _install_settings() {
        //getting option
        //for almasder4wordpress_active
        if (!get_option("almasder4wordpress_active")) {
            // no nothing here
            add_option("almasder4wordpress_active", "yes");
        } else {
            //your migrate stuff here
        }
        //for almasder4wordpress_linelocation
        if (!get_option("almasder4wordpress_linelocation")) {
            // no nothing here
            add_option("almasder4wordpress_linelocation", "above");
        }
        //for almasder4wordpress_linetype
        if (!get_option("almasder4wordpress_linetype")) {
            // no nothing here
            add_option("almasder4wordpress_linetype", "text");
        }
        //for lineprefix
        if (!get_option("almasder4wordpress_lineprefix")) {
            // no nothing here
            add_option("almasder4wordpress_lineprefix", "");
        }
        //for linecolor
        if (!get_option("almasder4wordpress_linecolor")) {
            // no nothing here
            add_option("almasder4wordpress_linecolor", "#AF2717");
        }
        
    }

    public function almasder4wordpress_addsourcemetabox() {
        //@ref https://developer.wordpress.org/reference/functions/add_meta_box/
        $callback_func_args = array();
        add_meta_box('almasder4wordpress-source-meta-box', __("Post/News/Article Origin", 'almasder4wordpress'), array($this, 'almasder4wordpress_origin_inner_custom_box'), 'post', 'normal', 'high', $callback_func_args);
    }

    public function almasder4wordpress_origin_inner_custom_box() {
        $almasder4wordpress_linetype = get_option("almasder4wordpress_linetype");

        $values = get_post_custom($post->ID);
        $almasder4wordpress_masdertext =  esc_attr($values['_almasder4wordpress_text'][0]);
        $almasder4wordpress_masderlink = esc_attr($values['_almasder4wordpress_link'][0]);
          
       // $text = isset( $values['my_meta_box_text'] ) ? esc_attr( $values['my_meta_box_text'][0] ) ;
        //$selected = isset( $values['my_meta_box_select'] ) ? esc_attr( $values['my_meta_box_select'][0] ) : ”;
       // $check = isset( $values['my_meta_box_check'] ) ? esc_attr( $values['my_meta_box_check'][0] ) : ”;
        ?>
        <?php
        if ($almasder4wordpress_linetype == "text" || $almasder4wordpress_linetype == "href") {
            ?>
            <p>                
                <label for="almasder4wordpress_masdertext"> <?php _e('Post Source in Text', 'almasder4wordpress'); ?> </label>
                <input  style="width:99%" name="almasder4wordpress_masdertext" id="almasder4wordpress_masdertext" value="<?php echo $almasder4wordpress_masdertext;?>" />
            </p>
            <?php
        }
        if ($almasder4wordpress_linetype == "href" || $almasder4wordpress_linetype == "link") {
            ?>
            <p>
                <label for="almasder4wordpress_masderlink"> <?php _e('Post Source Link', 'almasder4wordpress'); ?> </label>
                <input  style="width:99%" name="almasder4wordpress_masderlink" id="almasder4wordpress_masderlink" value="<?php echo $almasder4wordpress_masderlink;?>" /><br /> <?php _e("Leave link empty if you don't want to us", 'almasder4wordpress'); ?>
            </p>             
            <?php
        }
    }

}
