<?php
	
	include("config.php");
	$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);

	$offers = $conn->prepare("SELECT `ID`, `offerType`, `forumName`, `username`, `item`, `amount`, `price`, `postDate` FROM `entries` ORDER BY `ID` DESC LIMIT 25");
    $offers->execute();
    $offers->store_result();
    $offers->bind_result($id, $offerType, $forumName, $username, $item, $amount, $price, $postDate);
    $num_rows = $offers->num_rows;
    if ($num_rows == 0)
        echo '<div class="alert-box error"><span>ERROR: </span>No Offers Currently Posted!</div>';
    else {
		echo '<div class="header_text">There are currently '.$num_rows.' active offers <br /></div>';
		echo '<div class="header_text">Need information on an item? Check the <a href="#" target="_blank">wiki!</a> <br /></div>';
		echo '<div class="header_text">This page only displays 25 offers, updated live! To view all offers, either search or go back to the home page.<br /></div>';
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
            echo '</tr>';
        while ($offers->fetch()) {
            echo '<tr>';
                echo '<td>'.$forumName.'</td>';
                echo '<td>'.$username.'</td>';
                echo '<td>'.$offerType.'</td>';
                echo '<td>'.ucwords($item).'</td>';
                echo '<td>'.number_format($amount).'</td>';
                echo '<td>'.number_format($price).'</td>';
                echo '<td>'.date("F j, Y / g:i a", strtotime($postDate)).'</td>';
        	echo '</tr>';
        }
        echo '</table>';
	}
?>