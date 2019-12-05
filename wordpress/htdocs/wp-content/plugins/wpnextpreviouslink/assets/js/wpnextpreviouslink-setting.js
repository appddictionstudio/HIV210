(function($) {
    'use strict';

    jQuery(document).ready(function($) {

    	//setting starts

		//Initiate Color Picker
		$('.wp-color-picker-field').wpColorPicker();

		//initialize chosen
		$(".chosen-select").chosen({ width:"auto" });

		// Switches option sections
		$('.wpnextpreviouslink_group').hide();
		var activetab = '';
		if (typeof (localStorage) != 'undefined') {
			//get
			activetab = localStorage.getItem("wpnextprevactactivetab");
		}

		//if url has section id as hash then set it as active or override the current local storage value
		if(window.location.hash){
			activetab = window.location.hash;
			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem("wpnextprevactactivetab", activetab);
			}
		}


		if (activetab != '' && $(activetab).length) {
			$(activetab).fadeIn();
		} else {
			$('.wpnextpreviouslink_group:first').fadeIn();
		}

		$('.wpnextpreviouslink_group .collapsed').each(function() {
			$(this).find('input:checked').parent().parent().parent().nextAll().each(
				function() {
					if ($(this).hasClass('last')) {
						$(this).removeClass('hidden');
						return false;
					}
					$(this).filter('.hidden').removeClass('hidden');
				});
		});

		if (activetab != '' && $(activetab + '-tab').length) {
			$(activetab + '-tab').addClass('nav-tab-active');
		}
		else {
			$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
		}

		$('.nav-tab-wrapper a').click(function(evt) {
			$('.nav-tab-wrapper a').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active').blur();
			var clicked_group = $(this).attr('href');
			if (typeof (localStorage) != 'undefined') {
				//set
				localStorage.setItem("wpnextprevactactivetab", $(this).attr('href'));
			}
			$('.wpnextpreviouslink_group').hide();
			$(clicked_group).fadeIn();
			evt.preventDefault();
		});

		$('.wpsa-browse').on('click', function(event) {
			event.preventDefault();

			var self = $(this);

			// Create the media frame.
			var file_frame = wp.media.frames.file_frame = wp.media({
				title: self.data('uploader_title'),
				button: {
					text: self.data('uploader_button_text')
				},
				multiple: false
			});

			file_frame.on('select', function() {
				var attachment = file_frame.state().get('selection').first().toJSON();

				self.prev('.wpsa-url').val(attachment.url);
			});

			// Finally, open the modal
			file_frame.open();
		});

		//setting ends


		$('.wpnp_image_name').on('change', function (event) {
			var $this = $(this);

			var imagename = $('.wpnp_image_name').val();

			if(imagename != 'custom'){
				$('#wpnp_previousimg').attr('src', wpnp.image_url + 'l_' + imagename + '.png');
				$('#wpnp_nextimg').attr('src', wpnp.image_url + 'r_' + imagename + '.png');
            }
		});
    });
})(jQuery);
