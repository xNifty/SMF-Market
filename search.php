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
</head>
<body>
    <ul class="nav">
        <li><a href="./">Home</a></li>
        <li><a href="#">Forums</a></li>
        <li><form action="search.php" method="POST">
            <input type="text" name="search" placeholder="Search">
            <input type="submit" value="Submit"></form></li>
    </ul>
    <?php
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
        $search = "%{$_POST['search']}%";
        $display = $_POST['search'];
        if ($conn->connect_error) {
            echo '<div class="center_text">Error occured! Please alert the web admin!</div>';
            exit();
        } else {
            if (@$offers = $conn->prepare("SELECT `ID`, `username`, `item`, `amount`, `price`, `postDate` FROM `entries` WHERE `username` LIKE ? OR `item` LIKE ? ORDER BY `ID` DESC LIMIT 50")) {
                @$offers->bind_param('ss', $search, $search);
                @$offers->execute();
                @$offers->store_result();
                @$offers->bind_result($id, $username, $item, $amount, $price, $postDate);
                @$num_rows = $offers->num_rows;
                if ($num_rows == 0)
                    echo '<div class="alert-box error"><span>ERROR: </span>No Results Found For <div class="special_word">'.$display.'</div></div>';
                    //echo '<div class="center_text">No Results Found For "'.$search.'"</div>';
                else {
                    echo '<div class="alert-box success"><span>SUCCESS: </span>Found '.$num_rows.' results for <div class="special_word">'.$display.'</div></div>';
                    echo '<table>';
                        echo '<tr>';
                            echo '<th>User</th>';
                            echo '<th>Item</th>';
                            echo '<th>Price</th>';
                            echo '<th>Amount</th>';
                            echo '<th>Date</th>';
                            echo '<th>Delete</th>';
                        echo '</tr>';
                    while ($offers->fetch()) {
                        echo '<tr>';
                            echo '<td>'.$username.'</td>';
                            echo '<td>'.ucwords($item).'</td>';
                            echo '<td>'.number_format($amount).'</td>';
                            echo '<td>'.number_format($price).'</td>';
                            echo '<td>'.date("F j, Y / g:i a", strtotime($postDate)).'</td>';
                           if ($username == $SMFUser OR (in_array_any($allowed_groups, $user_info['groups'])))
                            echo '<td><form action="delete.php?" method="GET" id="delete" onsubmit="setTimeout(function () { window.location.reload(); }, 60)">
                                    <form type="submit" value="Delete">
                                    </form><button type="submit" form="delete" value="'.$id.'" name="id">Delete</button></td>';
                        else
                            echo '<td><button type="button" disabled>Delete</button></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            }
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            echo '<div class="footer">All times are Eastern</div>';
            echo '<div class="footer">Page load complete; execution time: ' .$time. '</div>';
            echo '<div class="footer">Market written by yolocatz (aka nifty); &copy; 2015 yolocatz and Project Rainbow</div>';
        }
    ?>
</body>
</html>