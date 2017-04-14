;(function( $ ) {

	$(function() {

		if( $.fn.wpColorPicker ) {
			$( '.youxi-taxonomy-color-field' ).wpColorPicker();
		}

		if( wp && wp.media ) {

			$( '.youxi-taxonomy-image-field' ).each( function() {

				var attachment, selection, 
					$control = $( this ), 
					$remove = $( '.youxi-taxonomy-image-field__remove', this ), 
					$image  = $( '.youxi-taxonomy-image-field__img', this ), 
					$input  = $( '.youxi-taxonomy-image-field__input', this );

				var frame = wp.media({
					states: [
						new wp.media.controller.Library({
							library:   wp.media.query({ type: 'image' }),
							multiple:  false,
							date:      false
						})
					]
				});

				frame.on( 'select', function() {

					selection = frame.state().get( 'selection' );

					if( selection.length && ( attachment = selection.at( 0 ) ) ) {

						$input.val( attachment.id );

						if( attachment.attributes ) {

							if( attachment.attributes.sizes && attachment.attributes.sizes.thumbnail ) {
								$image.html( '<img src="' + attachment.attributes.sizes.thumbnail.url + '">' );
							} else {
								$image.html( '<img src="' + attachment.attributes.url + '">' );
							}

							$remove.show();
						}
					}
				});

				$image.on( 'click', function() {
					frame.open();
				});

				$remove.on( 'click', function() {
					$image.html( '' );
					$input.val( '' );
					$remove.hide();
				});
			});
		}
	});
	
}) ( jQuery, window, document );
