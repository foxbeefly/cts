<?php
require('config.php');

$sponsor = new Sponsor();

	if(isset($_GET['id']) && $_GET['id'] > 0)
	{
		$getid = $_GET['id'];
		$sponsorList = $sponsor->listSponsor($getid);

	} elseif (isset($_GET['type'])) {
 		if($_GET['type'] === 'r')
 		{
			$getType = $_GET['type'];
			$sponsorList = $sponsor->getSponsors($getType);
		} elseif ($_GET['type'] === 'a') {
			$getType = $_GET['type'];
			$sponsorList = $sponsor->getSponsors($getType);
		}		
	} else {
		$sponsorList = $sponsor->getSponsors();
	}
	
$jList = json_encode($sponsorList);
echo $jList;
?>