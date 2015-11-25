$(function() {
	var a = 0;
	
	$('[name="alertStart"]').each(function() {
		$('#alertStart_'+a+'').datetimepicker({
			format: 'yyyy-mm-dd',
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			minView: 2,
			forceParse: 0
		});
		$('#alertExpires_'+a+'').datetimepicker({
			format: 'yyyy-mm-dd',
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			minView: 2,
			forceParse: 0
		});
		a++;
	});
	
	$('#newAlertStart').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	$('#newAlertExpires').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});

});