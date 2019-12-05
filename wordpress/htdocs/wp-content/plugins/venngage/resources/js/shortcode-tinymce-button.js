(function() {
    tinymce.PluginManager.add("venngageshortcode", function( editor )
    {
        var shortcodeValues = [];

        registerTinyMCE(tinymce, editor, "new");

        venngageAlterContent(true);

        editor.addButton("venngageshortcode", {
            title: "Infographics",
            image: jQuery("#venngage_resources").attr("data-img") + "infographicsicon.png",
            onclick: function(e) {
                venngagePopUp(true);
            },
        });
    });
})();