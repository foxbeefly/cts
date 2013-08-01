<?php
require('config.php');

$data = new Event();

	if(isset($_GET['id']) && $_GET['id'] > 0)
	{
		$getid = $_GET['id'];
		$dataList = $data->listEvent($getid);

	} elseif(isset($_GET['rid']) && $_GET['rid'] > 0) {
		$getid = $_GET['rid'];
		$dataList = $data->listResults($getid);
	} else {
		$dataList = $data->getEvents();
	}
	
$jdataList = json_encode($dataList);
echo $jdataList;
?>