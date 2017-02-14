/**
 * AJAX Nette Framwork plugin for jQuery
 *
 * @copyright   Copyright (c) 2009 Jan Marek
 * @license     MIT
 * @link        http://nettephp.com/cs/extras/jquery-ajax
 * @version     0.2
 */

jQuery.extend({
	nette: {
		updateSnippet: function (id, html) {
			$("#" + id).html(html);
		},

		success: function (payload) {
			// redirect
			if (payload.redirect) {
				window.location.href = payload.redirect;
				return;
			}

			// snippets
			if (payload.snippets) {
				for (var i in payload.snippets) {
					jQuery.nette.updateSnippet(i, payload.snippets[i]);
				}
			}

			$('.main-ajax-spinner').removeClass('visible');
			/*
			if(typeof payload.messages !== 'undefined') {
				for (i = 0; i < payload.messages.length; i++) {
					switch(payload.messages[i].type) {
						case "success":
	 			   			toastr.success(payload.messages[i].message);
	 			   			break;
	 			   		case "info":
	 			   			toastr.info(payload.messages[i].message);
	 			   			break;
						case "error":
	 			   			toastr.error(payload.messages[i].message);
	 			   			break;
						case "warning":
	 			   			toastr.warning(payload.messages[i].message);
	 			   			break;
					}
				}
			}
			*/
		}
	}
});

jQuery.ajaxSetup({
	success: jQuery.nette.success,
	dataType: "json",
    beforeSend: function() {
    	$('.main-ajax-spinner').addClass('visible');
    },
});

$('body').on('submit', 'form.ajax', function( event ) {
	var form = $(this);
	var action = form.attr("action");

	$.ajax({
	    type: "POST",
	    url: action,
	    data: $(this).serialize(),
	    success: function(data) {                   
			$.nette.success(data);
			if(!form.hasClass("no-push-history")) {
				window.history.pushState(data, null, action);
			}
	    }
	});
	event.preventDefault();
});

$('body').on('click', 'a.ajax', function (event) {
	var href = this.href;
	
	$.get(this.href,function( data ) {
		$.nette.success(data);
		window.history.pushState(data, null, href);
	});
	
    event.preventDefault();
});
