<?php
require('classes/classDatabase.php');

$pathtodir = 'classes/'; // local

	foreach (glob($pathtodir."*.inc.php") as $filename)
	{
		include $filename;
	}
?>