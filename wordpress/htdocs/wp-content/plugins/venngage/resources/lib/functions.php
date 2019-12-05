<?php
/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB directory)
 *
 * Be sure to replace all instances of "fdt_" with your project"s prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . "/cmb2/init.php" ) ) {
	require_once dirname( __FILE__ ) . "/cmb2/init.php";
} elseif ( file_exists( dirname( __FILE__ ) . "/CMB2/init.php" ) ) {
	require_once dirname( __FILE__ ) . "/CMB2/init.php";
}

add_action( "cmb2_init", "venngage_custom_post" );
/**
 * Hook in and add a demo metabox. Can only happen on the "cmb2_init" hook.
 */
function venngage_custom_post() {
	// Start with an underscore to hide fields from custom fields list
	$prefix = __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__;

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb_demo = new_cmb2_box( array(
		"id"            => $prefix . "attributes",
		"title"         => __( "Hotel Attributes", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__ ),
		"object_types"  => array("post", "page"), // Post type
		"context"       => "side",
		"priority"      => "high",
		"show_names"    => false, // Show field names on the left
		"cmb_styles" => false, // false to disable the CMB stylesheet
		"closed"     => true, // true to keep the metabox closed by default
	) );

	$cmb_demo->add_field( array(
		"id"   => $prefix . "embed",
		"type" => "spl_fields",
	) );

}

add_action("cmb2_render_spl_fields", "venngage_spl_fields", 10, 5);

function venngage_spl_fields($field, $escaped_value, $object_id, $object_type, $field_type_object){
    require_once __VENNGAGE_INFOGRAPHICS_DIR__ . "venngage.php";
    include_once __VENNGAGE_INFOGRAPHICS_DIR__ . "resources/admin/includes/footer.php";
}
