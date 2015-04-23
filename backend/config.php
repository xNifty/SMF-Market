<?php
/*
	{SMF Market - simple item marketplace tied into SMF}
    Copyright (C) {2015}  {Ryan 'iBeNifty' Malacina}
    You can find the full license included with these files.
*/
/*
* So I set some random variables in here that get used all over the place. The reason I am doing this is so that
*	you can .gitignore this file and not have your paths get screwed up each time an update is released.
*/

/*
* Relative URL for the SSI.php 
* This is important: accessing the SSI directly through a URL is a big no-no and SMF will not allow for it.
* Instead, you need to access it relative to the market directory.
* So, let's say SMF is at public_html/smf and the market is at public_html/market; this means that to access the SSI from
*	index and search, you must go one level back and for delete and post two levels back.
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

// Your SMF Forum URL - do not include index.php
$forums = "http://localhost/smf";

// Offers shown per page
$perpage = 25;

/*
* Allowed moderation group IDs
* Administrators   : 1
* Global Moderator : 2
*/
$allowed_groups = array(1, 2);

/*
* Banned Group ID - set the ID of the banned user group here and place market banned users in this group (as an additional membergroup)
* Banned ID : ?
*/
$banned_groups =  array(-9999);

/*
* useItemDB
*	- Allows for autocompletion
*/
$useItemDB = True;

/*
* useItemVarification
* 	- use the Item Database to verify items are real
*/
$useItemVarification = True;

?>