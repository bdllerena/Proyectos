// A self-executing anonymous function,
// standard technique for developing jQuery plugins.

(function($){
	
	$.fn.splashScreen = function(settings){
		
		// Providing default options:
		
		settings = $.extend({
		},settings);
		
		return this.each(function() {
			console.log($(this).data('link'));
			innerhtml = $(this).html();

			var target = $($(this).data('target'));
			var splashScreen = $('<div>',{
				id	: 'splashScreen',
				css:{
					backgroundImage		: 'url(' + $(this).data('img') + ')',
					backgroundPosition	: 'center 100px',
					backgroundColor: 'rgba(1,0,0,0.5)',
					height				: $(document).height()+200,
					display: 'none'
				}
			});

			$('body').append(splashScreen);
			splashScreen.fadeIn('slow');
		
			var splashExit = $('<a>', {
				id: '', href: $(this).data('link'),
				css:{
					position: 'absolute',
					display		: 'block',
					top: (target.offset().top + $(this).data('top'))+'px',
					left: (target.offset().left + $(this).data('left'))+'px',
					width: $(this).data('width')+'px',
					height: $(this).data('height')+'px',
					border: '0px solid black'
				}
			});
//			splashScreen.append(splashExit);

			var splashText = $('<div>', {
				id: '',
				css:{
					margin: 'auto',
					'margin-top': '100px',
					height: '250px',
					width: '600px',
					border: '2px solid #888',
					'border-radius': '8px',
					'background-color': '#f0f0f0',
					'padding': '40px'
				}
			});
			splashScreen.append(splashText);

			splashText.append(innerhtml);

			splashScreen.click(function(){
				splashScreen.fadeOut('slow');
			});
    });

		return;
	}
	
})(jQuery);
