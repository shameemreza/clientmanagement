$(function() {

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

});