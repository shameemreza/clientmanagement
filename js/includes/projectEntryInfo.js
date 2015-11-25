$(function() {
	
	/** ******************************
	 * Password Show/Hide
	 ****************************** **/
	$('#hide, #hide1').hide();
	var hideShow = $('#hideShow').val();

	$('#show').click(function(e) {
		e.preventDefault();
		$('#entryPass').prop('type','text');
		$('#show').hide();
		$('#hide').show();
	});
	$('#hide').click(function(e) {
		e.preventDefault();
		$('#entryPass').prop('type','password');
		$('#hide').hide();
		$('#show').show();
	});
	
	$('#show1').click(function(e) {
		e.preventDefault();
		$('.showPass').html(hideShow);
		$('#show1').hide();
		$('#hide1').show();
	});
	$('#hide1').click(function(e) {
		e.preventDefault();
		$('.showPass').html('********');
		$('#hide1').hide();
		$('#show1').show();
	});

});