/* 
 * Indicate if files have been select on input[type=file] elements
 * styled with label
 * From: https://tympanus.net/codrops/2015/09/15/styling-customizing-file-inputs-smart-way/
 */

 var fileUpload = (function (public) {
 	public = {};

 	public.initialize = function(index) {
 		var inputs = document.querySelectorAll('.inputfile');
 		Array.prototype.forEach.call( inputs, function(input) {
 			var label	 = input.nextElementSibling,
 				labelVal = label.innerHTML;

 			input.addEventListener('change', function(e) {
 				var fileName = '';
 				if (this.files && this.files.length > 1)
 					fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
 				else
 					fileName = e.target.value.split('\\').pop();

 				if (fileName)
 					label.querySelector('span').innerHTML = fileName;
 				else
 					label.innerHTML = labelVal;
 			});

 			// Firefox bug fix
 			input.addEventListener('focus', function() { input.classList.add('has-focus'); });
 			input.addEventListener('blur', function() { input.classList.remove('has-focus'); });
 		});
 	};

 	return public;
 }(fileUpload || {}));

;( function (document, window, index)
{
	var inputs = document.querySelectorAll( '.inputfile' );
	Array.prototype.forEach.call( inputs, function( input )
	{
		var label	 = input.nextElementSibling,
			labelVal = label.innerHTML;

		input.addEventListener( 'change', function( e )
		{
			var fileName = '';
			if( this.files && this.files.length > 1 )
				fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
			else
				fileName = e.target.value.split( '\\' ).pop();

			if( fileName )
				label.querySelector( 'span' ).innerHTML = fileName;
			else
				label.innerHTML = labelVal;
		});

		// Firefox bug fix
		input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
		input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
	});
}(document, window, 0));