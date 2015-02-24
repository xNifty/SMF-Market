<?php
require_once('../../smf/SSI.php');
$_SESSION['login_url'] = 'localhost' . $_SERVER['PHP_SELF'];
$_SESSION['logout_url'] = 'localhost/testing/test.php';

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
	if (!$context['user']['is_guest']) {
		echo '<div class="center_text_header">Welcome, '.($context['user']['username']).'!</div>';
		echo '<hr>';
	} else {
		echo '<div class="center_text">Welcome guest.</div>';
		echo '<hr>';
	}

	        /* 
	        * Allowed moderation group IDs
	        * Administrators   : 1
	        * Global Moderator : 2
	        */
	        $allowed_groups = array(1, 2);

	        /*
	        * Here is the MySQLi database loading and offer displaying from a single user / item search; we'll display upto 50 results here
	        * TODO:
	        *   - Pages for overflow
	        *   - Sort by buying or selling
	        *   - Sort by price, amount (search page only)
	        * DONE:
	        *   - Returns any matching result (i.e. dragon would show all items with dragon in name or users with dragon in their name)
	        */
	        $entry = $_POST['post-offer'];
	        if ($conn->connect_error) {
	        	echo '<div class="center_text">Error occured! Please alert the web admin! Error: ' . mysqli_connect_error() . '</div>';
	        	exit();
	        } else {
	        	echo $_POST['offer'].'<br>';
	        	echo $_POST['item'].'<br>';
	        	echo $_POST['price'].'<br>';
	        	echo $_POST['amount'].'<br>';
	        	echo $now->format('Y-m-d H:i:s');
	        }
?>