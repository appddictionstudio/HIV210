<?php
/**
 * CF7 Skins Admin Class.
 * 
 * Implement all functionality for CF7 Skins Admin on CF7 plugin page.
 * 
 * @package cf7skins
 * @author Neil Murray
 * 
 * @since 0.1.0
 */

class CF7_Skins_Admin {
	
	/**
	 * Setup plugin hooks.
	 * 
	 * @since 0.1.0
	 */
	function __construct() {
	
		/**
		 * Return if current user does not have capability access.
		 * 
		 * @since 1.1.1
		 */
		if ( ! current_user_can( WPCF7_ADMIN_READ_WRITE_CAPABILITY ) ) {
			return;
		}

		// Action hooks to store selected template and style while updating or creating new CF7
		add_action( 'wpcf7_after_create', array( &$this, 'skin_update' ) );
		add_action( 'wpcf7_after_create', array( &$this, 'cf7skins_copy' ) ); // @since 1.2.0
		add_action( 'wpcf7_after_update', array( &$this, 'skin_update' ) );

		// Store current form id while duplicating
		add_filter( 'wpcf7_copy', array( $this, 'store_copy_id' ), 1, 2 ); // @since 1.2.0

		// Push the styles and scripts to the admin header
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		
		// Create metabox for CF7 Skins
		// 'wpcf7_add_meta_boxes' hook removed from CF7 in v4.2
		// @link https://contactform7.com/2015/05/18/contact-form-7-42-beta/
		add_action( 'wpcf7_add_meta_boxes', array( &$this, 'add_meta_boxes' ) ); // CF7 < v4.2
		add_action( 'wpcf7_admin_footer', array( &$this, 'add_meta_boxes_42' ) ); // CF7 >= v4.2
		
		// All other Addons should use this action hook
		add_action( 'wpcf7_admin_footer', array( $this, 'cf7skins_admin_footer' ) ); // @since 1.1.1
		add_filter( 'cf7s_visual_update_js_callbacks', array( $this, 'visual_update' ) ); // @since 2.0.0
		add_action( 'wp_ajax_cf7s_update_mailtags', array( &$this, 'update_mailtags' ) ); // @since 2.0.0
	}
	
	/**
	 * Update current form post meta data with selected style and/or template.
	 *
	 * Get post id (contact form 7 form id) in $_POST['cf7s-template'] and $_POST['cf7s-style']
	 * Post meta name 'cf7s_template' for template and 'cf7s_style' for style
	 * 
	 * @action 'wpcf7_after_create'
	 * @action 'wpcf7_after_update'
	 * @do_action 'cf7s_update'
	 * 
	 * @param $cf7 (class) WPCF7_ContactForm - Contact Form 7 object data
	 * 
	 * @since 0.1.0
	 */
	function skin_update( $cf7 ) {  // NRM - CHANGE name to cf7skins_update & match with do_action
		// Get the current contact form 7 id
		$form_id = CF7_Skins_Contact::get_form_id( $cf7 );
		
		// Update the post meta
		if( isset( $_POST['cf7s-template'] ) ) {
			update_post_meta( $form_id, 'cf7s_template', esc_attr( $_POST['cf7s-template'] ) );
		}

		if( isset( $_POST['cf7s-style'] ) ) {
			update_post_meta( $form_id, 'cf7s_style', esc_attr( $_POST['cf7s-style'] ) );
		}

		// Update metabox state
		if( isset( $_POST['cf7s-postbox'] ) ) {
			update_post_meta( $form_id, 'cf7s_postbox', $_POST['cf7s-postbox'] );
		}

		// Add action while saving the Contact Form 7 form
		do_action( 'cf7s_update', $cf7 );
	}
	
	/**
	 * Copy Contact Form 7 form with CF7 Skins data included.
	 * 
	 * @action 'wpcf7_after_create'
	 * @do_action 'cf7skins_copy'
	 * 
	 * @param $cf7 (class) WPCF7_ContactForm
	 * 
	 * @since 1.2.0
	 */
	function cf7skins_copy( $cf7 ) {
		// make sure this is a copy action only
		// @see function store_copy_id()
		if( ! $copy_id = get_option( '_cf7skins_copy_id' ) ) {
			return;
		}

		$cf7->copy_id = $copy_id; // add new property to the class for further use
		
		// Get current skins
		$template = get_post_meta( $copy_id, 'cf7s_template', true );
		$style = get_post_meta( $copy_id, 'cf7s_style', true );
		$postbox = get_post_meta( $copy_id, 'cf7s_postbox', true );
		
		// Copy skins
		update_post_meta( $cf7->id(), 'cf7s_template', $template );
		update_post_meta( $cf7->id(), 'cf7s_style', $style );
		update_post_meta( $cf7->id(), 'cf7s_postbox', $postbox );
		
		/**
		 * Copy hook action
		 * 
		 * @param $cf7 (class) WPCF7_ContactForm
		 * 		  $cf7->id() is the newly created CF7 ID
		 * 		  $cf7->copy_id is the original copied CF7 ID
		 * 
		 * @since 1.1.2
		 */
		do_action( 'cf7skins_copy', $cf7 );
		
		// Delete option to avoid this function running twice while doing default saving
		delete_option( '_cf7skins_copy_id' );
	}
	
	/**
	 * Store current form id while duplicating.
	 * 
	 * @filter 'wpcf7_copy'
	 * 
	 * @param $new (object) newly created Contact Form 7
	 *		  $wpcf7 (class) WPCF7_ContactForm
	 *
	 * @since 1.2.0
	 */
	function store_copy_id( $new, $wpcf7 ) { // NRM - why is this called $wpcf7 & not $cf7 like elsewhere?
		update_option( '_cf7skins_copy_id', $wpcf7->id() ); // store copied ID to database temporarily
		return $new;
	}

	/**
	 * Enqueue CF7 Skins admin styles and scripts.
	 * 
	 * @action 'admin_enqueue_scripts'
	 * @do_action 'cf7s_admin_enqueue_scripts'
	 * 
	 * @param $hook_suffix current page hook
	 * 
	 * @since 0.1.0
	 */
	function admin_enqueue_scripts( $hook_suffix ) {
		// NRM - ADD EXPLANATION
		if ( false === strpos( $hook_suffix, 'wpcf7' ) ) {
			return;
		}

		wp_enqueue_style( 'tipsy',
			CF7SKINS_URL . 'css/tipsy.css',
			array( 'contact-form-7-admin' ), '1.0.0a', 'all' );
		
		wp_enqueue_style( 'typicons',
			CF7SKINS_URL . 'css/typicons/typicons.min.css',
			array( 'contact-form-7-admin' ), '2.0.7', 'all' );	
			
		wp_enqueue_style( 'cf7s-admin',
			CF7SKINS_URL . 'css/admin.css',
			array( 'contact-form-7-admin' ), CF7SKINS_VERSION, 'all' );

		wp_enqueue_script( 'tipsy',
			CF7SKINS_URL . 'js/jquery.tipsy.js',
			array( 'jquery' ), '1.0.0a', true );
		
		wp_enqueue_script( 'cf7s-admin',
			CF7SKINS_URL . 'js/jquery.admin.js',
			array( 'jquery', 'underscore' ), CF7SKINS_VERSION, true );
		
		wp_localize_script( 'cf7s-admin', 'cf7s', array(
			'nonce'		=> wp_create_nonce( 'cf7s' ),  // generate a nonce for security checking
			'load'		=> 'load_template',            // post action for reading and loading selected template
			'sort'		=> 'cf7s_sort_skin',           // post action for sorting skin
			'l10n'		=> array(
				'loading'		=> __('Loading template...', CF7SKINS_TEXTDOMAIN ),
				'emptyfilter'	=> __('Empty filter, please select a filter.', CF7SKINS_TEXTDOMAIN ),
				'select' 		=> __('Select', CF7SKINS_TEXTDOMAIN ),
				'selected' 		=> __('Selected', CF7SKINS_TEXTDOMAIN ),
				'deselect' 		=> __('Deselect', CF7SKINS_TEXTDOMAIN ),
				'expanded'		=> __('Expanded View', CF7SKINS_TEXTDOMAIN ),
				'deselect_style'	=> __('Click to remove this Style from your form.', CF7SKINS_TEXTDOMAIN ),
				'deselect_template'	=> __('Click to remove this Template from your form.', CF7SKINS_TEXTDOMAIN ),
			)
		));
		
		do_action( 'cf7s_admin_enqueue_scripts', $hook_suffix );
	}
	
	/**
	 * Create the skins metabox - CF7 < v4.2 only.
	 * 
	 * @action 'wpcf7_add_meta_boxes'
	 * @do_action 'cf7s_add_meta_boxes'
	 * 
	 * @param $post_id is the current post editing ID
	 * 
	 * @since 0.1.0
	 */
	function add_meta_boxes( $post_id ) {
		add_meta_box( 'cf7s', __( 'Skins', CF7SKINS_TEXTDOMAIN ),
			array( &$this, 'skins_meta_box' ), null, 'mail', 'core' );
	
		// Add action while creating the skins metabox
		do_action( 'cf7s_add_meta_boxes', $post_id ); // NRM - why is this hook only added in CF7 < v4.2?
	}
	
	/**
	 * Custom skins dialog/metabox added in CF7 footer from CF7 version 4.2.
	 *
	 * @action 'wpcf7_admin_footer'
	 * 
	 * @param $post contact form object
	 * 
	 * @since 1.0.1
	 */
	function add_meta_boxes_42( $cf7 ) {
		if (version_compare(WPCF7_VERSION, '4.2') >= 0) {
			
			// set expand/collapse state for skins metabox
			$postbox_meta = get_post_meta( $cf7->id(), 'cf7s_postbox', true );
			$postbox_class = isset( $postbox_meta[CF7SKINS_OPTIONS] ) ? $postbox_meta[CF7SKINS_OPTIONS] : '';
			
			// Create container id for JavaScript pointer
			// This was previously added by add_meta_box() function
			echo '<div class="wrap">';
				echo '<div id="cf7skins-42" class="postbox '. $postbox_class  .'">';
				echo '<input type="hidden" value="'.$postbox_class.'" class="cf7skins-42 cf7s-postbox" name="cf7s-postbox['. CF7SKINS_OPTIONS .']" />'; // postbox expand/collapse
				echo '<div title="'. __('Click to toggle', CF7SKINS_TEXTDOMAIN ) .'" class="handlediv"><br></div>';
					echo '<h3 class="hndle"><span>'. __('Skins', CF7SKINS_TEXTDOMAIN ) .'</span></h3>';
					echo '<div class="inside">';
						echo '<div id="cf7s" class="cf7-42">';
							$this->generate_tab( $cf7, null ); // in tab.php
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		}
	}
	
	/**
	 * Display the skins metabox. CF7 < v4.2 only
	 * 
	 * @param $post current post object
	 * @param $box metabox arguments
	 * 
	 * @since 0.1.0
	 */
	function skins_meta_box( $post, $box ) {
		$this->generate_tab( $post, $box ); // in tab.php
	}
	
	/**
	 * Check if current admin page is Contact Form 7 editing page.
	 * This function can be used to determine if it is a CF7 edit/new page.
	 * 
	 * @return {boolean}
	 * 
	 * @since 1.0.2
	 */
	public static function edit_page() {
		
		// The hook_suffix is same as get_current_screen()->base.
		global $hook_suffix;
		
		// Don't show at the Contact Form 7 list wp-admin/admin.php?page=wpcf7
		if ( 'toplevel_page_wpcf7' == $hook_suffix && isset( $_GET['post'] ) && ! empty( $_GET['post'] ) ) {
			return true;
		}

		/** 
		 * CF7 registers a menu title and it is used by WordPress to generate the hook suffix,
		 * The menu title is translated to current user/site language if available.
		 *
		 * Since the wpcf7_admin_menu_change_notice() was introduced,
		 * CF7 adds a bullet red number (a.k.a plugins count) to the add_menu_page menu() title
		 * that adds a number in hook suffix changing it from i.e "contact_page_wpcf7-new" to "contact-8_page_wpcf7-new".
		 *
		 * Approach taken is to get last 15 chars of hook suffix and should be
		 * similar to '_page_wpcf7-new', without tranlasted menu title and a number.
		 * 
		 * @links 	https://plugins.trac.wordpress.org/browser/contact-form-7/trunk/admin/admin.php#L25
		 *			https://plugins.trac.wordpress.org/changeset/1985273
		 *			https://core.trac.wordpress.org/browser/tags/5.0.3/src/wp-admin/includes/plugin.php#L1097
		 * 
		 * @since 2.0.2
		 */
		$wpcf7_new = '_page_wpcf7-new' === substr( $hook_suffix, -15 ) ? true : false;
		
		// Backward compatibility for CF7 new page
		if ( version_compare( WPCF7_VERSION, '4.4' ) <= 0 ) {
			if ( $wpcf7_new && isset( $_GET['locale'] ) ) { // CF7 < 4.4 new page
				return true;
			}
		} else {
			if ( $wpcf7_new ) { // CF7 > 4.4 new page
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Add CF7 Skins admin footer hook.
	 * 
	 * All CF7 Skins addons should use this action hook.
	 * 
	 * @action 'wpcf7_admin_footer'
	 * @do_action cf7skins_admin_footer
	 * 
	 * @since 1.1.1
	 */
	function cf7skins_admin_footer() {
		do_action( 'cf7skins_admin_footer' );
	}
	
	/**
	 * Update mailtag after Visual AJAX save by running
	 * JavaScript callback function 'updateMailtags'.
	 * See /js/jquery.admin.js file.
	 * 
	 * @since 2.0.0
	 */
	function visual_update( $callbacks ) {
		// We are using JavaScript namespace/class cf7sAdmin.updateMailtags
		$callbacks[] = array( 'cf7sAdmin' => 'updateMailtags' );
		return $callbacks;
	}
	
	/**
	 * Update mailtags after save Visual
	 * 
	 * @since 2.0.0
	 */
	function update_mailtags() {
		
		// Check if we have the same nonce for security reason
		if ( ! wp_verify_nonce( $_POST['nonce'], 'cf7s' ) || ! isset( $_POST['post_ID'] ) )
			die();
		
		$cf7 = WPCF7_ContactForm::get_instance( (int) $_POST['post_ID'] ); // get CF7 object
		$cf7->set_properties( // manual set CF7 form value
			array( 'form' => stripslashes( $_POST['form'] ) ) // remove extra backslases
		);
		
		// Get all mailtags for current form
		// Uses ob_start because suggest_mail_tags() function echoing output
		ob_start();
		$cf7->suggest_mail_tags( 'mail' ); // exclude 'not-for-mail' tag type
		$mailtags = ob_get_contents();
		ob_end_clean();
		
		echo json_encode( $mailtags );
		exit;
	}
}
