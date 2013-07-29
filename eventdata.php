<?php
require('config.php');

$event = new Event();

	if(isset($_GET['id']) && $_GET['id'] > 0)
	{
		$getid = $_GET['id'];
		$eventList = $event->listEvent($getid);

	} else {
		$eventList = $event->getEvents();
	}
	
$jlogList = json_encode($eventList);
echo $jlogList;
?>