<!-- 
	{SMF Market - simple item marketplace tied into SMF}
    Copyright (C) {2015}  {Ryan 'iBeNifty' Malacina}
    You can find the full license included with these files.
-->

<?php

/*
* So I set some random variables in here that get used all over the place. The reason I am doing this is so that
*	you can .gitignore this file and not have your paths get screwed up each time an update is released.
*/

// Relative URL for the SSI.php
/* 
* This is important: accessing the SSI directly through a URL is a big no-no and SMF will not allow for it.
* Instead, you need to access it relative to the market directory.
*/
$level1 = '../smf/SSI.php'; // This is one level above any file making use of it (in this case: index.php and search.php)
$level2 = '../../smf/SSI.php'; // This is two levels above any file making use of it (in this case: delete.php and post.php)

// Database Connection Information
// Database Server, typcially localhost
$DBServer = 'localhost';
// Database Username
$DBUser = 'root';
// Database Password
$DBPass = '';
// Database Where All Offers Get Added
$DBName = 'market';
// Database Containing All The Items (for item validation when posting + the autocomplete)
$DBItems = 'items';

// Your header image URL
$headerimg = "images/header.png";

// Your SMF Forum URL
$forums = "#";

// Offers shown per page
$perpage = 25;
?>