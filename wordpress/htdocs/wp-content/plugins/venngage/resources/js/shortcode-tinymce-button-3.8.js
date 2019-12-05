(function() {
    tinymce.create("tinymce.plugins.venngageshortcode", {

        init : function(ed, url) {
            var t = this;
            t.editor = ed;
            
            registerTinyMCE(tinymce, ed, "old");
            venngageAlterContent(true);
        },

        //Creates the dropdown
        createControl : function(n, cm)
        {
            if(n == "venngageshortcode")
            {
                var button = cm.createButton("venngageshortcode",
                {
                    title : "Infographics",
                    image: jQuery("#venngage_resources").attr("data-img") + "infographicsicon.png",
                    onclick: function(e) {
                        venngagePopUp(true);
                    },
                });
                return button;
            }
            return null;
        }
    });

    tinymce.PluginManager.add("venngageshortcode", tinymce.plugins.venngageshortcode);

})();