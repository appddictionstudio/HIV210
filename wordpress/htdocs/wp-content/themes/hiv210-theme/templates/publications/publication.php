<div class="col-md-3 text-xs-center">
    <a href="<?php echo wp_get_attachment_url($publication->publication_file) ?>">
        <div>
            <div id='agenda-wrap' style='width:100px;height:125px;margin:auto;margin-bottom:20px;'>
                <img width="100" height="125" src="/wp-content/themes/hiv210-theme/dist/images/icons/pdf_icon.svg" class="attachment-medium size-medium" alt="Click to download file" />
            </div>

            <h3 class="kicker-text" style='white-space:pre-wrap;'><?php echo $publication->publication_title ?></h3>
        </div>
    </a>
</div>