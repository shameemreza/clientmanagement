$(function() {

	$('.barGraph').each(function () {
		$(this).find('.barGraph-bar').animate({
			width: $(this).attr('data-percent')
		}, 2000);
	});
	
	/** ******************************
	 * Password Show/Hide
	 ****************************** **/
	$('#hide2').hide();
	$('#show2').click(function(e) {
		e.preventDefault();
		$('#newPass').prop('type','text');
		$('#show2').hide();
		$('#hide2').show();
	});
	$('#hide2').click(function(e) {
		e.preventDefault();
		$('#newPass').prop('type','password');
		$('#hide2').hide();
		$('#show2').show();
	});
	
	/** ******************************
	 * Sidebar Lists
	 ****************************** **/
    var allPanels = $('.accordion > dd').hide();

    $('.accordion > dt > a').click(function () {
        $target = $(this).parent().next();
        if (!$target.hasClass('active')) {
            allPanels.removeClass('active').slideUp();
            $target.addClass('active').slideDown();
			$('.accordion > dt > a').find("i").removeClass('fa-angle-down');
			$('.accordion > dt > a').find("i").addClass('fa-angle-right');
        } else {
            $target.removeClass('active').slideUp();
        }
		$(this).find("i").toggleClass("fa-angle-right fa-angle-down");
        return false;
    });

});