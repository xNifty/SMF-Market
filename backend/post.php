<?php
require_once('../../smf/SSI.php');
is_not_guest("You need to be logged in to post an offer.");
include("backend/base.php");

$DBServer = 'localhost';
$DBUser = 'root';
$DBPass = '';
$DBName = 'market';

$SMFUser = $context['user']['username'];
@$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
$now = new DateTime();
$now->setTimezone(new DateTimeZone('America/Detroit'));
?>
<?php
	function in_array_any($needles, $haystack) {
		return !!array_intersect($needles, $haystack);
	}
    /* 
    * Allowed moderation group IDs
    * Administrators   : 1
    * Global Moderator : 2
    */
    $allowed_groups = array(1, 2);

    /*
    * Here is the MySQLi database posting
    * TODO:
    *   - ???
    * DONE:
    *   - Post an offer
    */
    $entry = $_POST['post-offer'];
    if ($conn->connect_error) {
    	echo '<div class="center_text">Error occured! Please alert the web admin! Error: ' . mysqli_connect_error() . '</div>';
    	exit();
    } else {
    	if (!$context['user']['is_guest']) {
	    	if (@$offers = $conn->prepare("INSERT INTO entries(`offerType`, `Username`, `Item`, `Amount`, `Price`, `postDate`) VALUES (?, ?, ?, ?, ?, ?)")) {
				@$offers->bind_param('ssssss', $_POST['offer'], $SMFUser, strtolower($_POST['item']), $_POST['amount'], $_POST['price'], $now->format('Y-m-d H:i:s'));
				@$offers->execute();
				header("Location: ../");
	    	} else {
				echo '<div class="center_text">Error occured! Please alert the web admin!</div>';
				exit();
			}
    	} else {
    		header("Location: ../");
    	}
    }
?>