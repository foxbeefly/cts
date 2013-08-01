<?php
class Sponsor extends Application
{
	function getSponsors($sponsorType = 'e')
	{
		$listArray = array();
		// check for the table first, creating it if it does not...
		$this->checkTableExists($this->tableSponsors);
		if($data = $this->fetch("SELECT * FROM `$this->tableSponsors` WHERE `sponsorType` = '".$sponsorType."';"))
		{
			foreach($data as $row)
			{
				array_push($listArray, $row);
				/*
				$c = $row['sponsorID']-1;
				$listArray[$c]['id'] = $row['sponsorID'];
				$listArray[$c]['name'] = $row['sponsorName'];
				$listArray[$c]['description'] = $row['sponsorDescription'];
				$listArray[$c]['tel'] = $row['sponsorTel'];
				$listArray[$c]['cell'] = $row['sponsorCell'];
				$listArray[$c]['twitter'] = $row['sponsorTwitter'];
				$listArray[$c]['url'] = $row['sponsorURL'];
				$listArray[$c]['image'] = $row['sponsorImage'];
				$listArray[$c]['icon'] = $row['sponsorIcon'];
				 */
			}
		}
		return $listArray;
	}
	
	function listSponsor($sponsorID)
	{
		$sponsorList = array();
		$sql = "SELECT * FROM `$this->tableSponsors` WHERE `sponsorID` = '".$sponsorID."' LIMIT 1;";
		if($rawdata = $this->fetch($sql))
		{
			foreach($rawdata as $row)
			{
				$sponsorList['id'] = $row['sponsorID'];
				$sponsorList['name'] = $row['sponsorName'];
				$sponsorList['description'] = $row['sponsorDescription'];
				$sponsorList['tel'] = $row['sponsorTel'];
				$sponsorList['cell'] = $row['sponsorCell'];
				$sponsorList['twitter'] = $row['sponsorTwitter'];
				$sponsorList['url'] = $row['sponsorURL'];
				$sponsorList['image'] = $row['sponsorImage'];
				$sponsorList['icon'] = $row['sponsorIcon'];
			}
		}
		return $sponsorList;
	}
}
?>