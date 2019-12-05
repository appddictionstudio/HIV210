<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       http://codeboxr.com
 * @since      1.0.0
 * @package    WPNextPreviousLink
 * @subpackage WPNextPreviousLink/includes
 * @author     Codeboxr <info@codeboxr.com>
 */
class WPNextPreviousLink {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wpnextpreviouslink_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $wpnextpreviouslink;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    private $settings_api;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = WPNEXTPREVIOUSLINK_PLUGIN_NAME;
        $this->version     = WPNEXTPREVIOUSLINK_VERSION;

        $this->load_dependencies();
        $this->set_locale();

        $this->settings_api = new WPNextPreviousLink_Settings_API($this->plugin_name, $this->version);

        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Wpnextpreviouslink_Loader. Orchestrates the hooks of the plugin.
     * - Wpnextpreviouslink_i18n. Defines internationalization functionality.
     * - Wpnextpreviouslink_Admin. Defines all hooks for the admin area.
     * - Wpnextpreviouslink_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wpnextpreviouslink-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wpnextpreviouslink-i18n.php';

        /**
         * The class responsible for defining settings functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wpnextpreviouslink-setting.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wpnextpreviouslink-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wpnextpreviouslink-public.php';

        
        $this->loader = new Wpnextpreviouslink_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Wpnextpreviouslink_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Wpnextpreviouslink_i18n();
        $plugin_i18n->set_domain($this->get_wpnextpreviouslink());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Wpnextpreviouslink_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        //adding the setting action
        $this->loader->add_action('admin_init', $plugin_admin, 'setting_init');
        
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

        $this->loader->add_action('plugin_row_meta', $plugin_admin, 'add_plugin_row_meta', 10, 2 );
        //add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );

        // add settings link
        $this->loader->add_filter('plugin_action_links_wpnextpreviouslink/wpnextpreviouslink.php', $plugin_admin, 'add_action_links');
        
        $plugin_admin->set_loader($this->loader);


        //$plugin_admin->run();
    }



    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Wpnextpreviouslink_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('wp_footer', $plugin_public, 'wordPress_next_previous_link');
        
        $plugin_public->set_loader($this->loader);

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }
    
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_wpnextpreviouslink() {
        return $this->wpnextpreviouslink;
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Wpnextpreviouslink_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
