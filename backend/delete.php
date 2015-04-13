<!-- 
    {SMF Market - simple item marketplace tied into SMF}
    Copyright (C) {2015}  {Ryan 'iBeNifty' Malacina}
    You can find the full license included with these files.
-->

<?php
include("config.php");
require_once($level2);
is_not_guest("You don't have access to this.");

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
    * Here is the MySQLi database loading and offer displaying from a single user / item search; we'll display upto 50 results here
    * TODO:
    *   - ???
    * DONE:
    *   - Delete an offer
    */

    $delete = $_POST['id'];
    if ($conn->connect_error) {
        echo '<div class="center_text">Error occured! Please alert the web admin!</div>';
        exit();
     } else {
        @$user = $conn->prepare("SELECT `ID`, `offerType`, `forumName`, `username`, `item`, `amount`, `price`, `postDate` FROM `entries` WHERE `ID` = ?");
        @$user->bind_param('i', $delete);
        @$user->execute();
        @$user->store_result();
        @$user->bind_result($id, $offerType, $forumName, $username, $item, $amount, $price, $postDate);
        $user->fetch();
        if ($forumName == $SMFUser OR (in_array_any($allowed_groups, $user_info['groups'])) and (!$context['user']['is_guest'])) {
            echo 'offer: '.$offerType." | delete:".$delete;
            $string = "DELETED OFFER: ".$offerType." | ".$username." | ".$item." | ".$amount." | ".$price." | ".$postDate."\n";
            $filename = $now->format('Y-m-d');
            file_put_contents("../logs/".$filename, $string, FILE_APPEND | LOCK_EX);
            @$query = $conn->prepare("DELETE FROM entries WHERE `ID` = ?");
            @$query->bind_param('i', $delete);
            @$query->execute();
            header("Location: ../");
        } else {
            header("Location: ../");
            die();
        }
    }
?>