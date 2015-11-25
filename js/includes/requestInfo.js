$(function() {

	// Accept Project System options
	$("#acceptDecline").change(function() {
		if ($('#acceptDecline').val() !== '1') {
			// Hide if declining
			$('#acceptProj').slideUp('slow');
		} else {
			// Show if accepting
			$('#acceptProj').slideDown('slow');
		}
	});

	// Hide on Page Load
	$('#acceptProj').hide();

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

});