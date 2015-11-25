<?php
	// Get Events
	$query = "SELECT
				clientevents.clienteventId, clientevents.clientId, clientevents.isShared,
				DATE_FORMAT(clientevents.startDate,'%Y-%m-%d') AS startsOnDate, DATE_FORMAT(clientevents.startDate,'%H:%i') AS startTime,
				clientevents.startDate, DATE_FORMAT(clientevents.startDate,'%m') AS startMonth, DATE_FORMAT(clientevents.startDate,'%H:%i') AS timeStart,
				DATE_FORMAT(clientevents.startDate,'%M %d, %Y') AS displayDate, DATE_FORMAT(clientevents.startDate,'%l:%i %p') AS displaystart,
				DATE_FORMAT(clientevents.endDate,'%M %d, %Y') AS displayendDate, DATE_FORMAT(clientevents.endDate,'%l:%i %p') AS displayend,
				DATE_FORMAT(clientevents.endDate,'%Y-%m-%d') AS endsOnDate, DATE_FORMAT(clientevents.endDate,'%H:%i') AS endTime,
				clientevents.endDate, DATE_FORMAT(clientevents.endDate,'%m') AS endMonth, DATE_FORMAT(clientevents.endDate,'%Y, %m, %d') AS dateEnd,
				clientevents.eventTitle, clientevents.eventDesc, clientevents.eventColor,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS postedBy
			FROM
				clientevents
				LEFT JOIN clients ON clientevents.clientId = clients.clientId
			WHERE
				clientevents.clientId = ".$clientId;
	$res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	
	$qry = "SELECT
				adminevents.admineventId, adminevents.adminId, adminevents.isPublic,
				DATE_FORMAT(adminevents.startDate,'%Y-%m-%d') AS startsOnDate, DATE_FORMAT(adminevents.startDate,'%H:%i') AS startTime,
				adminevents.startDate, DATE_FORMAT(adminevents.startDate,'%m') AS startMonth, DATE_FORMAT(adminevents.startDate,'%H:%i') AS timeStart,
				DATE_FORMAT(adminevents.startDate,'%M %d, %Y') AS displayDate, DATE_FORMAT(adminevents.startDate,'%l:%i %p') AS displaystart,
				DATE_FORMAT(adminevents.endDate,'%M %d, %Y') AS displayendDate, DATE_FORMAT(adminevents.endDate,'%l:%i %p') AS displayend,
				DATE_FORMAT(adminevents.endDate,'%Y-%m-%d') AS endsOnDate, DATE_FORMAT(adminevents.endDate,'%H:%i') AS endTime,
				adminevents.endDate, DATE_FORMAT(adminevents.endDate,'%m') AS endMonth, DATE_FORMAT(adminevents.endDate,'%Y, %m, %d') AS dateEnd,
				adminevents.eventTitle, adminevents.eventDesc, adminevents.eventColor,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS postedBy
			FROM
				adminevents
				LEFT JOIN admins ON adminevents.adminId = admins.adminId
			WHERE
				adminevents.isPublic = 1";
	$result = mysqli_query($mysqli, $qry) or die('-2'.mysqli_error());
?>
<script type="text/javascript">
	$(function() {
		var date = new Date();
		var d = date.getDate(),
			m = date.getMonth(),
			y = date.getFullYear();
		$('#calendar').fullCalendar({
			header: {
				left: 'prevYear,prev,next,nextYear today',
				center: 'title',
				right: 'newEvent month,agendaWeek,agendaDay'
			},
			buttonText: {
				prev: "<span class='fa fa-angle-left'></span>",
				next: "<span class='fa fa-angle-right'></span>",
				prevYear: "<span class='fa fa-angle-double-left'></span>",
				nextYear: "<span class='fa fa-angle-double-right'></span>",
				today: '<?php echo $todayLink; ?>',
				newEvent: "<?php echo $newEventLink; ?>",
				month: '<?php echo $monthLink; ?>',
				week: '<?php echo $weekLink; ?>',
				day: '<?php echo $dayLink; ?>'
			},
			events: [
			<?php
				$delim = '';
				while($row = mysqli_fetch_assoc($res)) {

					// Months start at 0 - so subtract 1 month from the query dates
					$eventStartMnth = $row['startMonth'];
					$eventStartMonth = --$eventStartMnth;
					$eventDate = strtotime($row['startDate']);
					$eventDate = date('Y', $eventDate).', '.$eventStartMonth.', '.date('d, H, i', $eventDate);

					$eventEndMnth = $row['endMonth'];
					$eventEndMonth = --$eventEndMnth;
					$eventEnd = strtotime($row['endDate']);
					$eventEnd = date('Y', $eventEnd).', '.$eventEndMonth.', '.date('d, H, i', $eventEnd);

					// Check for an All Day event
					if ($row['timeStart'] != '00:00') { $allDay = 'allDay: false,'; } else { $allDay = 'allDay: true,'; }
					if ($row['startTime'] == '00:00') { $startTime = ''; } else { $startTime = $row['startTime']; }
					if ($row['endTime'] == '00:00') { $endTime = ''; } else { $endTime = $row['endTime']; }
					// Check for an End Date
					if ($row['dateEnd'] != '0000, 00, 00') { $endsOn = "end: new Date(".$eventEnd."),"; } else { $endsOn = ""; }
					// Set the Times to Display
					if ($row['displaystart'] != '12:00 AM') { $displaytime = $row['displaystart'].' &mdash; '.$row['displayend']; } else { $displaytime = $noTimesSet; }
					// Check if it is a Shared Event
					if ($row['isShared'] == '1') { $isShared = '<small class="label label-info preview-label">'.$sharedEvent.'</small>'; } else { $isShared = ''; }

					echo $delim."{";
					echo "
							title: '".$row['eventTitle']."',
							start: new Date(".$eventDate."),
							startsondate: '".$row['startsOnDate']."',
							endsondate: '".$row['endsOnDate']."',
							starttime: '".$startTime."',
							endtime: '".$endTime."',
							".$endsOn."
							".$allDay."
							color: '".$row['eventColor']."',
							desc: '".$row['eventDesc']."',
							startson: '".$row['displayDate']."',
							displaytime: '".$displaytime."',
							shared: '".$isShared."',
							isPublic: '',
							colorinput: '".$row['eventColor']."',
							postedby: '".$row['postedBy']."',
							eventid: '".$row['clienteventId']."',
							uid: '".$clientId."'
						";
					echo "}";
					$delim = ',';
				}

				while($rows = mysqli_fetch_assoc($result)) {

					// Months start at 0 - so subtract 1 month from the query dates
					$eventStartMnth = $rows['startMonth'];
					$eventStartMonth = --$eventStartMnth;
					$eventDate = strtotime($rows['startDate']);
					$eventDate = date('Y', $eventDate).', '.$eventStartMonth.', '.date('d, H, i', $eventDate);

					$eventEndMnth = $rows['endMonth'];
					$eventEndMonth = --$eventEndMnth;
					$eventEnd = strtotime($rows['endDate']);
					$eventEnd = date('Y', $eventEnd).', '.$eventEndMonth.', '.date('d, H, i', $eventEnd);

					// Check for an All Day event
					if ($rows['timeStart'] != '00:00') { $allDay = 'allDay: false,'; } else { $allDay = 'allDay: true,'; }
					if ($rows['startTime'] == '00:00') { $startTime = ''; } else { $startTime = $rows['startTime']; }
					if ($rows['endTime'] == '00:00') { $endTime = ''; } else { $endTime = $rows['endTime']; }
					// Check for an End Date
					if ($rows['dateEnd'] != '0000, 00, 00') { $endsOn = "end: new Date(".$eventEnd."),"; } else { $endsOn = ""; }
					// Set the Times to Display
					if ($rows['displaystart'] != '12:00 AM') { $displaytime = $rows['displaystart'].' &mdash; '.$rows['displayend']; } else { $displaytime = $noTimesSet; }
					// Check if it is a Public Event
					if ($rows['isPublic'] == '1') { $isPublic = '<small class="label label-primary preview-label">'.$pulicEvent.'</small>'; } else { $isPublic = ''; }

					echo $delim."{";
					echo "
							title: '".$rows['eventTitle']."',
							start: new Date(".$eventDate."),
							startsondate: '".$rows['startsOnDate']."',
							endsondate: '".$rows['endsOnDate']."',
							starttime: '".$startTime."',
							endtime: '".$endTime."',
							".$endsOn."
							".$allDay."
							color: '".$rows['eventColor']."',
							desc: '".$rows['eventDesc']."',
							startson: '".$rows['displayDate']."',
							displaytime: '".$displaytime."',
							shared: '',
							isPublic: '".$isPublic."',
							colorinput: '".$rows['eventColor']."',
							postedby: '".$rows['postedBy']."',
							eventid: '',
							uid: ''
						";
					echo "}";
					$delim = ',';
				}
			?>
			],
			// Show event details & options on event title click
			eventClick: function(calEvent, jsEvent, view) {

				// View Event Modal
				$('.viewEvent').modal('toggle');
				$('.event-title').show().html(calEvent.title);
				$('.event-desc').show().html(calEvent.isPublic + calEvent.shared + ' <span class="label label-default preview-label">' + calEvent.displaytime + '</span><span class="pull-right"><span class="label label-default preview-label"><?php echo $eventPostedBy; ?>: ' + calEvent.postedby + '</span></span><br /><br />' + calEvent.desc.replace(/\r\n/g, "<br />"));
				if (calEvent.isPublic === '') {
					$('.event-actions').show().html('<span class="pull-right"><a data-toggle="modal" data-dismiss="modal" href="#editEvent' + calEvent.eventid + '" class="btn btn-success"><i class="fa fa-edit"></i> <?php echo $editEvent; ?></a> <a data-toggle="modal" data-dismiss="modal" href="#deleteEvent' + calEvent.eventid + '" class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo $deleteEvent; ?></a> <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $closeBtn; ?></button></span>');
				} else {
					$('.event-actions').show().html('<span class="pull-right"><button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $closeBtn; ?></button></span>');
				}
				
				// Edit Event Modal
				$('.editEvent').attr('id', 'editEvent' + calEvent.eventid +'');
				$('.event-modal-title').show().html(calEvent.title);
				$('#editstartDate').val(calEvent.startsondate);
				$('#editeventTime').val(calEvent.starttime);
				$('#editendDate').val(calEvent.endsondate);
				$('#editendTime').val(calEvent.endtime);
				$('.titleField').val(calEvent.title);
				$('#eventColor').val(calEvent.colorinput);
				$('.descField').val(calEvent.desc);
				$('.event-id').val(calEvent.eventid);
				$('.client-id').val(calEvent.uid);

				// Delete Event Modal
				$('.deleteEvent').attr('id', 'deleteEvent' + calEvent.eventid +'');
			}
		});
	});
</script>