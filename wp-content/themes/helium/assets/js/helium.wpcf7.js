(function($) {

	var old_wpcf7InitForm = $.fn.wpcf7InitForm;
	$.fn.wpcf7InitForm = function() {
		// Move the response output to the top of the form
		var $responseOutput = $(this).find('div.wpcf7-response-output');
		$responseOutput.prependTo( $responseOutput.closest( '.wpcf7-form' ) );

		old_wpcf7InitForm.apply( this, arguments );
	};

	var old_wpcf7ClearResponseOutput = $.fn.wpcf7ClearResponseOutput;
	$.fn.wpcf7ClearResponseOutput = function() {
		this.find('div.wpcf7-response-output')
			.removeClass('alert-danger alert-success alert-warning alert-info');
		return old_wpcf7ClearResponseOutput.apply( this, arguments );
	};

	var old_wpcf7AjaxSuccess = $.wpcf7AjaxSuccess;
	$.wpcf7AjaxSuccess = function(data, status, xhr, $form) {
		if (! $.isPlainObject(data) || $.isEmptyObject(data))
			return;

		old_wpcf7AjaxSuccess.apply( this, arguments );

		var $responseOutput = $form.find('div.wpcf7-response-output');

		// Remove Bootstrap .has-danger class
		$( '.has-danger', $form ).removeClass('has-danger');

		if (data.invalids) {
			$.each(data.invalids, function(i, n) {
				// Bootstrap .has-danger class
				$form.find(n.into).addClass('has-danger');
			});
		}

		if( $responseOutput.hasClass( 'wpcf7-validation-errors' ) ) {
			$responseOutput.addClass( 'alert-danger' );
		} else if( $responseOutput.hasClass( 'wpcf7-mail-sent-ok' ) ) {
			$responseOutput.addClass( 'alert-success' );
		} else if( $responseOutput.hasClass( 'wpcf7-spam-blocked' ) ) {
			$responseOutput.addClass( 'alert-info' );
		} else if( $responseOutput.hasClass( 'wpcf7-mail-sent-ng' ) ) {
			$responseOutput.addClass( 'alert-warning' );
		}
	}

	$.fn.wpcf7Placeholder = function() {
		// Remove placeholder plugin
		return this;
	};

	var old_wpcf7NotValidTip = $.fn.wpcf7NotValidTip;
	$.fn.wpcf7NotValidTip = function(message) {
		old_wpcf7NotValidTip.apply( this, arguments );
		return this.each(function() {
			$(this).find('span.wpcf7-not-valid-tip').addClass('small text-danger');
		});
	};

})(jQuery);