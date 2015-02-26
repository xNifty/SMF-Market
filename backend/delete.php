<?php
require_once('../../smf/SSI.php');
include("backend/base.php");

$DBServer = 'localhost';
$DBUser = 'root';
$DBPass = '';
$DBName = 'market';

$SMFUser = $context['user']['username'];
@$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
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
        @$user = $conn->prepare("SELECT `username` FROM `entries` WHERE `ID` = ?");
        @$user->bind_param('i', $delete);
        @$user->execute();
        @$user->store_result();
        if ($user == $SMFUser OR (in_array_any($allowed_groups, $user_info['groups'])) and (!$context['user']['is_guest'])) {
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