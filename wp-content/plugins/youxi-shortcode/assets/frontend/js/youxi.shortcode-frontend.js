;(function( $, window, document, undefined ) {

	"use strict";

	$.Youxi = $.Youxi || {};

	var setupMethods = {

		'leaflet_map': function( context ) {

			/* Make sure we have Leaflet */
			if( typeof L == 'undefined' || ! L.map ) {
				return;
			}

			/* Initialize Leaflet Maps */
			$( '.leaflet-map', context ).each(function() {

				/* Check if already initialized */
				var map = $.data( this, 'leaflet' );

				if( ! map ) {

					var options = $( this ).data( 'leaflet-options' ) || {};
					var marker, markers = options.markers || [];
					var accessToken = $( this ).data( 'mapbox-access-token' ) || '';

					delete options.markers;

					if( ! accessToken ) {
						return;
					}

					map = L.map( this, options );

					if( options.scaleControl ) {
						L.control.scale().addTo( map );
					}

					L.tileLayer( 'https://api.tiles.mapbox.com/v4/mapbox.streets/{z}/{x}/{y}.png?access_token={accessToken}', {
						attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery &copy; <a href="http://mapbox.com">Mapbox</a>', 
						accessToken: accessToken
					}).addTo( map );

					markers.forEach( function( markerData, index ) {
						marker = L.marker( [ markerData.lat, markerData.lng ] ).addTo( map );
						if( markerData.description ) {
							marker.bindPopup( markerData.description );
						}
					});

					$.data( this, 'leaflet', map );
				}
			});
		}, 

		'tooltip': function( context ) {
			
			/* [tooltip] shortcode */
			if( $.fn.tooltip ) {
				$( '[data-toggle="tooltip"]', context ).tooltip();
			}
		}
	};

	var teardownMethods = {

		'leaflet_map': function( context ) {

			$( '.leaflet-map', context ).each(function() {
				var map = $.data( this, 'leaflet' );
				map && map.remove();
				$.removeData( this, 'leaflet' );
			});
		}, 

		'tooltip': function( context ) {
			
			/* [tooltip] shortcode */
			if( $.fn.tooltip ) {
				$( '[data-toggle="tooltip"]', context ).tooltip( 'dispose' );
			}
		}
	};

	$.extend( $.Youxi, {

		Shortcode: {

			setup: function( context ) {

				$.each( setupMethods, function( id, fn ) {
					$.isFunction( fn ) && fn.call( null, context );
				});
			}, 

			teardown: function( context ) {

				$.each( teardownMethods, function( id, fn ) {
					$.isFunction( fn ) && fn.call( null, context );
				});
			}
		}
	});

	$(function() {
		$.Youxi.Shortcode.setup();
	});

}) ( jQuery, window, document );
