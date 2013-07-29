<?php
require('config.php');

$sponsor = new Sponsor();

	if(isset($_GET['id']) && $_GET['id'] > 0)
	{
		$getid = $_GET['id'];
		$sponsorList = $sponsor->listSponsor($getid);

	} else {
		$sponsorList = $sponsor->getSponsors();
	}
	
$jList = json_encode($sponsorList);
echo $jList;
?>