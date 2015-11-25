// Fix for the following known Bootstrap bugs
// 		https://github.com/twbs/bootstrap/issues/10044
// 		https://github.com/twbs/bootstrap/issues/5566
// 		https://github.com/twbs/bootstrap/pull/7692
// 		https://github.com/twbs/bootstrap/issues/8423
// 		https://github.com/twbs/bootstrap/issues/7318
// 		https://github.com/twbs/bootstrap/issues/8423
if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
	document._oldGetElementById = document.getElementById;
	document.getElementById = function(id) {
		if(id === undefined || id === null || id === '') {
			return undefined;
		}
		return document._oldGetElementById(id);
	};
}

/**
 * Function to generate a Random Password
 **/
function generatePassword(limit) {
	limit = limit || 6;
	var password = '';
	// You can add or remove any characters you wish between the two single quote marks (')
	// Do NOT use singe quote marks in your characters list (')
	var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!"£$&=^*#_-@+,.';
	var list = chars.split('');
	var len = list.length,
		i = 0;
	do {
		i++;
		var index = Math.floor(Math.random() * len);
		password += list[index];
	}
	while (i < limit);
	// Return the newly generated password
	return password;
}

$(document).ready(function() {

	/** ******************************
	 * Header Notifications
	 ****************************** **/
	$('#mail, #tasks').slimscroll({
		height: '355px',
		width: '310px'
	});
	
	/** ******************************
	 * Current Time
	 ****************************** **/
	setInterval(function() {
		var date = new Date(),
		time = date.toLocaleTimeString();
		$(".clock").html(time);
	}, 1000);
	 
	/** ******************************
	 * Alert Message Boxes
	 ****************************** **/
    $('.alertMsg .alert-close').each(function() {
        $(this).click(function(e) {
            e.preventDefault();
            $(this).parent().fadeOut("slow", function() {
                $(this).addClass('hidden');
            });
        });
    });

	/** ******************************
	* Activate Tool-tips
	****************************** **/
    $("[data-toggle='tooltip']").tooltip();

	/** ******************************
	* Activate Popovers
	****************************** **/
	$("[data-toggle='popover']").popover();

	/** ******************************
	* Side Bar Nav
	****************************** **/
	$('[data-toggle=collapse]').click(function(e){
		e.preventDefault();
		$(this).find("i").toggleClass("fa-plus fa-minus");
	});

	$('.collapse').on('show', function() {
		$('.collapse').each(function(){
			if ($(this).hasClass('in')) {
				$(this).collapse('toggle');
			}
		});
	})

});