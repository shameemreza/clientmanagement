$(function() {
	
	/** ******************************
    * Time Clock
    ****************************** **/
	var isRunning = $('#running').val();
	if (isRunning == '0') {
		if ($("#timetrack").hasClass("btn-warning")) {
			$("#timetrack").removeClass("btn-warning");
			$("#timetrack").addClass('btn-success');
		}
		if ($("#timetrack i").hasClass("fa fa-sign-out")) {
			$("#timetrack i").removeClass("fa fa-sign-out");
			$("#timetrack i").addClass('fa fa-sign-in');
		}
		$("#timetrack").addClass('btn-success');
		$("#timetrack i").addClass('fa fa-sign-in');
		$(".clock-status").html("Clocked Out");
		$("#timetrack span").html("Clock In");
	} else {
		if ($("#timetrack").hasClass("btn-success")) {
			$("#timetrack").removeClass("btn-success");
			$("#timetrack").addClass('btn-warning');
		}
		if ($("#timetrack i").hasClass("fa fa-sign-in")) {
			$("#timetrack i").removeClass("fa fa-sign-in");
			$("#timetrack i").addClass('fa fa-sign-out');
		}
		$("#timetrack").addClass('btn-warning');
		$("#timetrack i").addClass('fa fa-sign-out');
		$(".clock-status").html("Clocked In");
		$("#timetrack span").html("Clock Out");
	}
	
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
	
	/** ******************************
	 * Date Picker
	 ****************************** **/
	$('#startDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	$('#dueDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	$('#paymentDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});

});