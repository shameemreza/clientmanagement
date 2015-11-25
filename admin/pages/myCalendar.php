<?php
	$fullcalendar = 'true';
	$calinclude = 'true';
	$datePicker = 'true';
	$colorPicker = 'true';
	$jsFile = 'calendar';

	// Add New Event
	if (isset($_POST['submit']) && $_POST['submit'] == 'newEvent') {
		// Validations
		if($_POST['startDate'] == '') {
			$msgBox = alertBox($eventDateReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['eventTitle'] == '') {
			$msgBox = alertBox($eventTitleReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			$isPublic = $mysqli->real_escape_string($_POST['isPublic']);
			$dateOfEvent = $mysqli->real_escape_string($_POST['startDate']);
			$timeOfEvent = $mysqli->real_escape_string($_POST['eventTime']);
			$startDate = $dateOfEvent.' '.$timeOfEvent.':00';
			$endOfEvent = $mysqli->real_escape_string($_POST['endDate']);
			$endTimeOfEvent = $mysqli->real_escape_string($_POST['endTime']);
			$endDate = $endOfEvent.' '.$endTimeOfEvent.':00';
			$eventTitle = $mysqli->real_escape_string($_POST['eventTitle']);
			$eventColor = $mysqli->real_escape_string($_POST['eventColor']);
			$eventDesc = $mysqli->real_escape_string($_POST['eventDesc']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									adminevents(
										adminId,
										isPublic,
										startDate,
										endDate,
										eventTitle,
										eventDesc,
										eventColor
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('sssssss',
								$adminId,
								$isPublic,
								$startDate,
								$endDate,
								$eventTitle,
								$eventDesc,
								$eventColor
			);
			$stmt->execute();
			$msgBox = alertBox($newEventSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['startDate'] = $_POST['eventTime'] = $_POST['endDate'] = $_POST['endTime'] = $_POST['eventTitle'] = $_POST['eventDesc'] = $_POST['eventColor'] = '';
			$stmt->close();
		}
	}

	// Edit Event
	if (isset($_POST['submit']) && $_POST['submit'] == 'editEvent') {
		// Validations
		if($_POST['eventTitle'] == '') {
			$msgBox = alertBox($eventTitleReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			$isclient = $mysqli->real_escape_string($_POST['isclient']);
			$eventId = $mysqli->real_escape_string($_POST['eventId']);
			$dateOfEvent = $mysqli->real_escape_string($_POST['startDate']);
			$timeOfEvent = $mysqli->real_escape_string($_POST['eventTime']);
			$startDate = $dateOfEvent.' '.$timeOfEvent.':00';
			$endOfEvent = $mysqli->real_escape_string($_POST['endDate']);
			$endTimeOfEvent = $mysqli->real_escape_string($_POST['endTime']);
			$endDate = $endOfEvent.' '.$endTimeOfEvent.':00';
			$eventTitle = $mysqli->real_escape_string($_POST['eventTitle']);
			$eventColor = $mysqli->real_escape_string($_POST['eventColor']);
			$eventDesc = $mysqli->real_escape_string($_POST['eventDesc']);

			if ($isclient == '') {
				$stmt = $mysqli->prepare("
									UPDATE
										adminevents
									SET
										startDate = ?,
										endDate = ?,
										eventTitle = ?,
										eventColor = ?,
										eventDesc = ?
									WHERE
										admineventId = ?
				");
				$stmt->bind_param('ssssss',
									$startDate,
									$endDate,
									$eventTitle,
									$eventColor,
									$eventDesc,
									$eventId

				);
				$stmt->execute();
				$stmt->close();
			} else {
				$stmt = $mysqli->prepare("
									UPDATE
										clientevents
									SET
										startDate = ?,
										endDate = ?,
										eventTitle = ?,
										eventColor = ?,
										eventDesc = ?
									WHERE
										clienteventId = ?
				");
				$stmt->bind_param('ssssss',
									$startDate,
									$endDate,
									$eventTitle,
									$eventColor,
									$eventDesc,
									$eventId

				);
				$stmt->execute();
				$stmt->close();
			}
			$msgBox = alertBox($eventUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['startDate'] = $_POST['eventTime'] = $_POST['endDate'] = $_POST['endTime'] = $_POST['eventTitle'] = $_POST['eventDesc'] = $_POST['eventColor'] = '';
		}
	}

	// Delete Event
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteEvent') {
		$isclient = $mysqli->real_escape_string($_POST['isclient']);
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);

		if ($isclient == '') {
			$stmt = $mysqli->prepare("DELETE FROM adminevents WHERE admineventId = ?");
			$stmt->bind_param('s', $deleteId);
			$stmt->execute();
			$stmt->close();
		} else {
			$stmt = $mysqli->prepare("DELETE FROM clientevents WHERE clienteventId = ?");
			$stmt->bind_param('s', $deleteId);
			$stmt->execute();
			$stmt->close();
		}
		$msgBox = alertBox($eventDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }

	include 'includes/navigation.php';
?>
<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>
	<div id="calendar"></div>
	<p class="cal-quip text-muted">Click on an Event Title for more information &amp; options.</p>
</div>

<div id="" class="modal fade viewEvent" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><span class="event-title"></span></h4>
			</div>
			<div class="modal-body event-padding">
				<p class="event-desc"></p>
			</div>
			<div class="modal-footer">
				<div class="event-actions"></div>
			</div>
		</div>
	</div>
</div>

<div id="" class="modal fade editEvent" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Edit Event <span class="event-modal-title"></span></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="startDate">Start Date</label>
								<input type="text" class="form-control" name="startDate" id="editstartDate" required="" value="" />
								<span class="help-block"><?php echo $dateFormat; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventTime">Start Time</label>
								<input type="text" class="form-control" name="eventTime" id="editeventTime" value="" />
								<span class="help-block"><?php echo $eventTimeFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="endDate">End Date</label>
								<input type="text" class="form-control" name="endDate" id="editendDate" required="" value="" />
								<span class="help-block"><?php echo $dateFormat; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="endTime">End Time</label>
								<input type="text" class="form-control" name="endTime" id="editendTime" value="" />
								<span class="help-block"><?php echo $eventTimeFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventTitle"><?php echo $eventTitleField; ?></label>
								<input type="text" class="form-control titleField" name="eventTitle" required="" maxlength="50" value="" />
								<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventColor">Event Color</label>
								<div class="input-group colorpicker-component editEvent-cp">
									<input type="text" class="form-control" name="eventColor" id="eventColor" value="" />
									<span class="input-group-addon"><i></i></span>
								</div>
								<span class="help-block">Hexadecimal Format (ie. #e96f50).</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="eventDesc"><?php echo $eventDescField; ?></label>
						<textarea class="form-control descField" name="eventDesc" rows="4"></textarea>
						<span class="help-block"><?php echo $eventDescFieldHelp; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="eventId" class="event-id" value="" />
					<input type="hidden" name="isclient" class="isclient" value="" />
					<button type="input" name="submit" value="editEvent" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>

<div id="" class="modal fade deleteEvent" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="post">
				<div class="modal-body">
					<p class="lead">Are you sure you want to DELETE the event <span class="event-modal-title"></span>?</p>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="deleteId" class="event-id" value="" />
					<input type="hidden" name="isclient" class="isclient" value="" />
					<button type="input" name="submit" value="deleteEvent" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>

<div id="newEvent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newEvent" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $addNewEventModalTitle; ?></h4>
			</div>

			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="isPublic">Make this a Public Event?</label>
						<select class="form-control" name="isPublic" id="isPublic">
							<option value="0"><?php echo $noBtn; ?></option>
							<option value="1"><?php echo $yesBtn; ?></option>
						</select>
						<span class="help-block"><?php echo $shareEventFieldHelp; ?></span>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="startDate">Start Date</label>
								<input type="text" class="form-control" name="startDate" id="newstartDate" required="" value="<?php echo isset($_POST['startDate']) ? $_POST['startDate'] : ''; ?>" />
								<span class="help-block"><?php echo $dateFormat; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventTime">Start Time</label>
								<input type="text" class="form-control" name="eventTime" id="neweventTime" value="<?php echo isset($_POST['eventTime']) ? $_POST['eventTime'] : ''; ?>" />
								<span class="help-block"><?php echo $eventTimeFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="endDate">End Date</label>
								<input type="text" class="form-control" name="endDate" id="newendDate" required="" value="<?php echo isset($_POST['endDate']) ? $_POST['endDate'] : ''; ?>" />
								<span class="help-block"><?php echo $dateFormat; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="endTime">End Time</label>
								<input type="text" class="form-control" name="endTime" id="newendTime" value="<?php echo isset($_POST['endTime']) ? $_POST['endTime'] : ''; ?>" />
								<span class="help-block"><?php echo $eventTimeFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventTitle"><?php echo $eventTitleField; ?></label>
								<input type="text" class="form-control" name="eventTitle" required="" maxlength="50" value="<?php echo isset($_POST['eventTitle']) ? $_POST['eventTitle'] : ''; ?>" />
								<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventColor">Event Color</label>
								<div class="input-group colorpicker-component newEvent-cp">
									<input type="text" class="form-control" name="eventColor" />
									<span class="input-group-addon"><i></i></span>
								</div>
								<span class="help-block">Hexadecimal Format (ie. #e96f50).</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="eventDesc"><?php echo $eventDescField; ?></label>
						<textarea class="form-control" name="eventDesc" rows="4"><?php echo isset($_POST['eventDesc']) ? $_POST['eventDesc'] : ''; ?></textarea>
						<span class="help-block"><?php echo $eventDescFieldHelp; ?></span>
					</div>
				</div>

				<div class="modal-footer">
					<button type="input" name="submit" value="newEvent" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> Save New Event</button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>