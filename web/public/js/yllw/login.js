$(document).ready(function() {
	$("form").submit(function(e) {
		e.preventDefault();

		var action = $(this).attr('action');

		var post = $(this).serialize();
		post += "&js=true";

		var submit = $("input[type=submit]", this);

		submit.addClass('processing').val('Checking');

		$.post(action, post).done(function(data) {
			if(data == 'fail') {
				submit.removeClass('processing').addClass('error').val('Invalid Combo');
				setTimeout(function() {
					submit.removeClass('error').val('Login');
				}, 1500);
			}

			if(data == 'success') {
				submit.removeClass('processing').removeClass('error').addClass('success').val('Success');
				window.location = submit.attr("data-process");
			}
		});
	})
})