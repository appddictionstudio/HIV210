<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://codeboxr.com
 * @since      1.0.0
 *
 * @package    Wpnextpreviouslink
 * @subpackage Wpnextpreviouslink/admin/partials
 */
if (!defined('WPINC')) {
    die;
}
?>
<div class="wrap">
    <h2><?php _e('CBX Next Previous Link/Article Options', 'wpnextpreviouslink'); ?></h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
                            <?php
                            $this->settings_api->show_navigation();
                            $this->settings_api->show_forms();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            include('sidebar.php');
            ?>
        </div>
		<div class="clear clearfix"></div>
    </div>
</div>