<div id="<?php echo $id;?>"></div>
<script>
    jQuery(document).ready(function(){
        venngageLog("calling for <?php echo $id;?>");
        getEmbedCode("<?php echo $url;?>", "<?php echo $id;?>");
    });
</script>