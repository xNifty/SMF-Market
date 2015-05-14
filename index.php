<!-- 
	{SMF Market - simple item marketplace tied into SMF}
    Copyright (C) {2015}  {Ryan 'iBeNifty' Malacina}
    You can find the full license included with these files.
-->

<?php
include("backend/config.php");
include("backend/functions.php");
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
            $("input#item").autocomplete({
            	source: "backend/base.php",
                minLength:3,
            });
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
				echo '<li><a href="index.php">Home</a></li>';
				echo '<li><a href="'.$forums.'">Forums</a></li>';
				echo '<li><a href="./live.php">Live Feed</a></li>';
				if (!$context['user']['is_guest']) {
					$_SESSION['logout_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					echo '<li>'.ssi_logout().'</li>';
				}
				echo '<li><form name="searchForm" action="search.php" method="GET" onsubmit="return validateSearch()">';
					echo '<input type="text" name="search" placeholder="Search" maxlength="25">';
					echo '<input type="submit" value="Submit"></form></li>';
			echo '</ul>';
			if ($context['user']['is_guest']) {
				echo '<div class="login">';
				$_SESSION['login_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				echo ssi_login();
				echo '</div>';
			}
			$now = new DateTime();
			$now->setTimezone(new DateTimeZone('America/Detroit'));
			if ($checkForUpdate and in_array_any($allowed_groups, $user_info['groups'])) {
				if (versionCompare($version))
					echo '<div class="alert-box warning"><span>Notice: </span>New market version ('.getLatestTag().') <a href="https://github.com/xNifty/SMF-Market/releases" target="_blank">available</a>!</div>';
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
			* Here is the MySQLi database loading and offer displaying
			* TODO:
			*		- Report an entry if it is fake, etc. Log the reported and reportee to prevent fake report abuse
			*		- Report bug(s) option
			* DONE:
			*		- Searching
			*		- User deletion of their entries
			*		- Insert new offers
			*		- Pagination
			* 		- Banned user group
			*		- Send private message to user on forums by clicking their name (logged in users only)
			* IGNORED:
			*		- On page login / logout (SMF SSI actually makes this stupidly annoying)
			*		- Sort by buying or selling (search page only, index is always newest->oldest)
			*/
	       	if ($conn->connect_error) {
	       		echo '<div class="center_text">Error occured! Please alert the web admin!</div>';
	       		exit();
	       	 } else {
	       	 	if (@$offers = $conn->prepare("SELECT `ID`, `offerType`, `forumName`, `username`, `item`, `amount`, `price`, `postDate` FROM `entries` ORDER BY `ID` DESC LIMIT $startpage, $perpage")) {
	                @$offers->execute();
	                @$offers->store_result();
	                @$offers->bind_result($id, $offerType, $forumName, $username, $item, $amount, $price, $postDate);
	                @$num_rows = $offers->num_rows;
	                if ($num_rows == 0)
	                    echo '<div class="alert-box error"><span>ERROR: </span>No Offers Currently Posted!</div>';
	                else {
						echo '<div class="header_text">There are currently '.totalOffersIndex($conn).' active offers <br /></div>';
						if ($haswiki)
							echo '<div class="header_text">Need information on an item? Check the <a href="'.$wiki.'" target="_blank">wiki!</a> <br /></div>';
						echo '<div class="header_text">This page only displays '.$perpage.' offers; please use the page listing at the bottom for more or try narrowing with the search bar. <br /></div>';
						echo '<div class="header_text notice">Notice: we do not confirm any one person owns the item they are "selling"; please report those abusing the system. <br /></div>';
						echo '<hr>';
	                    echo '<table class="displayoffers">';
	                        echo '<tr>';
								echo '<th>Forum Name</th>';
								echo '<th>Server Name</th>';
	                            echo '<th>Offer Type</th>';
	                            echo '<th>Item</th>';
	                            echo '<th>Amount</th>';
	                            echo '<th>Price per Item</th>';
	                            echo '<th>Date</th>';
	                            echo '<th>Delete</th>';
	                        echo '</tr>';
	                    while ($offers->fetch()) {
	                        echo '<tr>';
	                        	if (!$context['user']['is_guest'])
	                        		echo '<td><a href="'.$forums.'/index.php?action=pm;sa=send;u='.getUserID($forumName).';subject=['.$offerType.'] '.ucwords($item).'" target="_blank">'.$forumName.'</a></td>';
	                        	else
	                        		echo '<td>'.$forumName.'</td>';
	                            echo '<td>'.$username.'</td>';
	                            echo '<td>'.$offerType.'</td>';
	                            echo '<td>'.ucwords($item).'</td>';
	                            echo '<td>'.number_format($amount).'</td>';
	                            echo '<td>'.number_format($price).'</td>';
	                            echo '<td>'.date("F j, Y / g:i a", strtotime($postDate)).'</td>';
	                           if ($forumName == $SMFUser OR (in_array_any($allowed_groups, $user_info['groups'])))
	                            echo '<td><form action="backend/delete.php" method="POST" id="delete" onsubmit="window.location.reload();">
	                                    <form type="submit" value="Delete">
	                                    </form><button type="submit" form="delete" value="'.$id.'" name="id" onclick="return confirm(\'Are you sure you wish to delete this entry?\');">Delete</button></td>';
	                        	else
	                          	echo '<td><button type="button" disabled>Delete</button></td>';
	                    	echo '</tr>';
	                    }
	                    echo '</table>';
                	}
                } else {
                	echo 'Something broke...contact the web admin.';
                	die();
                }
	       	 	if (!$context['user']['is_guest'] and (!in_array_any($banned_groups, $user_info['groups']))) {
					echo '<form action="backend/post.php" method="POST" id="post-offer" name="post-offer" onsubmit="return validatePostForm()">';
	   	 				echo '<div class="new_offer">';
		       	 			echo '<div class="offer-header">Add an Offer</div>';
		       	 			echo '<div class="offer-title">Offer Type</div>';
		       	 			echo '<select offer="offerType" name="offer" required> <option value="" disabled selected>Please Select One</option> <option value="Buying">Buying</option> <option value="Selling">Selling</option></select>';

		       	 			echo '<div class="offer-title">Item Name</div>';
		       	 			echo '<input type ="text" id="item" name="item" maxlength="25" placeholder="Item Name" required>';

							echo '<div class="offer-title">Price Per Item</div>';
		       	 			echo '<input type ="number" onKeyPress="return numbersonly(this, event)" name="price" min="1" max="2147483647" placeholder="Price Per Item" required>';

		       	 			echo '<div class="offer-title">Amount</div>';
		       	 			echo '<input type ="number" onKeyPress="return numbersonly(this, event)" name="amount" min="1" max="9999" placeholder="Amount to Sell (max 9999)" required>';

		       	 			echo '<div class="offer-title">Server Username</div>';
		       	 			echo '<input type ="text" id="forumname" name="forumname" maxlength="12" placeholder="Server Username" required>';

		       	 			echo '<div class="offer_submit"><input type="submit" form="post-offer" name="post-offer"></div>';
		       	 		echo '</div>';
			       	echo '</form>';
		       	} else if (in_array_any($banned_groups, $user_info['groups'])) {
		       		echo '<div class="new_offer">';
		       		echo '<div class="banned">You have been banned from using the market. You may appeal at [PLACE LINK HERE]</div>';
		       		echo '</div>';
		       	}
				if (@$pagin = $conn->prepare("SELECT * FROM `entries`")) {
					@$pagin->execute();
					@$pagin->store_result();
					@$pagin->bind_result($id, $offerType, $username, $item, $amount, $price, $postDate);
					@$total = $pagin->num_rows;
					$total_pages = ceil($total / $perpage);
					echo '<div class="pag_text">';
					for ($i = 1; $i <= $total_pages; $i++) {
						echo '<a href="index.php?page='.$i.'">'.$i.'</a>';
					}
					echo '</div>';
				}
	       	 	$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	       	 	echo '<div class="footer">All times are Eastern<br>';
	       	 	if (in_array_any($allowed_groups, $user_info['groups']))
	       			echo 'Page load complete; execution time: ' .$time.'<br>';
	       		echo 'SMF-Market, version '.$version.', written and maintained by Ryan M. on <a href="https://github.com/xNifty" target="_blank">GitHub</a>; &copy; 2015 <a href="https://ibenifty.me/" target="_blank">Ryan M.</a></div>';
	       	}
	    ?>
	</wrapper>
</body>
</html>
