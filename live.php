<!-- 
	{SMF Market - simple item marketplace tied into SMF}
    Copyright (C) {2015}  {Ryan 'iBeNifty' Malacina}
    You can find the full license included with these files.
-->

<?php
include("backend/config.php");
require_once($level1);

$SMFUser = $context['user']['username'];

@$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);

if (isset($_GET['page'])) {
	$page = $_GET['page'];
} else {
	$page = 1;
}
$startpage = ($page-1)*$perpage;
?>

<!doctype HTML>
<head>
	<meta charset="utf-8">
	<title>Market - Home</title>
	<link href="css/style.css" rel="stylesheet" type="text/css">
	<script src="javascript/validate.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css">
    <script type="text/javascript">
        $(document).ready(function() {
            $("#item").autocomplete({
                    source:'backend/base.php',
                    minLength:3
                });
            });
        $(document).ready(function() {
			$.ajaxSetup({ cache: false }); // This part addresses an IE bug.  without it, IE will only load the first number and will never refresh
			setInterval(function() {
				$('#listitems').load('backend/list_offers.php');
			}, 3000); // the "3000" here refers to the time to refresh the div.  it is in milliseconds. 
		});

		$(window).load(function() {
			$('#dvLoading').fadeOut(3000);
		});
    </script>
</head>

<noscript>
	<style type="text/css">
		wrapper {display: none;}
		.nsText {text-align: center;}
	</style>
	<div class="nsText">
		Please enable JS to use the market.
	</div>
</noscript>

<body>
	<wrapper>
		<?php
			echo '<ul class="nav">';
				echo '<li><a href="./">Home</a></li>';
				echo '<li><a href="'.$forums.'">Forums</a></li>';
				echo '<li><form name="searchForm" action="search.php" method="GET" onsubmit="return validateSearch()">';
					echo '<input type="text" name="search" placeholder="Search" maxlength="25">';
					echo '<input type="submit" value="Submit"></form></li>';
			echo '</ul>';
			$now = new DateTime();
			$now->setTimezone(new DateTimeZone('America/Detroit'));
			function in_array_any($needles, $haystack) {
				return !!array_intersect($needles, $haystack);
			}

			echo '<div class="header_img"><img src="'.$headerimg.'" alt="market header"></div>';

			if (!$context['user']['is_guest']) {
				echo '<div class="center_text_header">Welcome, '.($context['user']['name']).'!</div>';
			 } else {
				echo '<div class="center_text_header">Welcome, Guest!</div>';
				echo '<div class="header_text">You must be logged into the <a href="'.$forums.'">forums</a> to make use of posting offers<br /></div>';
				echo '<hr>';
			}

			/*
			* Allowed moderation group IDs
			* Administrators   : 1
			* Global Moderator : 2
			*/
			$allowed_groups = array(1, 2);

			/*
			* Here is the MySQLi database loading and offer displaying
			* TODO:
			*		- Send private message to user on forums by clicking their name (logged in users only)
			*		- Report an entry if it is fake, etc. Log the reported and reportee to prevent fake report abuse
			*		- Report bug(s) option
			* DONE:
			*		- Searching
			*		- User deletion of their entries
			*		- Insert new offers
			*		- Pagination
			* IGNORED:
			*		- On page login / logout (SMF SSI actually makes this stupidly annoying)
			*   - Sort by buying or selling (search page only, index is always newest->oldest)
			*/
	       	if ($conn->connect_error) {
	       		echo '<div class="center_text">Error occured! Please alert the web admin!</div>';
	       		exit();
	       	 } else {
	       	 	echo '<div id ="dvLoading"></div>';
	       	 	echo '<div id="listitems"></div>';
	       	 	$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	       	 	echo '<div class="footer">All times are Eastern</div>';
	       	 	if (in_array_any($allowed_groups, $user_info['groups']))
	       			echo '<div class="footer">Page load complete; execution time: ' .$time. '</div>';
	       		echo '<div class="footer">Market written and maintained by Ryan M. on <a href="https://github.com/xNifty" target="_blank">GitHub</a>; &copy; 2015 <a href="https://ibenifty.me/" target="_blank">Ryan M.</a></div>';
	       	}
	    ?>
	</wrapper>
</body>
</html>
