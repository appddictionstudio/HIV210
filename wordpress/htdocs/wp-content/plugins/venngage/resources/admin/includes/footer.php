<?php
    add_thickbox();

    global $post, $wp_version;
?>

<a id="venngagePopupA" href="#TB_inline?height=550&inlineId=venngagePopup" class="thickbox"></a>

<div id="venngageLogo" style="display:none">
    <img src="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>white-med-size.png" class="new">
    <img src="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>white-med-size.png" class="old">
</div>

<div id="venngageRepresentation" style="display:none" contenteditable="false">
    <div class="venngageEmbed" contenteditable="false" data-seq="_x_">
        <div class="content">
            <div class="close">x</div>
            <img src="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>black-med-size.png">
            <h3 class="venngage"><?php _e("Your Embed will display here. Click on Preview to see it.", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?></h3>
            <img src="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>infographicsicon.png">
        </div>
        <div class="clear"></div>
        <div class="venngageOverlay" id="overlay-_x_"><br/></div>
    </div>
</div>

<div id="venngagePopup" style="display:none">
<div class="venngagePopupContent <?php echo $wp_version <= 3.8 ? "old" : "new"?>">
    <div id="venngage_embed">
        <h2 class="venngage"><?php _e("Embed", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?></h2>
        <div class="textarea">
            <textarea id="embedcode" name="embedcodetext" class="large-text" placeholder="<?php _e("Paste the published URL here", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?>"></textarea>
        </div>
        <div>
            <input type="button" id="embedBttn" class="button-primary" value="<?php _e("Embed", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?>">
        </div>
<?php
    $array  = Venngage::getPostMeta($post->ID, "embedurl");
    if($array){
        foreach($array as $indx=>$url){
?>
    <input type="hidden" class="embedcodehidden" name="embedcode[]" value="<?php echo $indx;?>||<?php echo esc_attr($url);?>" data-seq="<?php echo $indx;?>">
<?php
        }
    }
?>
    </div>

    <div style="display: none"
        id="venngage_resources"
        data-img="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>"
        data-ajax="<?php echo __VENNGAGE_INFOGRAPHICS_AJAX__;?>"
        data-post-id="<?php echo $post->ID;?>"
        data-error-val-msg="<?php _e("Please paste a valid embed URL", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?>"
        data-confirm-msg="<?php _e("Are you sure you want to remove?", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?>"
    ></div>

    <div id="venngage_links">
        <h2 class="venngage"><?php _e("Create", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?></h2>
        <div id="imgs">
            <a href="http://venngage.com" target=_"new">
                <div>
                    <img src="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>infographicsicon.png">
                    <h3><?php _e("Infographics", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?></h3>
                </div>
            </a>
            <a href="http://venngage.com" target=_"new">
                <div>
                    <img src="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>reportsicon.png">
                    <h3><?php _e("Reports", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?></h3>
                </div>
            </a>
            <a href="http://venngage.com" target=_"new">
                <div>
                    <img src="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>postersicon.png">
                    <h3><?php _e("Posters", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?></h3>
                </div>
            </a>
            <a href="http://venngage.com" target=_"new">
                <div>
                    <img src="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>promotionsicon.png">
                    <h3><?php _e("Promotions", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?></h3>
                </div>
            </a>
            <a href="http://venngage.com" target=_"new">
                <div>
                    <img src="<?php echo __VENNGAGE_INFOGRAPHICS_IMAGES__;?>socialicon.png">
                    <h3><?php _e("Social Posts", __VENNGAGE_INFOGRAPHICS_PLUGIN_SLUG__);?></h3>
                </div>
            </a>
        </div>
        <div class="clear"></div>
    </div>
</div>
</div>