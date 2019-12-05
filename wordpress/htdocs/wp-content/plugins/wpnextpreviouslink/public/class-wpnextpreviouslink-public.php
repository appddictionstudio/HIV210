<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://codeboxr.com
 * @since      1.0.0
 * @package    WPNextPreviousLink
 * @subpackage WPNextPreviousLink/public
 * @author     Codeboxr <info@codeboxr.com>
 */
class WPNextPreviousLink_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    //for settings

    private $settings_api;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        $this->settings_api = new WPNextPreviousLink_Settings_API($plugin_name, $version);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_register_style('wpnextpreviouslink-public', plugin_dir_url(__FILE__) . '../assets/css/wpnextpreviouslink-public.css', array(), $this->version, 'all');
		wp_enqueue_style('wpnextpreviouslink-public');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
    	//nothing to enqueue
    }

    /**
     * To access all loader class property
     *
     * @param Loader class object $loader
     *
     * @since 1.0.0
     */
    public function set_loader($loader) {
        $this->loader = $loader;
    }
    


    /**
     * Apply next prev link on front end
     */
    public function wordPress_next_previous_link() {

        
        global $style, $post;

        $show_action = true;

        $show_action = apply_filters('wpnp_go_or_not', $show_action, $this); //this may help in may ways

        if(!$show_action) return;

        $left_image = $left_image_hover  = $right_image  = $right_image_hover = '';
        
        //set the default values to show fire in front end
        $image_name    = $this->settings_api->get_option('wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow');
        $style_top     = intval($this->settings_api->get_option('wpnp_style_top', 'wpnextpreviouslink_basics', 50));
        $unit_type     = $this->settings_api->get_option('wpnp_unit_type', 'wpnextpreviouslink_basics', '%');
        $show_home     = intval($this->settings_api->get_option('wpnp_show_home', 'wpnextpreviouslink_basics', 1));

        $show_archive  = intval($this->settings_api->get_option('wpnp_show_archive', 'wpnextpreviouslink_basics', 1));
        $show_category = intval($this->settings_api->get_option('wpnp_show_category', 'wpnextpreviouslink_basics', 1));
        $show_tag      = intval($this->settings_api->get_option('wpnp_show_tag', 'wpnextpreviouslink_basics', 1));
        $show_author   = intval($this->settings_api->get_option('wpnp_show_author', 'wpnextpreviouslink_basics', 1));
        $show_date     = intval($this->settings_api->get_option('wpnp_show_date', 'wpnextpreviouslink_basics', 1));

        $same_cat      = intval($this->settings_api->get_option('wpnp_same_cat', 'wpnextpreviouslink_basics', 0));

        //for showing different type of arrow in front end
        $left_image = plugins_url('assets/images/l_'. $this->settings_api->get_option('wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow') . '.png', dirname(__FILE__));
        $left_image = apply_filters('wpnp_showleftimg', $left_image, $image_name);

        $left_image_hover = plugins_url('assets/images/l_'. $this->settings_api->get_option('wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow') . '_hover.png', dirname(__FILE__));
        $left_image_hover = apply_filters('wpnp_showleftimg_hover', $left_image_hover, $image_name);

        $right_image = plugins_url('assets/images/r_'. $this->settings_api->get_option('wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow') . '.png', dirname(__FILE__));
        $right_image = apply_filters('wpnp_showrightimg', $right_image, $image_name);

        $right_image_hover = plugins_url('assets/images/r_'. $this->settings_api->get_option('wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow') . '_hover.png', dirname(__FILE__));
        $right_image_hover = apply_filters('wpnp_showrightimg_hover', $right_image_hover, $image_name);


        //
        $style = '<style type="text/css">
        #wpnp_previous{
                    background-image: url(' . $left_image . ') ;
                    top:' . $style_top . $unit_type . ';                   
                    }

        #wpnp_previous:hover{
                    background-image: url(' . $left_image_hover . ');
                    }

        #wpnp_next{
                    background-image: url(' . $right_image . ') ;
                    top: ' . $style_top . $unit_type . ';                   
                    }
        #wpnp_next:hover{
                    background-image: url(' . $right_image_hover . ');
                    }
        </style>';

        echo $style;

        $post_to_show       = intval($this->settings_api->get_option('wpnp_show_post','wpnextpreviouslink_basics', 1)); //show post next(2) or prev(1)
        $show_post_archive  = intval($this->settings_api->get_option('wpnp_show_post_archive','wpnextpreviouslink_basics', 1)); //show both next and prev in archive
        $show_post_single   = intval($this->settings_api->get_option('wpnp_show_post_single','wpnextpreviouslink_basics', 1)); //show both next and prev in single


        $next_posts_html ='';
        $prev_posts_html ='';

        $next_post_html ='';
        $prev_post_html ='';

        //if((is_front_page() && $show_home) || (!is_front_page() && is_singular())){
        if($this->is_page_front_page() || is_singular()){

            //any kind of single post or details post
            $next_post_html = get_next_post_link('%link', '<span id="wpnp_next"> &larr; %title</span>', $same_cat); //will return html link
            $prev_post_html = get_previous_post_link('%link', '<span id="wpnp_previous"> &larr; %title</span>', $same_cat); // will return html link
        }
        else{
            //mean archive
            $prev_posts_html = get_previous_posts_link(__('<span id="wpnp_previous">&rarr;</span>', 'wpnextpreviouslink'));
            $next_posts_html = get_next_posts_link(__('<span id="wpnp_next">&larr;</span>', 'wpnextpreviouslink'));
        }




        //for details
        if (is_singular()) {
            $post_types_to_show = $this->settings_api->get_option('wpnp_show_posttypes','wpnextpreviouslink_basics',array('post','page','attachment'));

            //if show both next prev for single
            if($show_post_single){
                if(!in_array($post->post_type,$post_types_to_show)){
                    return;
                }

                //show prev if exists
                if($prev_post_html != NULL){
                    echo $prev_post_html;
                }


               //show next if exits
                if($next_post_html != NULL){
                    echo $next_post_html;
                }

            }
            else{
            	//show either prev and next for singular post

                if($post_to_show == 1){ //show prev
                    if(!in_array($post->post_type, $post_types_to_show)){
                        return;
                    }

                    if($prev_post_html != NULL){
                        echo $prev_post_html;
                    }


                }elseif($post_to_show == 2){//show next
                    if(!in_array($post->post_type, $post_types_to_show)){
                        return;
                    }


                    if($next_post_html != NULL){
                        echo $next_post_html;
                    }

                }//for future implementation of more options
            }

        } //end singular post
        elseif (is_home() || is_front_page()) {

            //archive
            $show = true;


            if (!$show_home) {
                $show = false;
            }

            if ($show) {




                //if show both next prev for archive
                if($show_post_archive){

                    if($prev_posts_html != NULL){
                        echo $prev_posts_html;
                    }

                    if($next_posts_html != NULL){
                        echo $next_posts_html;
                    }

                }
                else{
					//show either prev and next for archive
                    if($post_to_show == 1){ //show prev

                        if($prev_posts_html != NULL){
                            echo $prev_posts_html;
                        }

                    }elseif($post_to_show == 2){ //show next

                        if($next_posts_html != NULL){
                            echo $next_posts_html;
                        }
                    }
                }
            }
        } else {
            //archive
            $show = true;
            if ((!$show_archive) || (!$show_category && is_category()) || (!$show_tag  && is_tag()) || (!$show_author && is_author()) || (!$show_date && is_date())) {
                $show = false;
            }
            if ($show) {

                //if in archive any one wants to show both next and prev
                if($show_post_archive){

                	//prev
                    if($prev_posts_html != NULL){
                        echo $prev_posts_html;
                    }

                    //next
                    if($next_posts_html != NULL){
                        echo $next_posts_html;
                    }
                }
                else{
                    if($post_to_show == 1){ //prev

                        if($prev_posts_html != NULL){
                            echo $prev_posts_html;
                        }

                    }elseif($post_to_show == 2){ //next
                        if($next_posts_html != NULL){
                            echo $next_posts_html;
                        }
                    }
                }

            }
        }
    }

	/**
	 * Is the frontpage a page ?
	 *
	 * @return bool
	 */
    public function is_page_front_page(){
    	if('page' == get_option( 'show_on_front') && get_option( 'page_on_front' ) && is_page( get_option( 'page_on_front' ) )) return true;
    	return false;
    }
}
