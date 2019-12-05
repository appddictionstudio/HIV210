<?php
/**
* Template Name: Event
* Template URI: http://
* Author: Neil Murray
* Author URI: http://cf7skins.com
* Description:
* Instructions:
* Version: 1.1
* Version Date: 2018-03-30
* Tags: featured, event
**/
?>
<fieldset>
	<legend><?php _e( 'Event', CF7SKINS_TEXTDOMAIN); ?></legend>
	<ol>
		<li> <?php _e( 'Name', CF7SKINS_TEXTDOMAIN); ?> [text cf7s-name] </li>
		<li> <?php _e( 'Phone Number', CF7SKINS_TEXTDOMAIN); ?> [tel cf7s-phone] </li>
		<li> <?php _e( 'Email', CF7SKINS_TEXTDOMAIN); ?> [email* cf7s-email] </li>
		<li> <?php _e( 'Which workshops will you be attending?', CF7SKINS_TEXTDOMAIN ); ?> [checkbox cf7s-checkbox1 "<?php _e( 'Option 1', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 2', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 3', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
		<li> <?php _e( 'Are you an existing customer?', CF7SKINS_TEXTDOMAIN ); ?> [radio cf7s-radio1 default:1 "<?php _e( 'Yes', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'No', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
		<li> <?php _e( 'How did find out about this event?', CF7SKINS_TEXTDOMAIN); ?> [select cf7s-select1 "<?php _e( 'Option 1', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 2', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 3', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
		<li> <?php _e( 'Comments or Questions', CF7SKINS_TEXTDOMAIN); ?> [textarea cf7s-comments] </li>
	</ol>
	[submit "<?php _e( 'Submit', CF7SKINS_TEXTDOMAIN); ?>"]
	<p>* <?php _e( 'Required', CF7SKINS_TEXTDOMAIN ); ?></p>
</fieldset>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tempus pharetra vehicula. Aliquam pellentesque mi non scelerisque placerat.</p>