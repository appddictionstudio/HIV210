<?php
/**
 * License Class.
 * 
 * Implements EDD Licensing & Updates.
 * 
 * @package cf7skins
 * @author Neil Murray
 * 
 * @since 0.2.0
 */

class CF7_Skins_License {
	
	/**
	 * Class constructor
	 * 
	 * @action 'cf7skins_section_license'
	 * 
	 * @since 0.2.0
	 */
	function __construct() {
	
		// License tab should not be shown if CF7 Skins Pro is not installed
		// add_filter( 'cf7skins_setting_tabs', array( &$this, 'license_tab' ), 1, 1 );
		add_action( 'cf7skins_section_license', array( &$this, 'active_sites' ) );
	}

	/**
	 * Enable license tab if other plugins need activation.
	 *
	 * NRM - where is this now called?
	 * 
	 * @param (TYPE)	$tabs	ADD EXPLANATION
	 * 
	 * @since 0.5.0
	 */
    function license_tab( $tabs ) {
		$tabs['license'] = __( 'Licenses', CF7SKINS_TEXTDOMAIN );
		return $tabs;
	}

	/**
	 * Add active sites based on license key.
	 * 
	 * NRM - why is this not done in CF7 Skins Pro plugin?
	 * 
	 * @since 0.2.0
	 */
	function active_sites() {
		echo '
		<br /><hr />
		<h3>'. __( 'Active Licenses', CF7SKINS_TEXTDOMAIN ) .'</h3>
		<table class="wp-list-table widefat fixed tags" style="background-color: transparent;">
		<tbody>
			<tr>
				<th scope="row"><b>'. __( 'Plugin Name', CF7SKINS_TEXTDOMAIN ) .'</b></th>
				<th scope="row"><b>'. __( 'License Key', CF7SKINS_TEXTDOMAIN ) .'</b></th>
				<th scope="row"><b>'. __( 'Site URL', CF7SKINS_TEXTDOMAIN ) .'</b></th>
			</tr>';

			$logs = apply_filters( 'cf7skins_activation_logs', get_option( 'cf7skins_activation' ) ); // @since 1.2.0

			if( is_array( $logs ) ) {

				foreach( $logs as $log ) {
					echo '<tr>';
						echo '<td>'. $log->item_name .'</td>';
						echo '<td>'. $log->license_key .'</td>';
						
						echo '<td>';
						$sites = json_decode( $log->sites );
						foreach( $sites as $site ) 
							echo $site . '<br />';
						echo '</td>';
					echo '</tr>';
				}
			}

		echo '
		</tbody>
		</table>';
	}

} new CF7_Skins_License();
