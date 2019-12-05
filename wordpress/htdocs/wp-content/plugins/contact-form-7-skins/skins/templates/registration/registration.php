<?php
/**
* Template Name: Registration
* Template URI: http://
* Author: Neil Murray
* Author URI: http://cf7skins.com
* Description:
* Instructions:
* Version: 1.1
* Version Date: 2018-03-30
* Tags: 
**/
?>
<fieldset>
	<legend><?php _e( 'Registration', CF7SKINS_TEXTDOMAIN ); ?></legend>
	<ol>
		<li> <?php _e( 'Name', CF7SKINS_TEXTDOMAIN ); ?> [text cf7s-name] </li>
		<li> <?php _e( 'Address', CF7SKINS_TEXTDOMAIN ); ?> [text cf7s-address] </li>
		<li> <?php _e( 'City', CF7SKINS_TEXTDOMAIN ); ?> [text cf7s-city] </li>
		<li> <?php _e( 'State / Province / Region', CF7SKINS_TEXTDOMAIN ); ?> [text cf7s-state] </li>
		<li> <?php _e( 'Phone Number', CF7SKINS_TEXTDOMAIN ); ?> [tel cf7s-phone] </li>
		<li> <?php _e( 'Email', CF7SKINS_TEXTDOMAIN ); ?> [email cf7s-email] </li>
		<li> <?php _e( 'Message', CF7SKINS_TEXTDOMAIN ); ?> [textarea cf7s-special-needs] </li>
		<li> <?php _e( 'Preferred Contact Method', CF7SKINS_TEXTDOMAIN ); ?> [radio cf7s-radio1 default:1 "<?php _e( 'Email', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Phone', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
		<li> <?php _e( 'Drop-down menu', CF7SKINS_TEXTDOMAIN ); ?> [select cf7s-select1 "<?php _e( 'Option 1', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 2', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 3', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
		<li> <?php _e( 'Checkboxes', CF7SKINS_TEXTDOMAIN ); ?> [checkbox cf7s-checkbox1 "<?php _e( 'Option 1', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 2', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 3', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
	</ol>
	[submit "<?php _e( 'Submit', CF7SKINS_TEXTDOMAIN); ?>"]
</fieldset>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tempus pharetra vehicula. Aliquam pellentesque mi non scelerisque placerat.</p>
