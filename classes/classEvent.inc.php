<?php
class Event extends Application
{
	function getEvents()
	{
		// check for the table first, creating it if it does not...
		$this->checkTableExists($this->tableEvents);
		$data = $this->fetch("SELECT * FROM `$this->tableEvents`;");
		foreach($data as $row) {
				
			$c = $row['eventID']-1;
			$listArray[$c]['id'] = $row['eventID'];
			$listArray[$c]['name'] = $row['eventName'];
			$listArray[$c]['description'] = $row['eventDescription'];
			$listArray[$c]['start'] = $row['eventStart'];
			//$listArray[$c]['end'] = $row['eventEnd'];
			$listArray[$c]['venue'] = $row['eventVenue'];
			$listArray[$c]['gps'] = $row['eventGPS'];
			$listArray[$c]['cost'] = $row['eventCost'];
			$listArray[$c]['image'] = $row['eventImage'];
		}
		
		return $listArray;
	}
	
	function listEvent($eventID)
	{
		$sql = "SELECT * FROM `$this->tableEvents` WHERE `eventID` = '".$eventID."' LIMIT 1;";
		$rawdata = $this->fetch($sql);
		foreach($rawdata as $row) {
			$eventList['id'] = $row['eventID'];
			$eventList['name'] = $row['eventName'];
			$eventList['description'] = $row['eventDescription'];
			$eventList['start'] = $row['eventStart'];
			//$eventList['end'] = $row['eventEnd'];
			$eventList['venue'] = $row['eventVenue'];
			$eventList['gps'] = $row['eventGPS'];
			$eventList['cost'] = $row['eventCost'];
			$eventList['image'] = $row['eventImage'];
		}
		return $eventList;
	}
}
?>