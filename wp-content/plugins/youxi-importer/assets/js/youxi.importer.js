
;(function( $, window, document, undefined ) {

	"use strict";

	var ImporterSettings = {};

	var Importer = function( element ) {
		this.element = $( element );
		this.init();
	}

	Importer.prototype = {

		init: function() {

			// Sort all demo tasks by priority
			$.each( ImporterSettings.demos || {}, function( demoId, demoTasks ) {
				demoTasks.sort( function( a, b ) { return ( a.priority || 10 ) - ( b.priority || 10 ); } );
			});

			// Find all import buttons
			this.importButtons = this.element.find( '.demo-content .demo-actions button' );

			// Bind import button event handler
			this.importButtons.on( 'click.importer', $.proxy( this.onImportButtonClick, this ) );

			window.onbeforeunload = $.proxy( function() {
				if( this.isImporting ) {
					return true;
				}
			}, this );
		}, 

		debug: function( message ) {
			console.log( message );
		}, 

		feedback: function( message ) {
			$( '.more-details', this.currentDemoContainer ).html( message );
		}, 

		onImportButtonClick: function( event ) {

			var demoContainer = $( event.target ).closest( '.demo-content' );

			if( ! this.isImporting && demoContainer.length ) {
				this.import( demoContainer[0] );
			}

			event.preventDefault();
		}, 

		import: function( demoContainer ) {

			var that = this;
			var demoId = $( demoContainer ).data( 'demo-id' );

			if( ImporterSettings.demos && ImporterSettings.demos.hasOwnProperty( demoId ) ) {

				var importQueue = $({});
				var taskFailures = 0;
				var taskCount = ImporterSettings.demos[ demoId ].length;

				var taskFailureCallback = function( jqXHR, textStatus, errorThrown ) {
					taskFailures++;
					that.debug( textStatus + ( errorThrown ? ( ': ' + errorThrown ) : '' ) );
				};

				var taskDoneCallback = function( response ) {
					if( ! response.success ) {
						taskFailures++;
						that.debug( response.data );
					}
				};

				this.currentDemoContainer = demoContainer;
				this.beginImport();

				$.each( ImporterSettings.demos[ demoId ], function( n, task ) {

					var importerTask = ImporterTask.get( task );

					importerTask.on( 'notify', that.feedback );
					importerTask.on( 'notifyError', that.debug );

					importQueue.queue( function( next ) {

						importerTask.run()
							.done( taskDoneCallback )
							.fail( taskFailureCallback )
							.then( next, next );
					});
				});

				importQueue.queue( function( next ) {
					var message = ImporterSettings.completionMessage.replace( '{count}', taskFailures.toString() );
					that.feedback.call( that, message );
					next();
				});

				importQueue.queue( $.proxy( this.endImport, this ) );
			}
		}, 

		beginImport: function() {
			this.importButtons.prop( 'disabled', this.isImporting = true );
			$( this.currentDemoContainer ).addClass( 'is-importing' );
		}, 

		endImport: function() {

			var that = this;

			setTimeout( function() {

				that.importButtons.prop( 'disabled', that.isImporting = false );
				$( that.currentDemoContainer ).removeClass( 'is-importing' );
				$( that.currentDemoContainer ).find( '.more-details' ).empty();

			}, ImporterSettings.importFinishTimeout );
		}
	};

	var ImporterTask = function( task ) {
		this.init( task );
	}

	$.extend( ImporterTask, {
		get: function( task ) {
			if( 'wordpress' == task.id ) {
				return new ImporterTask.WP( task );
			}
			return new ImporterTask( task );
		}
	});

	ImporterTask.prototype = {

		init: function( task ) {
			this.task = task;
			this.messages = this.task.messages || {};
			this.dfd = $.Deferred();

			delete this.task.messages;
		}, 

		ajaxProps: function( task_args ) {
			return {
				action: ImporterSettings.ajaxAction, 
				_wpnonce: this.task.nonce, 
				demo_id: this.task.demo_id, 
				task_id: this.task.id, 
				task_args: task_args || this.task.params || {}
			}
		}, 

		run: function() {

			this.trigger( 'notify', this.messages.status || '' );

			$.post( ImporterSettings.ajaxUrl, this.ajaxProps() )
				.done( this.dfd.resolve )
				.fail( this.dfd.reject );

			return this.dfd.promise();
		}, 

		/**
		 * MicroEvent - to make any js object an Event Emitter
		 */
		on: function( event, fn ) {
			this._events = this._events || {};
			this._events[ event ] = this._events[ event ] || [];
			this._events[ event ].push( fn );
		}, 

		trigger: function( event ) {
			this._events = this._events || {};
			if( event in this._events ) {
				for( var i = 0; i < this._events[ event ].length; i++ ){
					this._events[ event ][ i ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
				}
			}
		}
	};

	/* Make ImporterTaskHandler extendable using Backbone's inheritance method */
	ImporterTask.extend = function( protoProps ) {
		var parent = this;
		var child = function(){ return parent.apply(this, arguments); };

		// Set the prototype chain to inherit from `parent`, without calling
		// `parent`'s constructor function.
		var Surrogate = function(){};
		Surrogate.prototype = parent.prototype;
		child.prototype = new Surrogate;

		// Add prototype properties (instance properties) to the subclass
		$.extend( child.prototype, protoProps );

		return child;
	}

	ImporterTask.WP = ImporterTask.extend({

		run: function() {

			var that = this;
			var wpTaskArgs = {
				post_orphans: {}, 
				url_remap: {}, 
				processed_posts: {}
			};
			var wpImportQueue = $({});
			var params = that.task.params || {};
			var attachments = params.attachments || [];
			var attachmentsProcessed = 0;
			var attachmentsCount = attachments.length;

			var attachmentDoneCallback = function( response ) {

				var message;

				if( response.success ) {

					try {

						var jsonData = JSON.parse( response.data );

						$.extend( true, wpTaskArgs, {
							'post_orphans': jsonData.post_orphans,   
							'url_remap': jsonData.url_remap, 
							'processed_posts': jsonData.processed_posts
						});

						if( jsonData.message ) {
							message = jsonData.message;
						}

					} catch( e ) {
						message = e.message;
					}

				} else {
					message = response.data;
				}

				if( message ) {
					that.trigger( 'notifyError', message );
				}
			};

			var attachmentFailCallback = function( jqXHR, textStatus, errorThrown ) {
				that.trigger( 'notifyError', textStatus + ( errorThrown ? ( ': ' + errorThrown ) : '' ) );
			};

			// Begin importing attachments
			$.each( attachments, function( n, attachment ) {

				wpImportQueue.queue( function( next ) {

					var message, ajaxProps = that.ajaxProps({
						'import': 'attachment', 
						'attachment': attachment, 
						'base_url': params.base_url
					});

					message = that.messages.attachmentStatus || '';
					message = message.replace( '{current}', ++attachmentsProcessed );
					message = message.replace( '{total}', attachmentsCount );

					that.trigger( 'notify', message );

					$.post( ImporterSettings.ajaxUrl, ajaxProps )
						.done( attachmentDoneCallback )
						.fail( attachmentFailCallback )
						.then( next, next );
				});
			});

			// Queue the WordPress import task
			wpImportQueue.queue( function() {
				that.trigger( 'notify', that.messages.wpStatus || '' );
				$.post( ImporterSettings.ajaxUrl, that.ajaxProps({ 'import': 'wp', 'wp': wpTaskArgs }) )
					.done( that.dfd.resolve )
					.fail( that.dfd.reject );
			});

			return that.dfd.promise();
		}
	});

	$( function() {
		$.extend( ImporterSettings, _youxiImporterSettings );
		$( '.demo-browser' ).each( function() {
			new Importer( this );
		});
	});

})( jQuery, window, document );
