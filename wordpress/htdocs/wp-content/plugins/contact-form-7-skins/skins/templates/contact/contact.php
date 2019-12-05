<?php
/**
* Template Name: Contact
* Template URI: http://
* Author: Neil Murray
* Author URI: http://cf7skins.com
* Description:
* Instructions:
* Version: 1.1
* Version Date: 2018-03-30
* Tags: popular, contact
**/
?>
<fieldset>
	<legend><?php _e( 'Contact Form', CF7SKINS_TEXTDOMAIN); ?></legend>
	<ol>
		<li> <?php _e( 'Your Name (required)', CF7SKINS_TEXTDOMAIN ); ?> [text* cf7s-name] </li>
		<li> <?php _e( 'Email Address (required)', CF7SKINS_TEXTDOMAIN ); ?> [email* cf7s-email-address] </li>
		<li> <?php _e( 'Your Message', CF7SKINS_TEXTDOMAIN ); ?> [textarea cf7s-message] </li>
	</ol>
	[submit "<?php _e( 'Submit', CF7SKINS_TEXTDOMAIN); ?>"]
	<p>* <?php _e( 'Required', CF7SKINS_TEXTDOMAIN ); ?></p>
</fieldset>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tempus pharetra vehicula. Aliquam pellentesque mi non scelerisque placerat.</p>