<?php
/**
* Plugin Name: Venngage
* Plugin URI: http://venngage.com
* Description: Create and embed your Venngage infographics, charts and data visualizations into your WordPress site
* Version: 1.0.0
* Author: Venngage
* Author URI: http://venngage.com
* License: GPL2
*/
/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define("__VENNGAGE_INFOGRAPHICS_PLUGIN_NAME__", "Venngage");
define("__VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__", "__venngage_");
define( "__VENNGAGE_INFOGRAPHICS_VERSION__", 1.0);
define( "__VENNGAGE_INFOGRAPHICS_DIR__", trailingslashit(plugin_dir_path(__FILE__)));
define( "__VENNGAGE_INFOGRAPHICS_URL__", plugin_dir_url(__FILE__));
define( "__VENNGAGE_INFOGRAPHICS_ROOT__", trailingslashit(plugins_url("", __FILE__)));
define( "__VENNGAGE_INFOGRAPHICS_RESOURCES__", __VENNGAGE_INFOGRAPHICS_ROOT__ . "resources/" );
define( "__VENNGAGE_INFOGRAPHICS_IMAGES__", __VENNGAGE_INFOGRAPHICS_RESOURCES__ . "images/" );
define( "__VENNGAGE_INFOGRAPHICS_AJAX__", admin_url("admin-ajax.php?action=" . __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__));
define("__VENNGAGE_INFOGRAPHICS_DEBUG__", false);
define("__VENNGAGE_INFOGRAPHICS_TEST__", false);
define("__VENNGAGE_INFOGRAPHICS_STAGING__", false);

if(__VENNGAGE_INFOGRAPHICS_DEBUG__){
    @error_reporting(E_ALL);
    @ini_set("display_errors", "1");
}

/**
 * Abort loading if WordPress is upgrading
 */
if (defined("WP_INSTALLING") && WP_INSTALLING) return;

class Venngage{

    private $error;
    private $notice;

    public function __construct(){
        // all hooks and actions
        add_action("init", array($this, "venngage_register"));
        register_activation_hook( __FILE__ , array($this, "venngage_activate"));
        register_deactivation_hook( __FILE__ , array($this, "venngage_deactivate"));
        add_action("wp_enqueue_scripts", array($this, "venngage_includeResources"));
        add_action("plugins_loaded", array($this, "venngage_i18n"));
        add_action("plugins_loaded", array($this, "venngage_i18n"));
	    add_shortcode("infographic", array($this, "venngage_shortcode"));

        add_action("admin_enqueue_scripts", array($this, "venngage_includeResources"));
        add_action("admin_init", array($this, "venngage_add_editor_button"));
        add_action("save_post", array($this, "venngage_save_post"));

        add_action("wp_ajax_" . __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__, array($this, "ajax"));
    }


    /**
     * Initializes the locale
     */
    function venngage_i18n(){
        $pluginDirName  = dirname( plugin_basename( __FILE__ ) );
        $domain         = __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__;
        $locale         = apply_filters("plugin_locale", get_locale(), $domain);
        load_textdomain($domain, WP_LANG_DIR . "/" . $pluginDirName . "/" . $domain . "-" . $locale . ".mo");
        load_plugin_textdomain( $domain, "", $pluginDirName . "/resources/lang/" );
    }

    /**
     * Loads the JS and CSS resources
     */
    function venngage_includeResources() {
        wp_enqueue_script("jquery");

        wp_register_script("vi", __VENNGAGE_INFOGRAPHICS_RESOURCES__ . "js/vi.js");
        wp_enqueue_script("vi");

        wp_register_style("vi", __VENNGAGE_INFOGRAPHICS_RESOURCES__ . "css/vi.css");
        wp_enqueue_style("vi");
    }

    /**
     * Register the plugin
     */
    function venngage_register(){
        require_once __VENNGAGE_INFOGRAPHICS_DIR__ . "resources/lib/functions.php";
    }

    /**
     * Activate the plugin
     */
    function venngage_activate(){
        // do nothing
    }

    /**
     * Deactivate the plugin
     */
    function venngage_deactivate(){
        if(__VENNGAGE_INFOGRAPHICS_TEST__ || __VENNGAGE_INFOGRAPHICS_STAGING__){
            define("WP_UNINSTALL_PLUGIN", true);
            include_once __VENNGAGE_INFOGRAPHICS_DIR__ . "uninstall.php";
        }
    }

    /**
    * Start adding the infographics button to tinymce
    */
    public function venngage_add_editor_button(){
		global $wp_version;
        if($wp_version < 3.8) return;

        if(current_user_can("edit_posts") && current_user_can("edit_pages") && 'true' == get_user_option("rich_editing")){
            add_filter("mce_external_plugins", array($this, "venngage_add_buttons"));
            add_filter("mce_buttons", array($this, "venngage_register_buttons"));
            add_filter("mce_css", array( $this, "venngage_register_css" ) );
        }
    }

    /**
    * Adds the infographics button related CSS to tinymce
    */
	function venngage_register_css( $mce_css ) {
		// If the site has other css, add a comma
		if ( ! empty( $mce_css ) )
			$mce_css .= ',';

		// Add playbuzz TinyMCE editor css
		$mce_css .= __VENNGAGE_INFOGRAPHICS_RESOURCES__ . "css/tinymce.css";

		// Return the css list
		return $mce_css;

	}

    /**
    * Adds the infographics button to tinymce
    */
    public function venngage_add_buttons($plugin_array){
        $file       = "js/shortcode-tinymce-button.js";
        $version    = floatval(get_bloginfo("version"));
        if($version <= 3.8){
            $file   = "js/shortcode-tinymce-button-3.8.js";
        }
        $plugin_array["venngageshortcode"] = __VENNGAGE_INFOGRAPHICS_RESOURCES__ . $file;
        return $plugin_array;
    }

    /**
    * Registers the infographics button on tinymce
    */
    public function venngage_register_buttons($buttons){
        array_push($buttons, "separator", "venngageshortcode");
        return $buttons;
    }

    /**
    * Processes the shortcode
    */
	public function venngage_shortcode($atts, $content=NULL){
        global $post;

        $array      = self::getPostMeta($post->ID, "embedurl");
        $id         = $atts["id"];
        $url        = @$array[$id];
        ob_start();
        include __VENNGAGE_INFOGRAPHICS_DIR__ . "resources/public/shortcode.php";
        return ob_get_clean();
    }

    /**
    * Hook to save the embed code if present in the request
    */
    public function venngage_save_post($postID){
        if(!isset($_POST["embedcode"])) return;

        $array      = array();
        foreach($_POST["embedcode"] as $code){
            $arr    = explode("||", $code);
            $array[$arr[0]] = $arr[1];
        }
        self::setPostMeta($postID, "embedurl", $array);
    }

    /**
    * Save embeds immediately, without waiting for a publish event
    */
    public function ajax(){
        $action         = $_POST["_action"];
        switch($action){
            case "validate":
                $url        = filter_var($_POST["url"], FILTER_SANITIZE_URL);
                if(
                    filter_var($url, FILTER_VALIDATE_URL) === false
                    || strpos($_POST["url"], "https://infograph.venngage.com") > 0
                ){
                    echo json_encode(array("error" => __("Invalid URL", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__)));
                }
                break;
            case "update":
                $url        = self::getPostMeta($postID, "embedurl");
                $url[$_POST["id"]]    = $_POST["val"];
                self::setPostMeta($postID, "embedurl", $url);
                break;
        }
        die();
    }

    /****************************************** Util functions ******************************************/

    /**
     * Writes to the file /tmp/log.log if DEBUG is on
     */
    public static function writeDebug($msg){
        if(__VENNGAGE_INFOGRAPHICS_DEBUG__) file_put_contents(__VENNGAGE_INFOGRAPHICS_DIR__ . "tmp/log.log", date("F j, Y H:i:s") . " - " . $msg."\n", FILE_APPEND);
    }

    /**
     * Custom wrapper for the get_option function
     *
     * @return string
     */
    public static function getOption($field, $clean=false){
        $val = get_option(__VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__ . $field);
        return $clean ? htmlspecialchars($val) : $val;
    }

    /**
     * Custom wrapper for the update_option function
     *
     * @return mixed
     */
    public static function setOption($field, $value){
        return update_option(__VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__ . $field, $value);
    }

    /**
     * Custom wrapper for the get_post_meta function
     *
     * @return mixed
     */
    public static function getPostMeta($postID, $name, $single=true){
        return get_post_meta($postID, __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__ . $name, $single);
    }

    /**
     * Custom wrapper for the update_post_meta function
     */
    public static function setPostMeta($postID, $name, $value){
        update_post_meta($postID, __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__ . $name, $value);
    }

}

$venngage = new Venngage();
