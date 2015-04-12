<!-- 
    {SMF Market - simple item marketplace tied into SMF}
    Copyright (C) {2015}  {Ryan 'iBeNifty' Malacina}
    You can find the full license included with these files.
-->

<?php
include("config.php");
require_once($level2);
is_not_guest("You need to be logged in to post an offer.");

$SMFUser = $context['user']['username'];
@$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
@$valid = new mysqli($DBServer, $DBUser, $DBPass, $DBItems);
$now = new DateTime();
$now->setTimezone(new DateTimeZone('America/Detroit'));
?>
<?php
	function in_array_any($needles, $haystack) {
		return !!array_intersect($needles, $haystack);
	}

    /*
    * Here is the MySQLi database posting
    * TODO:
    *   - No duplicate entries from one author
    * DONE:
    *   - Post an offer
    */
    $entry = $_POST['post-offer'];
    $legalPost = False;
    if (!$context['user']['is_guest'] and $useItemVarification == "True") {
        if (@$checkValid = $valid->prepare("SELECT * FROM `list` WHERE `name` = ?")) {
            @$checkValid->bind_param('s', strtolower($_POST['item']));
            @$checkValid->execute();
            @$checkValid->store_result();
            @$checkValid->bind_result($id, $name);
            @$num_rows = $checkValid->num_rows;
            if ($num_rows > 0) {
                $legalPost = True;
            }
        } else {
            header("Location: ../");
        }
    } else if (!$context['user']['is_guest'] and $useItemVarification == "False") {
        $legalPost = True;
    } else {
        header("Location: ../");
    }
    if ($conn->connect_error) {
    	echo '<div class="center_text">Error occured! Please alert the web admin! Error: ' . mysqli_connect_error() . '</div>';
    	exit();
    } else {
    	if ((!$context['user']['is_guest']) and ($legalPost == True)) {
	    	if (@$offers = $conn->prepare("INSERT INTO entries(`offerType`, `Username`, `Item`, `Amount`, `Price`, `postDate`) VALUES (?, ?, ?, ?, ?, ?)")) {
				@$offers->bind_param('ssssss', $_POST['offer'], $SMFUser, strtolower($_POST['item']), $_POST['amount'], $_POST['price'], $now->format('Y-m-d H:i:s'));
                $string = "POSTED OFFER: ".$_POST['offer']." | ".$SMFUser." | ".strtolower($_POST['item'])." | ".$_POST['amount']." | ".$_POST['price']." | ".$now->format('Y-m-d H:i:s')."\n";
                $filename = $now->format('Y-m-d');
                file_put_contents("../logs/".$filename, $string, FILE_APPEND | LOCK_EX);
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