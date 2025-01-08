;(function($){
	const dataNS = 'pds-tabs--';
	const ns = 'plethoraplugins_';
	const icons = [
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 13v-2h14v2Z"/></svg>',
        "label": 'Minus'
    },
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11 19v-6H5v-2h6V5h2v6h6v2h-6v6Z"/></svg>',
        "label": 'Plus'
    },
    {
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m12 15.5 4.5-4.5-1.425-1.4L12 12.675 8.925 9.6 7.5 11Zm0 6.5q-2.075 0-3.9-.788-1.825-.787-3.175-2.137-1.35-1.35-2.137-3.175Q2 14.075 2 12t.788-3.9q.787-1.825 2.137-3.175 1.35-1.35 3.175-2.138Q9.925 2 12 2t3.9.787q1.825.788 3.175 2.138 1.35 1.35 2.137 3.175Q22 9.925 22 12t-.788 3.9q-.787 1.825-2.137 3.175-1.35 1.35-3.175 2.137Q14.075 22 12 22Zm0-2q3.35 0 5.675-2.325Q20 15.35 20 12q0-3.35-2.325-5.675Q15.35 4 12 4 8.65 4 6.325 6.325 4 8.65 4 12q0 3.35 2.325 5.675Q8.65 20 12 20Zm0-8Z"/></svg>',
        "label": 'Expand Circle Down (Outlined)'
	},
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m12 15.5 4.5-4.5-1.425-1.4L12 12.675 8.925 9.6 7.5 11Zm0 6.5q-2.075 0-3.9-.788-1.825-.787-3.175-2.137-1.35-1.35-2.137-3.175Q2 14.075 2 12t.788-3.9q.787-1.825 2.137-3.175 1.35-1.35 3.175-2.138Q9.925 2 12 2t3.9.787q1.825.788 3.175 2.138 1.35 1.35 2.137 3.175Q22 9.925 22 12t-.788 3.9q-.787 1.825-2.137 3.175-1.35 1.35-3.175 2.137Q14.075 22 12 22Z"/></svg>',
        "label": 'Expand Circle Down (Filled)'
    },
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="m24 30.75-12-12 2.15-2.15L24 26.5l9.85-9.85L36 18.8Z"/></svg>',
        "label": 'Expand More'
    },
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M14.15 30.75 12 28.6l12-12 12 11.95-2.15 2.15L24 20.85Z"/></svg>',
        "label": 'Expand Less'
    },
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M8.1 44v-3h31.8v3Zm16-4.5-7.6-7.6 2.15-2.15 3.95 3.95V14.3l-3.95 3.95-2.15-2.15 7.6-7.6 7.6 7.6-2.15 2.15-3.95-3.95v19.4l3.95-3.95 2.15 2.15ZM8.1 7V4h31.8v3Z"/></svg>',
        "label": 'Expand'
    },
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="m24 42-9-9 2.2-2.2 6.8 6.8 6.8-6.8L33 33Zm-6.8-24.6L15 15.2l9-9 9 9-2.2 2.2-6.8-6.8Z"/></svg>',
        "label": 'Unfold More'
    },
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M17.15 40 15 37.85l9-9 9 9L30.85 40 24 33.15ZM24 19.15l-9-9L17.15 8 24 14.85 30.85 8 33 10.15Z"/></svg>',
        "label": 'Unfold Less'
    },
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="m24 48-8.4-8.45 2.1-2.1 6.35 6.3 6.35-6.35 2.1 2.1Zm0-10-8.4-8.45 2.1-2.1 6.35 6.3 6.35-6.35 2.1 2.1Zm-6.3-17.4-2.15-2.1 8.5-8.5 8.45 8.45-2.15 2.15L24 14.3Zm0-10-2.15-2.1 8.5-8.5 8.45 8.45-2.15 2.15L24 4.3Z"/></svg>',
        "label": 'Unfold More Double'
    },
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="m17.7 47.95-2.1-2.1L24 37.4l8.5 8.5-2.1 2.1-6.35-6.35Zm0-10-2.1-2.1L24 27.4l8.5 8.5-2.1 2.1-6.35-6.35Zm6.35-17.35-8.5-8.5L17.7 10l6.3 6.3 6.35-6.3 2.15 2.15Zm0-10-8.5-8.5L17.7 0 24 6.3 30.35 0l2.15 2.15Z"/></svg>',
        "label": 'Unfold Less Double'
    },
	{
        "html": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M8.1 42 6 39.9l10.7-10.7h-5.9v-3h11v11h-3v-5.9Zm18.15-20.25v-11h3v5.9L39.9 6 42 8.1 31.35 18.75h5.9v3Z"/></svg>',
        "label": 'Hide'
    },
	
	
	
];
	var $currentIconInput;
	function getIconModal(){
		var id = ns + '_iconmodal';
		var $existing = $('#' + id);
		if($existing.length) return $existing;
		var $modal = $(`<div class="micromodal" aria-hidden="true">
							<div class="micromodal__overlay" tabindex="-1" >
							  <div class="micromodal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
								<button aria-label="Close modal" data-micromodal-close>X</button>
								<div class="micromodal-title-wrapper"><h2>Select Icon</h2><div id="` + ns + `_custom_icon_input_wrapper">or <label for="` + ns + `_custom_icon_input">paste custom HTML markup: </label><textarea rows="1" id="` + ns + `_custom_icon_input" ></textarea><button type="button" id="` + ns + `_custom_icon_input_button" class="  button button-primary">Go</button></div></div>
								<div class="micromodal-content-wrapper"></div>
							  </div>
							</div>
						  </div>`);
	    var $list = $('<ul class="' + ns + 'svg-icon-button-list" />');
		icons.map(icon => { 
			var $icon = $('<button class="' + ns + 'svg-icon-button" type="button" />').attr('aria-label', icon.label).attr('title', icon.label).data(dataNS + 'html', icon.html).html(icon.html);
			$icon.on('click', function(){
				$currentIconInput.val($(this).data(dataNS + 'html')).trigger('change');
				MicroModal.close(); 
			});
			$list.append($('<li/>').append($icon));
		});
	    $modal.attr('id', id).find('.micromodal-content-wrapper').append($list);
	    $('body').append($modal);
		$('#' + ns + '_custom_icon_input_button').on('click', function(){
			var val = $('#' + ns + '_custom_icon_input').val();
			$currentIconInput.val(val).trigger('change');
			MicroModal.close(); 
			
		});
		return $modal;
	}
	$(function(){
		MicroModal.init();
		
		$(document).on('click', '.' + ns + 'settings-form-reset-button', function(){
			//console.log('clicked reset');
			$('.' + ns + 'settings-form').find('[data-' + dataNS + 'default]:not(.' + ns + 'no-reset)').each(function(){
				var val = $(this).val();
				var def = $(this).attr('data-' + dataNS + 'default');
				//console.log('checking input: ' + $(this).attr('id') + ', val=' + val + ', def=' + def);
				if($(this).is(':checkbox,:radio')) {
					var isChecked = $(this).prop('checked');
					var newChecked = def == 1 || def == true;
					if(isChecked != newChecked) $(this).prop('checked', newChecked).trigger('change');
				}
				else {
					if(val != def) $(this).val(def).trigger('change');
				}
			});
		});
		
		$('#' + ns + 'tabs_setting_htabresponsive').on('change', function(){ var responsive = $(this).val() || 'accordion';  var acc = (responsive === 'accordion'); $('#' + ns + 'tabs_setting_hresponsiveaccordionscollapsedinitially').closest('tr')[acc ? 'show' : 'hide']()  }).trigger('change');

		$('#' + ns + 'tabs_setting_accordionicontype').on('change', function(){ 
			var type = $(this).val(); 
			$('#' + ns + 'tabs_setting_accordionicontwostateclosed,#' + ns + 'tabs_setting_accordionicontwostateopen').each(function(){
				$(this).closest('tr')[type == 'two_state' ? 'show' : 'hide']();
			});
			$('#' + ns + 'tabs_setting_accordioniconsinglestate').each(function(){
				$(this).closest('tr')[type == 'single_state' ? 'show' : 'hide']();
			});
		}).trigger('change');
				
				
		$('#' + ns + 'tabs_setting_accordionicontwostateclosed,#' + ns + 'tabs_setting_accordionicontwostateopen,#' + ns + 'tabs_setting_accordioniconsinglestate').each(function(){
			var $input = $(this).addClass('' + ns + 'svg-selector').addClass('' + ns + 'visually-hidden');
			var label = $('label[for="' + $input.attr('id') + '"]').text();
			var title = 'Select icon for "' + label + '"';
			var $preview = $('<span class="' + ns + 'svg-selector-preview"/>')
				.insertAfter($input);
			if(!$input.prop('disabled')) {
				$('<button type="button" class="' + ns + 'svg-selector-button button button-secondary"/>Select</button>')
					.attr('aria-label', title)
					.attr('title', title)
					//.prop('disabled', $input.prop('disabled'))
					.insertAfter($preview)
					.on('click', function(){
						$currentIconInput = $input;
						var $modal = getIconModal();
						MicroModal.show($modal.attr('id')); 
						$('#' + ns + '_custom_icon_input').val($currentIconInput.val());
					});
			}
			$input.on('change', function(){
			   $preview.html($input.val());
			}).trigger('change');
		});
	});

})(jQuery);