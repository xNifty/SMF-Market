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

if (!isset($_GET['search'])) {
    header("Location: ./index.php");
}
if (empty($_GET['search'])) {
    header("Location: ./index.php");
}
?>
<!doctype HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Market - Search</title>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/table.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="javascript/jquery-latest.js"></script>
    <script type="text/javascript" src="javascript/jquery.tablesorter.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#results").tablesorter({headers: { 0: { sorter: false}, 6: {sorter: false}}});
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
    <?php
        echo '<ul class="nav">';
            echo '<li><a href="./">Home</a></li>';
            echo '<li><a href="'.$forums.'">Forums</a></li>';
            echo '<li><form action="search.php" method="GET">';
                echo '<input type="text" name="search" placeholder="Search">';
                echo '<input type="submit" value="Submit" onsubmit="validateSearch(search)"></form></li>';
        echo '</ul>';
        function in_array_any($needles, $haystack) {
            return !!array_intersect($needles, $haystack);
        }

        echo '<div class="header_img"><img src="'.$headerimg.'" alt="market header"></div>';

        if (!$context['user']['is_guest']) {
            echo '<div class="center_text_header">Welcome, '.($context['user']['username']).'!</div>';
            echo '<hr>';
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
        * Here is the MySQLi database loading and offer displaying from a single user / item search; we'll display upto 25 results per page
        * TODO:
        *   - Send private message to user on forums by clicking their name (logged in users only)
        *   - Report an entry if it is fake, etc. Log the reported and reportee to prevent fake report abuse
        * DONE:
        *   - Returns any matching result (i.e. dragon would show all items with dragon in name or users with dragon in their name)
        *   - Pages for overflow (pagination)
        *   - Sort by buying or selling (plus date, amount, item name)
        */
        $search = "%{$_GET['search']}%";
        $display = $_GET['search'];
        if ($conn->connect_error) {
            echo '<div class="center_text">Error occured! Please alert the web admin!</div>';
            exit();
        } else {
            if (@$offers = $conn->prepare("SELECT `ID`, `offerType`, `username`, `item`, `amount`, `price`, `postDate` FROM `entries` WHERE `username` LIKE ? OR `item` LIKE ? ORDER BY `ID` DESC LIMIT $startpage, $perpage")) {
                @$offers->bind_param('ss', $search, $search);
                @$offers->execute();
                @$offers->store_result();
                @$offers->bind_result($id, $offerType, $username, $item, $amount, $price, $postDate);
                @$num_rows = $offers->num_rows;
                if ($num_rows == 0)
                    echo '<div class="alert-box error"><span>ERROR: </span>No Results Found For <div class="special_word">'.$display.'</div></div>';
                else {
                    echo '<div class="alert-box success"><span>SUCCESS: </span>Found '.$num_rows.' results for <div class="special_word">'.$display.'</div></div>';
                    echo '<div class="header_text notice">Notice: we do not confirm any one person owns the item they are "selling"; please report those abusing the system. <br /></div>';
                    echo '<table id="results" class="tablesorter">';
                        echo '<thead>';
                        echo '<tr>';
                            echo '<th>User</th>';
                            echo '<th>Offer Type</th>';
                            echo '<th>Item</th>';
                            echo '<th>Amount</th>';
                            echo '<th>Price</th>';
                            echo '<th>Date</th>';
                            echo '<th>Delete</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
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
                    echo '</tbody>';
                    echo '</table>';
                }
            }
            if (@$pagin = $conn->prepare("SELECT * FROM `entries` WHERE `username` LIKE ? OR `item` LIKE ?")) {
                @$pagin->bind_param('ss', $search, $search);
                @$pagin->execute();
                @$pagin->store_result();
                @$pagin->bind_result($id, $offerType, $username, $item, $amount, $price, $postDate);
                @$total = $pagin->num_rows;
                $total_pages = ceil($total / $perpage);
                echo '<div class="pag_text">';
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<a href="search.php?search='.$display.'&page='.$i.'">'.$i.'</a>';
                }
                echo '</div>';
            }
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            echo '<div class="footer">All times are Eastern</div>';
            if (in_array_any($allowed_groups, $user_info['groups']))
                echo '<div class="footer">Page load complete; execution time: ' .$time. '</div>';
            echo '<div class="footer">Market written and maintained by Ryan M. on <a href="https://github.com/xNifty" target="_blank">GitHub</a>; &copy; 2015 <a href="https://ibenifty.me/" target="_blank">Ryan M.</a></div>';
        }
    ?>
</body>
</html>