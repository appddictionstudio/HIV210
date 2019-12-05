<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',            // Scripts and stylesheets
  'lib/extras.php',            // Custom functions
  'lib/setup.php',             // Theme setup
  'lib/titles.php',            // Page titles
  'lib/wrapper.php',           // Theme wrapper class
  'lib/customizer.php',        // Theme customizer
  'vendor/autoload.php',       // Autoload Composer Files
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

function change_logo($html)
{
  $html = str_replace('class="custom-logo"', 'img-fluid', $html);
  $html = str_replace('class="custom-logo-link"', 'navbar-brand', $html);
  return $html;

}
add_filter('get_custom_logo', 'change_logo');

add_theme_support('post-thumbnails');

/**
 * Creates the custom post type 'agenda'.
 *
 * @return void
 */
function create_post_type_agenda()
{
  $labels = [
    'name'               => 'Agendas',
		'singular_name'      => 'Agenda',
		'menu_name'          => 'Agendas',
		'name_admin_bar'     => 'Agenda',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Agenda',
		'new_item'           => 'New Agenda',
		'edit_item'          => 'Edit Agenda',
		'view_item'          => 'View Agenda',
    'all_items'          => 'All Agendas',
    'attributes'         => 'Agenda Attributes',
		'search_items'       => 'Search Agendas',
		'parent_item_colon'  => 'Parent Agendas:',
		'not_found'          => 'No Agendas found.',
		'not_found_in_trash' => 'No Agendas found in Trash.'
  ];

  $args = [
    'public'       => true,
    'labels'       => $labels,
    'description'  => 'Custom post type for agendas.',
    'menu_icon'    => 'dashicons-editor-ul',
    'show_in_rest' =>  true
  ];

  register_post_type('agenda', $args);
}
add_action('init', 'create_post_type_agenda');

/**
 * Creates the custom post type 'publication'.
 *
 * @return void
 */
function create_post_type_publication()
{
  $labels = [
    'name' => 'Publications',
    'singular_name' => 'Publication',
    'menu_name' => 'Publications',
    'name_admin_bar' => 'Publication',
    'add_new' => 'Add New',
    'add_new_item' => 'Add New Publication',
    'new_item' => 'New Publication',
    'edit_item' => 'Edit Publication',
    'view_item' => 'View Publication',
    'all_items' => 'All Publications',
    'attributes' => 'Publication Attributes',
    'search_items' => 'Search Publications',
    'parent_item_colon' => 'Parent Publications:',
    'not_found' => 'No Publications found.',
    'not_found_in_trash' => 'No Publications found in Trash.'
  ];

  $args = [
    'public' => true,
    'labels' => $labels,
    'description' => 'Custom post type for publications.',
    'menu_icon' => 'dashicons-screenoptions'
  ];

  register_post_type('publication', $args);
}
add_action('init', 'create_post_type_publication');

/**
 * Creates the custom taxonomy 'publication category'.
 * 
 * @return void
 */
function register_taxonomy_publication_category()
{
  $labels = [
    'name'          => 'Publication Categories',
    'singular_name' => 'Publication Category',
    'search_items'  => 'Search Publication Categories',
    'all_items'     => 'All Publication Categories',
    'edit_item'     => 'Edit Publication Category',
    'update_item'   => 'Update Publication Category',
    'add_new_item'  => 'Add New Publication Category',
    'new_item_name' => 'New Publication Category',
    'menu_name'     => 'Publication Categories'
  ];

  $args = [
    'hierarchical'      => true,
    'labels'            => $labels,
    'query_var'         => true,
    'show_admin_column' => true
  ];

  register_taxonomy('publication_category', 'publication', $args);
}
add_action('init', 'register_taxonomy_publication_category');

/**
 * Auto generates a title based on specified fields
 * 
 * @return void
 */
function auto_generate_post_title($post_id)
{
  $post_type  = get_post_type($post_id);
  $post       = get_post($post_id);

  // Bail if post is not an agenda or a publication
  if ($post_type != 'agenda' && $post_type != 'publication') {
    return;
  }

  // Do this if post is an agenda
  if ($post_type == 'agenda') {
    $post_cat  = $post->agenda_category;
    $post_date = $post->agenda_date;
    
    $post_title = "{$post_cat}_{$post_date}";
  }

  // Do this if post is a publication
  if ($post_type == 'publication') {
    $pub_title  = $post->publication_title;
    $post_title = $pub_title;
  }
  
  // Unhook this function so it doesn't loop infinitely
  remove_action('save_post', 'auto_generate_post_title');

  // Update the post, which calls save_post again
  wp_update_post(['ID' => $post_id, 'post_title' => $post_title]);

  // Re-hook this function
  add_action('save_post', 'auto_generate_post_title');
}
add_filter('save_post', 'auto_generate_post_title');

/**
 * Hides the title input on the agenda dashboard
 * 
 * @return void
 */
function hide_wp_title_input()
{
  $screen = get_current_screen();

  if (($screen->id != 'agenda') && ($screen->id != 'publication')) {
    return;
  }

  ?>
    <style type="text/css">
      #post-body-content {
        display: none;
      }
    </style>
  <?php 
}
add_action('admin_head', 'hide_wp_title_input');

/**
 * Load the new responsive stylesheet
 * 
 * @return void
 */
function register_responsive_styles()
{
  wp_register_style('responsive.css', get_template_directory_uri() . '/dist/styles/responsive.css');

  wp_enqueue_style('responsive.css');
}
add_action('wp_enqueue_scripts', 'register_responsive_styles');

/**
 * Load the org chart stylesheet
 * 
 * @return void
 */
function register_org_chart_styles()
{
  wp_register_style('org-chart.css', get_template_directory_uri() . '/dist/styles/org-chart.css');

  if (is_page_template('page-home.php')) {
    wp_enqueue_style('org-chart.css');
  }
}
add_action('wp_enqueue_scripts', 'register_org_chart_styles');