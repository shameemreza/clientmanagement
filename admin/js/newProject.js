$(document).ready(function() {

	$('#clientId').change(function() {
		$('#clientName').val($('#clientId option:selected').html());
	});
	
	/** ******************************
    * Date Picker
    ****************************** **/
    $('#dueDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});

});