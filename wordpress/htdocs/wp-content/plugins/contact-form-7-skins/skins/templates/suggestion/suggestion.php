<?php
/**
* Template Name: Suggestion
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
	<legend><?php _e( 'Suggestion Form', CF7SKINS_TEXTDOMAIN); ?></legend>
	<p><strong><?php _e( 'Please let us know what you think.', CF7SKINS_TEXTDOMAIN); ?></strong></p>
	<ol>
		<li> <?php _e( 'In which of the following areas do you have a suggestion?', CF7SKINS_TEXTDOMAIN); ?> [select cf7s-select1 multiple"<?php _e( 'Area 1', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Area 2', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Area 3', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Area 4', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
		<li> <p><?php _e( 'Note: You can select multiple items (Use Shift or Ctrl/Cmd + Click)', CF7SKINS_TEXTDOMAIN); ?></p> </li>
		<li> <?php _e( 'Suggestion', CF7SKINS_TEXTDOMAIN); ?> [text cf7s-suggestion] </li>
		<li> <?php _e( 'Details', CF7SKINS_TEXTDOMAIN); ?> [textarea cf7s-details] </li>
		<li> <?php _e( 'Your Email - please enter your email if you would like us to follow up with you.', CF7SKINS_TEXTDOMAIN); ?> [email cf7s-email] </li>
		<li> <?php _e( 'Radio buttons', CF7SKINS_TEXTDOMAIN); ?> [radio cf7s-radio1 default:1 "<?php _e( 'Option 1', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 2', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 3', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
		<li> <?php _e( 'Checkboxes', CF7SKINS_TEXTDOMAIN); ?> [checkbox cf7s-checkbox1 "<?php _e( 'Option 1', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 2', CF7SKINS_TEXTDOMAIN ); ?>" "<?php _e( 'Option 3', CF7SKINS_TEXTDOMAIN ); ?>"] </li>
	</ol>
	[submit "<?php _e( 'Submit', CF7SKINS_TEXTDOMAIN); ?>"]
</fieldset>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tempus pharetra vehicula. Aliquam pellentesque mi non scelerisque placerat.</p>