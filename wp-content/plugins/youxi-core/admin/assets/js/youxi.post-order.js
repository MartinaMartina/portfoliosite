/**
 * Youxi Post Order JS
 *
 * This script contains the initialization code for the post order screen.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	$( function() {

		if( $.fn.sortable ) {

			$( '.youxi-post-order-items-holder' ).sortable({

				update: function( event, ui ) {

					var orderResult = $( this ).sortable( 'toArray', { attribute: 'data-post-id' } );

					wp.ajax.post( 'youxi-post-order-save', {
						menu_order: orderResult, 
						nonce: _youxiPostOrder.nonce
					}).done( $.noop ).fail( console.log );
				}
			});
		}
	});

})( jQuery, window, document );