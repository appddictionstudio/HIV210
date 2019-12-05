var __tinyMCE, __editor, __vrsn;

var __activeEmbedIndx = -1;

jQuery(document).ready(function(){
    jQuery("#embedBttn").on("click", function(){
        validateEmbedURL();
    });
});

function registerTinyMCE(tinymce, editor, vrsn){
    __tinyMCE           = tinymce;
    __editor            = editor;
    __vrsn              = vrsn;
}

function commonTinyMCE_BeforeSetContent(event){
    var html        = jQuery("#venngageRepresentation").clone().html().replace(/>\s+</g,'><');
    //console.log(html);
    event.content       = event.content.replace(/\[infographic([^\]]*)\]/g, function(all, attribs) {
        var id  = getAttr(attribs, "id");
        return html.replace(/_x_/g, id) + "<p></p>";
    });
}

function commonTinyMCE_GetContent(event){
    event.content = event.content.replace(/((<div class="venngageEmbed"[^<>]*>)(.*?)(?:<\/div><\/div>))/g, function(matched, tags) {
            var attrib = getAttr(tags, "data-seq");
            if (attrib) {
                return '<p>[infographic id="' + attrib + '"]</p>';
            }
            return matched;
        });
}

function commonTinyMCE_click(event){
    if ( ( event.target.nodeName == "DIV" ) && ( event.target.className.indexOf("venngageOverlay") > -1 ) ) {
        var arr             = event.target.id.split("-");
        //venngageLog("e.target.id = " + event.target.id);
        __activeEmbedIndx   = arr[1];
        venngagePopUp(false);
    }

    if ( ( event.target.nodeName == "DIV" ) && ( event.target.className.indexOf("close") > -1 ) ) {
        if(confirm(jQuery("#venngage_resources").attr("data-confirm-msg"))){
            jQuery(tinyMCE.activeEditor.dom.doc.body).find(event.target).parent().parent().remove();
        }
    }
}

function venngageAlterContent(firstTime){
    if(__vrsn == "new"){
        __editor.on("BeforeSetContent", function( event ) {
            commonTinyMCE_BeforeSetContent(event);
        });
      
        __editor.on("GetContent", function( event ) {
            commonTinyMCE_GetContent(event);
        });

        __editor.on("click", function(event) {
            commonTinyMCE_click(event);
        });
    }else{
        __editor.onBeforeSetContent.add(function(ed, event) {
            commonTinyMCE_BeforeSetContent(event);
        });

        __editor.onGetContent.add(function(ed, event) {
            commonTinyMCE_GetContent(event);
        });

        __editor.onClick.add(function(ed, event) {
            commonTinyMCE_click(event);
        });

    }
}

function venngageEmbed(url){
    if(url == null || url == ""){
        tb_remove();
        return;
    }

    venngageLog("venngageEmbed:: __activeEmbedIndx = " + __activeEmbedIndx);

    var id  = "x" + new Date().getTime();
    var val = url;

    if(__activeEmbedIndx == -1 && val != "") __editor.execCommand('mceInsertContent', false, '[infographic id="' + id + '"]');

    venngageAlterContent(false);
    venngageLog("venngageEmbed:: after venngageAlterContent " + __activeEmbedIndx);

    //val     = makeSafe(val);

    if(__activeEmbedIndx != -1){
        id      = jQuery(".embedcodehidden[data-seq='" + __activeEmbedIndx + "']").attr("data-seq");
        var obj = jQuery(".embedcodehidden[data-seq='" + __activeEmbedIndx + "']").val(id + "||" + val);
    }else{
        jQuery('<input type="hidden" class="embedcodehidden" name="embedcode[]" value="' + id + "||" + val + '" data-seq="' + id + '">').appendTo("#venngage_embed");
    }

    __activeEmbedIndx   = -1;
    jQuery("#embedcode").val("");
    venngageLog("venngageEmbed:: __activeEmbedIndx = " + __activeEmbedIndx);

    jQuery.ajax({
        url: jQuery("#venngage_resources").attr("data-ajax"),
        method: "post",
        data: "_action=update&post=" + jQuery("#venngage_resources").attr("data-post-id") + "&id=" + id + "&val=" + val,
        success:  function(data, textStatus, jqXHR){
            venngageLog("updated");
        }
    });

    tb_remove();
}

function venngagePopUp(fromClick){
    if(fromClick){
        __activeEmbedIndx   = -1;
        jQuery("#embedcode").val("");
    }

    venngageLog("venngagePopUp:: __activeEmbedIndx = " + __activeEmbedIndx);
    if(__activeEmbedIndx != -1){
        var code = jQuery(".embedcodehidden[data-seq='" + __activeEmbedIndx + "']").val();
        if(code){
            var arr = code.split("||");
            jQuery("#embedcode").val(makeUnsafe(arr[1]));
        }
    }
    venngageLog("venngagePopUp:: __activeEmbedIndx = " + __activeEmbedIndx);

    jQuery("#venngagePopupA").trigger("click");
    jQuery("#TB_ajaxWindowTitle").append(jQuery("#venngageLogo img." + __vrsn).clone().show());
}

function venngageLog(msg){
    //console.log(msg);
}

function getAttr( pattern, attr ) {
    n = new RegExp( attr + '=\"([^\"]+)\"', 'g' ).exec( pattern );
    return n ? n[1] : '';

};

function makeSafe(val){
    return val.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, "&apos;");
}
function makeUnsafe(val){
    return val.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&').replace(/&quot;/g, '"').replace(/&apos;/g, "'");
}


function validateEmbedURL(){
    var url     = jQuery("#embedcode").val();
    jQuery.ajax({
        url: jQuery("#venngage_resources").attr("data-ajax"),
        method: "post",
        data: "_action=validate&url=" + url,
        dataType: "json",
        success: function(data, textStatus, jqXHR){
            if(data != null && data.error){
                venngageLog("error");
                alert(jQuery("#venngage_resources").attr("data-error-val-msg"));
                venngageEmbed(null);
            }else{
                venngageLog("validated");
                venngageEmbed(url);
            }
        }
    });
}

function getEmbedCode(url, div){
    var metadataEndPointURL = url.replace('/p/', '/d/');
    var embedURL = url.replace('/p/', '/e3/');

    jQuery.ajax({
        url: metadataEndPointURL,
        type: 'GET',
        dataType: 'jsonp',
        error: function(xhr, status, error) {
            // ERROR
            // 404 means the infographic with that community ID wasn't found
            venngageLog("error");
        },
        success: function(json) {
            // Example JSON:
            /*{
                 "status": "success",   // Success
                 "width": 1200,         // Width of infographic
                 "height": 800,         // Height of infographic
                 "embedExtraHeight": 65 // Free users have an extra Venngage banner for embeds
            }*/
            var embedCode = generateEmbedCode(json.width, json.height, json.embedExtraHeight);
            jQuery("#" + div).append(makeUnsafe(embedCode));
        }
    });

    // This returns the escaped embed code.
    function generateEmbedCode(width, height, embedExtraHeight) {
    	// Generate a random ID (0 - 999) so a page can have multiple infographics embeded on it without the script messing up.
    	var embedRandomID = Math.floor(Math.random() * 999);
        var extraPadding = 4;
        width += extraPadding;
        height += extraPadding + embedExtraHeight;

    	// Generate and insert the embed code into its text area.
    	var embedCode = '&lt;div id="vgdiv' + embedRandomID + '" style="margin: 0 auto;max-width:'
        + width + 'px;position: relative;padding-bottom: 100%;height: 0;overflow: hidden;"&gt;&lt;iframe id="vgframe'
        + embedRandomID + '" style="position: absolute;top:0;left: 0;width:100%;height: 100%;border:0;overflow:hidden;max-width:'
        + width + 'px;" src="'
        + embedURL + '"&gt;&lt;/iframe&gt;&lt;/div&gt;&lt;script&gt;!function(){var t=document.getElementById("vgframe'
        + embedRandomID + '"),e=document.getElementById("vgdiv'
        + embedRandomID + '"),n=function(){t.contentWindow.postMessage(e.offsetWidth,"*"),e.style.paddingBottom=e.offsetWidth*'
        + height + '/' + width + '+"px"};window.addEventListener("resize",n),window.addEventListener("load",n)}();&lt;/script&gt;';

        return embedCode;
    }
}