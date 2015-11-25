$(function() {

	// Hide the Preview Box header and footer on page load
	$('.msgOptions, .panel-heading').hide();

	// Get the PM Page we are on
	var pmPage = $(this).find('#pmPage').val();

	$('.msgLink').click( function() {
		$('.msgQuip').hide();
		$('.panel-heading').show();

		var fromName = $(this).find('.name').html();
		var dateSent = $(this).find('.time').html();
		var msgSubject = $(this).find('.subject').html();
		var msgText = $(this).find('[name="msgTxt"]').val();
		var msgId = $(this).find('[name="messageId"]').val();
		var isRead = $(this).find('[name="toRead"]').val();

		$('.theSubject').show().html('<h3 class="panel-title">'+msgSubject+'</h3>');
		$('.msgContent').show().html(msgText.replace(/\\r\\n/g, "<br />"));

		// Inbox
		if (pmPage === 'inbox') {
			$('.whoFrom').show().html('<span class="label label-default preview-label">From: '+fromName+'  on: '+dateSent+'</span>');
			if (isRead === '0') {
				$('.msgOptions').show().html('<form action="" method="post"><input name="messageId" value="'+msgId+'" type="hidden" /><a data-toggle="modal" href="#reply'+msgId+'" class="btn btn-primary btn-sm btn-icon"><i class="fa fa-reply"></i> Reply</a> <button type="input" name="submit" value="markRead" class="btn btn-success btn-sm btn-icon"><i class="fa fa-check-square"></i> Mark as Read</button> <a data-toggle="modal" href="#delete'+msgId+'" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash-o"></i> Delete</a></form>');
				$('.msgContent').addClass('contentMargin');
			} else {
				$('.msgOptions').show().html('<form action="" method="post"><input name="messageId" value="'+msgId+'" type="hidden" /><a data-toggle="modal" href="#reply'+msgId+'" class="btn btn-primary btn-sm btn-icon"><i class="fa fa-reply"></i> Reply</a> <button type="input" name="submit" value="archive" class="btn btn-warning btn-sm btn-icon"><i class="fa fa-archive"></i> Archive</button> <a data-toggle="modal" href="#delete'+msgId+'" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash-o"></i> Delete</a></form>');
				$('.msgContent').addClass('contentMargin');
			}
		}
		// Sent Messages
		if (pmPage === 'sent') {
			$('.whoTo').show().html('<span class="label label-default preview-label">Sent to: '+fromName+'  on: '+dateSent+'</span>');
			$('.msgOptions').show().html('<a data-toggle="modal" href="#delete'+msgId+'" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash-o"></i> Delete</a>');
			$('.msgContent').addClass('contentMargin');
		}
		// Archived Messages
		if (pmPage === 'archive') {
			$('.whoFrom').show().html('<span class="label label-default preview-label">From: '+fromName+'  on: '+dateSent+'</span>');
			$('.msgOptions').show().html('<form action="" method="post"><input name="messageId" value="'+msgId+'" type="hidden" /><button type="input" name="submit" value="sendInbox" class="btn btn-info btn-sm btn-icon"><i class="fa fa-long-arrow-left"></i> Send to Inbox</button> <a data-toggle="modal" href="#delete'+msgId+'" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash-o"></i> Delete</a></form>');
			$('.msgContent').addClass('contentMargin');
		}
	});
	
	$('.compose').click( function() {
		$('.msgOptions, .panel-default').hide();
		$('.msgContent').removeClass('contentMargin');
	});

	$('.showinbox').click( function() {
		$('.panel-default, .msgQuip').show();
		$('.msgOptions, .panel-heading, .whoFrom, .msgContent').hide();
	});

	$('.showsent').click( function() {
		$('.panel-default, .msgQuip').show();
		$('.msgOptions, .panel-heading, .whoTo, .msgContent').hide();
	});
	
	$('.showarchive').click( function() {
		$('.panel-default, .msgQuip').show();
		$('.msgOptions, .panel-heading, .whoFrom, .msgContent').hide();
	});

});