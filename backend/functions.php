<?php
include("config.php");

function getUserID($username) {
	$userID = loadMemberData(array($username), $is_name = true, $set = 'minimal');

	return $userID[0];
}
?>