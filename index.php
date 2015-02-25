<?php
require_once('../smf/SSI.php');
$_SESSION['login_url'] = 'localhost' . $_SERVER['PHP_SELF'];
$_SESSION['logout_url'] = 'localhost/testing/test.php';

$DBServer = 'localhost';
$DBUser = 'root';
$DBPass = '';
$DBName = 'market';

$SMFUser = $context['user']['username'];
@$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
?>

<!doctype HTML>
<head>
	<meta charset="utf-8">
	<title>PR Market, Alpha 0.0.1</title>
	<link href="css/style.css" rel="stylesheet" type="text/css">
	<script src="javascript/validate.js"></script>
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
		<ul class="nav">
			<li><a href="#">Home</a></li>
			<li><a href="#">Forums</a></li>
			<li><form action="search.php" method="POST">
				<input type="text" name="search" placeholder="Search">
				<input type="submit" value="Submit"></form></li>
		</ul>
		<?php
			$now = new DateTime();
			$now->setTimezone(new DateTimeZone('America/Detroit'));
			function in_array_any($needles, $haystack) {
				return !!array_intersect($needles, $haystack);
			}

			echo '<div class="header_img"><img src="images/header.png" alt="market header"></div>';

			if (!$context['user']['is_guest']) {
				echo '<div class="center_text_header">Welcome, '.($context['user']['username']).'!</div>';
			 } else {
				echo '<div class="center_text_header">Welcome, Guest!</div>';
			}

			/* 
			* Allowed moderation group IDs
			* Administrators   : 1
			* Global Moderator : 2
			*/
			$allowed_groups = array(1, 2);

			/*
			* Here is the MySQLi database loading and offer displaying; only shows 25 offers.
			* TODO:
			*	- Insert new offers
			*	- On page login / logout
			*	- Send private message to user on forums by clicking their name (logged in users only)
			*   - Sort by buying or selling
			* DONE:
			*	- Searching
			*	- User deletion of their entries
			*/
	       	if ($conn->connect_error) {
	       		echo '<div class="center_text">Error occured! Please alert the web admin!</div>';
	       		exit();
	       	 } else {
	       	 	if (@$offers = $conn->prepare("SELECT `ID`, `offerType`, `username`, `item`, `amount`, `price`, `postDate` FROM `entries` ORDER BY `ID` DESC LIMIT 25")) {
	                @$offers->execute();
	                @$offers->store_result();
	                @$offers->bind_result($id, $offerType, $username, $item, $amount, $price, $postDate);
	                @$num_rows = $offers->num_rows;
	                if ($num_rows == 0)
	                    echo '<div class="alert-box error"><span>ERROR: </span>No Offers Currently Posted!</div>';
	                    //echo '<div class="center_text">No Results Found For "'.$search.'"</div>';
	                else {
						echo '<div class="header_text">There are currently '.$num_rows.' active offers <br /></div>';
						echo '<div class="header_text">Need information on an item? Check the <a href="http://yolocatz.x10.mx/wiki" target="_blank">wiki!</a> <br /></div>';
						echo '<div class="header_text">This page only displays 25 offers; please use the search bar for more. <br /></div>';
						echo '<hr>';
	                    echo '<table>';
	                        echo '<tr>';
	                            echo '<th>User</th>';
	                            echo '<th>Offer Type</th>';
	                            echo '<th>Item</th>';
	                            echo '<th>Price</th>';
	                            echo '<th>Amount</th>';
	                            echo '<th>Date</th>';
	                            echo '<th>Delete</th>';
	                        echo '</tr>';
	                    while ($offers->fetch()) {
	                        echo '<tr>';
	                            echo '<td>'.$username.'</td>';
	                            echo '<td>'.$offerType.'</td>';
	                            echo '<td>'.ucwords($item).'</td>';
	                            echo '<td>'.number_format($amount).'</td>';
	                            echo '<td>'.number_format($price).'</td>';
	                            echo '<td>'.date("F j, Y / g:i a", strtotime($postDate)).'</td>';
	                           if ($username == $SMFUser OR (in_array_any($allowed_groups, $user_info['groups'])))
	                            echo '<td><form action="backend/delete.php" method="POST" id="delete" onsubmit="window.location.reload();">
	                                    <form type="submit" value="Delete">
	                                    </form><button type="submit" form="delete" value="'.$id.'" name="id">Delete</button></td>';
	                        else
	                            echo '<td><button type="button" disabled>Delete</button></td>';
	                        echo '</tr>';
	                    }
	                    echo '</table>';
                	}
                } else {
                	var_dump($offers);
                	echo 'OOOPS';
                }
	       	 	if (!$context['user']['is_guest']) {
					echo '<form action="backend/post.php" method="POST" id="post-offer">';;
	   	 				echo '<div class="new_offer">';
		       	 			echo '<div class="offer-header">Add an Offer</div>';
		       	 			echo '<div class="offer-title">Offer Type</div>';
		       	 			echo '<select offer="offerType" name="offer" required> <option value="" disabled selected>Please Select One</option> <option value="Buying">Buying</option> <option value="Selling">Selling</option></select>';	       	 			
		       	 			
		       	 			echo '<div class="offer-title">Item Name</div>';
		       	 			echo '<input type ="text" name="item" maxlength="25" placeholder="Item Name" required>';
							
							echo '<div class="offer-title">Price Per Item</div>';
		       	 			echo '<input type ="number" onKeyPress="return numbersonly(this, event)" name="price" min="1" max="9999" placeholder="Price Per Item (max 9999)" required>';
		       	 			
		       	 			echo '<div class="offer-title">Amount</div>';
		       	 			echo '<input type ="number" onKeyPress="return numbersonly(this, event)" name="amount" min="1" max="9999" placeholder="Amount to Sell (max 9999)" required>';
		       	 			
		       	 			echo '<div class="offer_submit"><input type="submit" form="post-offer" name="post-offer"></div>';
		       	 		echo '</div>';
			       	echo '</form>';
		       	}
	       	 	$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	       	 	echo '<div class="footer">All times are Eastern</div>';
	       		echo '<div class="footer">Page load complete; execution time: ' .$time. '</div>';
	       		echo '<div class="footer">Market written by yolocatz (aka nifty); &copy; 2015 yolocatz and Project Rainbow</div>';
	       	}
	    ?>
	</wrapper>
</body>
</html>