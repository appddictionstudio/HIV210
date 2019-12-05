<?php
/**
* Template Name: Fieldset in Fieldset
* Template URI: http://
* Author: Neil Murray
* Author URI: http://cf7skins.com
* Description:
* Instructions:
* Version: 1.1
* Version Date: 2018-03-30
* Tags: fieldset
**/
?>
<fieldset>
	<legend><?php _e( 'Your Details', CF7SKINS_TEXTDOMAIN ); ?></legend>
	<ol>
		<li> <?php _e( 'Name', CF7SKINS_TEXTDOMAIN ); ?> [text cf7s-name] </li>
		<li> <?php _e( 'Email', CF7SKINS_TEXTDOMAIN ); ?> [email* cf7s-email] </li>
		<li> <?php _e( 'Phone', CF7SKINS_TEXTDOMAIN ); ?> [text cf7s-phone] </li>
		<li> <?php _e( 'Message', CF7SKINS_TEXTDOMAIN ); ?> [textarea cf7s-message] </li>
	</ol>
	<p>Use paragraphs for text that is not a form field.</p>
	<fieldset>
	<legend><?php _e( 'Your Requirements', CF7SKINS_TEXTDOMAIN ); ?></legend>
	<ol>
		<li> <?php _e( 'Checkboxes', CF7SKINS_TEXTDOMAIN ); ?> [checkbox cf7s-checkbox-01 "<?php _e( 'Option 1', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 2', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 3', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
		<li> <?php _e( 'Radio Buttons', CF7SKINS_TEXTDOMAIN ); ?> [radio cf7s-radio-01 default:1 "<?php _e( 'Yes', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'No', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
		<li> <?php _e( 'Dropdown Select', CF7SKINS_TEXTDOMAIN ); ?> [select cf7s-select-01 "<?php _e( 'Item 1', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Item 2', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Item 3', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
	</ol>
	</fieldset>
	[submit "<?php _e( 'Submit', CF7SKINS_TEXTDOMAIN); ?>"]
	<p>* <?php _e( 'Required', CF7SKINS_TEXTDOMAIN ); ?></p>
</fieldset>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tempus pharetra vehicula. Aliquam pellentesque mi non scelerisque placerat.</p>
