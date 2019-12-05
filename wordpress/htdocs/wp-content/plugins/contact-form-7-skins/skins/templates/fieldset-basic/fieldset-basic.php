<?php
/**
* Template Name: Fieldset - Basic 
* Template URI: http://
* Author: Neil Murray
* Author URI: http://cf7skins.com
* Description:
* Instructions:
* Version: 1.1
* Version Date: 2018-03-30
* Tags: popular, featured, fieldset
**/
?>
<fieldset>
	<legend><?php _e( 'Your Details', CF7SKINS_TEXTDOMAIN); ?></legend>
	<ol>
		<li> <?php _e( 'Name', CF7SKINS_TEXTDOMAIN); ?> [text cf7s-name] </li>
		<li> <?php _e( 'Email', CF7SKINS_TEXTDOMAIN); ?> [email* cf7s-email] </li>
		<li> <?php _e( 'Phone', CF7SKINS_TEXTDOMAIN); ?> [text cf7s-phone] </li>
		<li> <?php _e( 'Message', CF7SKINS_TEXTDOMAIN ); ?> [textarea cf7s-message] </li>
	</ol>
	[submit "<?php _e( 'Submit', CF7SKINS_TEXTDOMAIN); ?>"]
	<p>* <?php _e( 'Required', CF7SKINS_TEXTDOMAIN ); ?></p>
</fieldset>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tempus pharetra vehicula. Aliquam pellentesque mi non scelerisque placerat.</p>