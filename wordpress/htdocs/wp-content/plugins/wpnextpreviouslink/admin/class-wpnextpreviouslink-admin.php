<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://codeboxr.com
 * @since      1.0.0
 * @package    WPNextPreviousLink
 * @subpackage WPNextPreviousLink/admin
 * @author     Codeboxr <info@codeboxr.com>
 */
class WPNextPreviousLink_Admin {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wpnextpreviouslink_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * Slug of the plugin screen.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix_settings = null;

    /**
     * The settings ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $wpnextpreviouslink The ID of this plugin.
     */
    private $wpnextpreviouslink;


    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * The basename of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_basename    The basename of this plugin.
     */
    private $plugin_basename;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    //for setting
    private $settings_api;

    /**
     * The plugin name of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The plugin name of the plugin.
     */
    protected $plugin_name;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->wpnextpreviouslink 	= $plugin_name;
        $this->plugin_name        	= $plugin_name;
        $this->plugin_basename    	= plugin_basename(plugin_dir_path(__DIR__) . $this->wpnextpreviouslink . '.php');
        $this->version            	= $version;
        $this->settings_api 		= new WPNextPreviousLink_Settings_API($plugin_name, $version);
    }//end of constructor

	/**
	 * Init setting
	 */
    public function setting_init() {
        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());
        //initialize settings
        $this->settings_api->admin_init();
    }//end method setting_init

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
	    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	    $current_page = isset( $_GET['page'] ) ? esc_attr( $_GET['page'] ) : '';

	    if($current_page == 'wpnextpreviouslink'){
	    	wp_register_style('chosen', plugin_dir_url(__FILE__) . '../assets/js/chosen.min.css', array(), $this->version, 'all');
	    	wp_register_style('wpnextpreviouslink-setting', plugin_dir_url(__FILE__) . '../assets/css/wpnextpreviouslink-setting.css', array('chosen'), $this->version, 'all');


		    wp_enqueue_style('wp-color-picker');
	    	wp_enqueue_style('chosen');
	    	wp_enqueue_style('wpnextpreviouslink-setting');
	    }
    }//end method enqueue_styles

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

	    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	    $current_page = isset( $_GET['page'] ) ? esc_attr( $_GET['page'] ) : '';


        //register the admin.js
	    wp_register_script('chosen', plugin_dir_url(__FILE__) . '../assets/js/chosen.jquery.min.js', array('jquery'), $this->version, true);
	    wp_register_script('wpnextpreviouslink-setting', plugin_dir_url(__FILE__) . '../assets/js/wpnextpreviouslink-setting.js', array('jquery', 'chosen'), $this->version, true);

        $translation_array = array(
            'image_url' => plugins_url('assets/images/', dirname(__FILE__))
        );
        wp_localize_script('wpnextpreviouslink-setting', 'wpnp', $translation_array);

        //enqueue admin.js wpnextpreviouslink settings page
	    if($current_page == 'wpnextpreviouslink'){
		    wp_enqueue_script('jquery');
		    wp_enqueue_media();

		    wp_enqueue_script('chosen');
		    wp_enqueue_script('wp-color-picker');
		    wp_enqueue_script('wpnextpreviouslink-setting');
	    }
    }//end method enqueue_scripts

    /**
     * Get plugin basename
     *
     * @since     1.0.0
     * @return    string    The basename of the plugin.
     */
    public function get_plugin_basename() {
        return $this->plugin_basename;
    }//end method get_plugin_basename

    /**
     * To access all loader class property
     *
     * @param Loader class object $loader
     *
     * @since 1.0.0
     */
    public function set_loader($loader) {
        $this->loader = $loader;
    }//end method set_loader

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {

        //setting menu
        $this->plugin_screen_hook_suffix_settings = add_options_page(
                esc_html__('CBX Next Previous Setting', 'wpnextpreviouslink'), esc_html__('CBX Next Previous', 'wpnextpreviouslink'), 'manage_options', 'wpnextpreviouslink', array($this, 'display_plugin_admin_settings')
        );
    }//end method add_plugin_admin_menu

    /**
     * Run all administrator program from here
     *
     * @since   1.0.0
     */
    public function run() {
        $this->loader->add_action('admin_menu', $this, 'add_plugin_admin_menu');
    }

    /**
     * Get The Available Arrow type for the link
     * 
     * @return array
     */
    public function get_wpnextprevios_arrow_type() {

        $arrow_types = array(
            'arrow'        => esc_html__('Classic', 'wpnextpreviouslink'),
            'arrow_blue'   => esc_html__('Blue', 'wpnextpreviouslink'),
            'arrow_dark'   => esc_html__('Dark', 'wpnextpreviouslink'),
            'arrow_green'  => esc_html__('Green', 'wpnextpreviouslink'),
            'arrow_orange' => esc_html__('Orange', 'wpnextpreviouslink'),
            'arrow_red'    => esc_html__('Red', 'wpnextpreviouslink')
        );

        return apply_filters('wpnp_arrow_options', $arrow_types);
    }//end method get_wpnextprevios_arrow_type
    
    /**
     * Get The Available Image type for the link
     * 
     * @return array
     */
    public function get_wpnextprevios_image_type() {

        $image_types = array('0' => 'Arrow');

        return apply_filters('wpnp_image_options', $image_types);
    }//end method get_wpnextprevios_image_type
    
   
    /**
     * Set settings fields
     * 
     * @return type array
     */
    public function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'wpnextpreviouslink_basics',
                'title' => esc_html__('Plugin Options', 'wpnextpreviouslink')
            )
        );

        $sections = apply_filters('wpnp_setting_sections', $sections);

        return $sections;
    }

    /**
     * Return the key value pair of posttypes
     * @param type array $all_post_types
     * @return type array
     */
    public function get_formatted_posttype_multicheckbox($all_post_types) {

        $posts_defination = array();

        foreach ($all_post_types as $key => $post_type_defination) {
            foreach ($post_type_defination as $post_type_type => $data) {
                if ($post_type_type == 'label') {
                    $opt_grouplabel = $data;
                }

                if ($post_type_type == 'types') {
                    foreach ($data as $opt_key => $opt_val) {
                        $posts_defination[$opt_grouplabel][$opt_key] = $opt_val;
                    }
                }
            }
        }

        return $posts_defination;
    }//end method get_formatted_posttype_multicheckbox

    /**
     * Get All(default and custom) Post types
     * @return type
     */
    public function wpnp_post_types() {
        $output    = 'objects'; // names or objects, note names is the default
        $operator  = 'and'; // 'and' or 'or'
        $postTypes = array();

        $post_type_args = array(
            'builtin' => array(
                'options' => array(
                    'public'   => true,
                    '_builtin' => true,
                    'show_ui'  => true,
                ),
                'label'   => esc_html__('Built in post types', 'wpnextpreviouslink'),
            )
        );

        $post_type_args = apply_filters('wpnp_supported_posttypes',$post_type_args);

        foreach ($post_type_args as $postArgType => $postArgTypeArr) {
            $types = get_post_types($postArgTypeArr['options'], $output, $operator);

            if (!empty($types)) {
                foreach ($types as $type) {
                    $postTypes[$postArgType]['label']              = $postArgTypeArr['label'];
                    $postTypes[$postArgType]['types'][$type->name] = $type->labels->name;
                }
            }
        }

        return $postTypes;
    }//end method wpnp_post_types

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields() {
        //get default values for basic settings

        $wpnp_arrow_type_options = $wpnp_image_type_options = array();
        $wpnp_saved_image_type = $this->settings_api->get_option('wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow');
        $posts_defination = $this->get_formatted_posttype_multicheckbox($this->wpnp_post_types());//get supported post types


        $wpnp_post_to_show = apply_filters('wpnp_post_to_show', array(
        	'1' => esc_html__('Previous', 'wpnextpreviouslink'),
	        '2' => esc_html__('Next', 'wpnextpreviouslink')
        ));//which post to show

        $wpnp_arrow_types = $this->get_wpnextprevios_arrow_type();//get supported arrow types
        $wpnp_image_types = $this->get_wpnextprevios_image_type();//get supported image types
        //arrange options for arrow types
        foreach ($wpnp_arrow_types as $key => $value) {
            $wpnp_arrow_type_options[$key] = $value;
        }
        //arrange options for image types
        foreach ($wpnp_image_types as $key => $value) {
            $wpnp_image_type_options[$key] = $value;
        }



        $wpnp_link_img_src_p = plugins_url('assets/images/l_'. $this->settings_api->get_option('wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow') . '.png', dirname(__FILE__));
        $wpnp_link_img_src_n = plugins_url('assets/images/r_'. $this->settings_api->get_option('wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow') . '.png', dirname(__FILE__));

		$wpnp_link_img_src_p = apply_filters('wpnp_showleftimg', $wpnp_link_img_src_p, $wpnp_saved_image_type);
		$wpnp_link_img_src_n = apply_filters('wpnp_showrightimg', $wpnp_link_img_src_n, $wpnp_saved_image_type);


        $settings_fields = array(
            'wpnextpreviouslink_basics' => array(
                array(
                    'name'     => 'wpnp_style_top',
                    'label'    => esc_html__('Vertical Position', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Vertical position of arrow or thumb', 'wpnextpreviouslink'),
                    'type'     => 'number',
                    'default'  => 50,
                    'size'     => '20',
                    'desc_tip' => true,
                ),
                array(
                    'name'     => 'wpnp_unit_type',
                    'label'    => esc_html__('Vertical Position Type', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Vertical position type px or %', 'wpnextpreviouslink'),
                    'type'     => 'select',
                    'default'  => '%',
                    'options'  => array('%' => '%', 'px' => 'px'),
                    'desc_tip' => true,
                ),
                array(
                    'name'     => 'wpnp_show_home',
                    'label'    => esc_html__('Show in Home page', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Show in home page or not. Default "Yes"', 'wpnextpreviouslink'),
                    'type'     => 'radio',
                    'default'  => 1,
                    'options'  => array('0' => esc_html__('No','wpnextpreviouslink'),  '1' => esc_html__('Yes','wpnextpreviouslink')),
                    'desc_tip' => true,
                ),
                array(
                    'name'     => 'wpnp_show_archive',
                    'label'    => esc_html__('Show in Archive View (Category, Tag, Author, Date)', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Show in Archive View or not. Default "Yes"', 'wpnextpreviouslink'),
                    'type'     => 'radio',
                    'default'  => 1,
                    'options'  => array('0' => esc_html__('No','wpnextpreviouslink'),  '1' => esc_html__('Yes','wpnextpreviouslink')),
                    'desc_tip' => true,
                ),
                array(
                    'name'     => 'wpnp_show_category',
                    'label'    => esc_html__('Show in Category View', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Show in Category View or not. Default "Yes".', 'wpnextpreviouslink'),
                    'type'     => 'radio',
                    'default'  => 1,
                    'options'  => array('0' => esc_html__('No','wpnextpreviouslink'),  '1' => esc_html__('Yes','wpnextpreviouslink')),
                    'desc_tip' => true,
                ),
                array(
                    'name'     => 'wpnp_show_tag',
                    'label'    => esc_html__('Show in Tag View', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Show in Tag View or not. Default "Yes"', 'wpnextpreviouslink'),
                    'type'     => 'radio',
                    'default'  => 1,
                    'options'  => array('0' => esc_html__('No','wpnextpreviouslink'),  '1' => esc_html__('Yes','wpnextpreviouslink')),
                    'desc_tip' => true,
                ),
                array(
                    'name'     => 'wpnp_show_author',
                    'label'    => esc_html__('Show in Author View', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Show in Author View or not. Default "Yes"', 'wpnextpreviouslink'),
                    'type'     => 'radio',
                    'default'  => 1,
                    'options'  => array('0' => esc_html__('No','wpnextpreviouslink'),  '1' => esc_html__('Yes','wpnextpreviouslink')),
                    'desc_tip' => true,
                ),
                array(
                    'name'     => 'wpnp_show_date',
                    'label'    => esc_html__('Show in Date View', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Show in Date View or not. Default "Yes"', 'wpnextpreviouslink'),
                    'type'     => 'radio',
                    'default'  => 1,
                    'options'  => array('0' => esc_html__('No','wpnextpreviouslink'),  '1' => esc_html__('Yes','wpnextpreviouslink')),
                    'desc_tip' => true,
                ),
                array(
                    'name'     => 'wpnp_same_cat',
                    'label'    => esc_html__('Navigate by Category', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Navigate by Category or not. Default "No". This feature works for only regular "post" type or "category" taxonomy.', 'wpnextpreviouslink'),
                    'type'     => 'radio',
                    'default'  => 0,
                    'options'  => array('0' => esc_html__('No','wpnextpreviouslink'),  '1' => esc_html__('Yes','wpnextpreviouslink')),
                    'desc_tip' => true,
                ),
                array(
                    'name'    => 'wpnp_show_posttypes',
                    'label'   => esc_html__('Post Type Selection', 'wpnextpreviouslink'),
                    'desc'    => esc_html__('Post Type Selection', 'wpnextpreviouslink'),
                    'type'    => 'multiselect',
                    'default' => array('post','page'),
                    'options' => $posts_defination
                ),
                array(
                    'name'  => 'wpnp_show_post',
                    'label' => esc_html__('Show Post', 'wpnextpreviouslink'),
                    'desc'  => esc_html__('Show which post to be appeared', 'wpnextpreviouslink'),
                    'type'  => 'radio',
                    'default'=> 1,
                    'options'=> $wpnp_post_to_show
                ),
                array(
                    'name'  => 'wpnp_show_post_archive', //this variable name is very confusing
                    'label' => esc_html__('Show Both in Archive for Arrow', 'wpnextpreviouslink'),
                    'desc'  => esc_html__('Show both left and right for any archive mode for arrow style', 'wpnextpreviouslink'),
                    'type'  => 'radio',
                    'default'=> 1,
                    'options'  => array('0' => esc_html__('No','wpnextpreviouslink'),  '1' => esc_html__('Yes','wpnextpreviouslink')),
                ),
                array(
                    'name'  => 'wpnp_show_post_single', //same for this variable
                    'label' => esc_html__('Show Both in Single for Arrow', 'wpnextpreviouslink'),
                    'desc'  => esc_html__('Show both left and right for any details mode for arrow style', 'wpnextpreviouslink'),
                    'type'  => 'radio',
                    'default'=> 1,
                    'options'  => array('0' => esc_html__('No','wpnextpreviouslink'),  '1' => esc_html__('Yes','wpnextpreviouslink')),
                ),
                array(
                    'name'     => 'wpnp_arrow_type',
                    'label'    => esc_html__('Image Type', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Type of the image', 'wpnextpreviouslink'),
                    'type'     => 'radio',
                    'default'  => '0',
                    'options'  => $wpnp_image_type_options,
                    'desc_tip' => true,
                ),
                array(
                    'name'     => 'wpnp_image_name',
                    'label'    => esc_html__('Arrow Style', 'wpnextpreviouslink'),
                    'desc'     => esc_html__('Arrow style of the next prev link.', 'wpnextpreviouslink'),
                    'type'     => 'select',
                    'size'     => 'wpnp_image_name',
                    'default'  => 'arrow',
                    'options'  => $wpnp_arrow_type_options,
                    'desc_tip' => true,
                ),
                array(
                    'name'  => 'wpnp_display_image',
                    'label' => esc_html__('Arrow Preview', 'wpnextpreviouslink'),
                    'desc'  => '<div style="margin-top:10px;" id="wpnp_next_previous" >
                                         <img style="width: 32px; height: 60px;" id="wpnp_previousimg" src="' . $wpnp_link_img_src_p . '" alt="'.esc_html__('Prev Preview Image(Width: 32px, Height: 60px)', 'wpnextpreviouslink').' "/>
                                         <img style="width: 32px; height: 60px; margin-left: 50px;" id="wpnp_nextimg" src="' . $wpnp_link_img_src_n . '" alt="'.esc_html__('Next Preview Image(Width: 32px, Height: 60px)', 'wpnextpreviouslink').' "/>
                                </div>',
                    'type'  => 'info',
                ),
            )
        );

        $settings_fields = apply_filters('wpnp_setting_fields', $settings_fields);

        return $settings_fields;
    }//end method get_settings_fields

    /**
     * Display options page of this plugin
     * @global type $wpdb
     * 
     */
    public function display_plugin_admin_settings() {
        global $wpdb;

        $plugin_data = get_plugin_data(plugin_dir_path(__DIR__) . '/../' . $this->plugin_basename);

        include('partials/admin-settings-display.php');
    }//end method display_plugin_admin_settings

    /**
     * Show row meta on the plugin screen.
     *
     * @param	mixed $links Plugin Row Meta
     * @param	mixed $file  Plugin Base file
     * @return	array
     */
    public function add_plugin_row_meta( $links, $file ) {
        if ( $file == $this->plugin_basename ) {
            $row_meta = array(
                'apidocs' => '<a target="_blank" href="https://codeboxr.com/product/show-next-previous-article-for-wordpress" title="' . esc_attr( esc_html__( 'Pro Addon', 'wpnextpreviouslink' ) ) . '">' . __( 'Pro Addon', 'wpnextpreviouslink' ) . '</a>',
                'support' => '<a target="_blank" href="https://codeboxr.com/contact-us" title="' . esc_attr( esc_html__( 'Visit Premium Customer Support Forum', 'wpnextpreviouslink' ) ) . '">' . esc_html__( 'Premium Support', 'wpnextpreviouslink' ) . '</a>',
            );

            return array_merge( $links, $row_meta );
        }

        return (array) $links;
    }//end method add_plugin_row_meta

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links)
    {

        return array_merge(
            array(
                'settings' => '<a href="' . admin_url('options-general.php?page=wpnextpreviouslink') . '">' . esc_html__('Settings', 'wpnextpreviouslink') . '</a>'
            ),
            $links
        );

    }//end of function add_action_links

}//end class WPNextPreviousLink_Admin
