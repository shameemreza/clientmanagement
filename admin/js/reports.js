$(document).ready(function() {

	/** ********************************************
    * Payments Report - Specific Client
    ******************************************** **/
	$('#client').change(function() {
		$('#clientFullName').val($('#client option:selected').html());
	});
	$('#fromDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	$('#toDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});

	/** ********************************************
    * Projects Report - Payments by Specific Project
    ******************************************** **/
	$('#project').change(function() {
		$('#projectFullName').val($('#project option:selected').html());
	});


	/** ********************************************
    * Project Tasks Report - Specific Project
    ******************************************** **/
	$('#task').change(function() {
		$('#taskFullName').val($('#task option:selected').html());
	});


	/** ********************************************
    * Payment Reports - All Payments Received by Date
    ******************************************** **/
	$('#payFromDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	$('#payToDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	
	/** ********************************************
    * Invoice Reports - All Invoices Paid by Date
    ******************************************** **/
	$('#invFromDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	$('#invToDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	
	/** ********************************************
    * Time Logs
    ******************************************** **/
	$('#manager').change(function() {
		$('#fullAdminName').val($('#manager option:selected').html());
	});
	$('#timeFromDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	$('#timeToDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	
	$('#timeProj1').change(function() {
		$('#projClientName').val($('#timeProj1 option:selected').html());
	});
	$('#timeProj2').change(function() {
		$('#clientProjName').val($('#timeProj2 option:selected').html());
	});
	$('#projTimeFromDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	$('#projTimeToDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	
	// Set the Project Selects - Only allow one or the other
	$('#timeProj1').change(function() {
		$('#timeProj2 option:first').prop('selected',  true);
	});
	
	$('#timeProj2').change(function() {
		$('#timeProj1 option:first').prop('selected',  true);
	});

	/** ********************************************
    * Manager Reports - Assigned Projects
    ******************************************** **/
	$('#theManager').change(function() {
		$('#theManagerName').val($('#theManager option:selected').html());
	});
	

});