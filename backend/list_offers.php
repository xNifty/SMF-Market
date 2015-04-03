<?php
	
	include("database.php");
	@$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);

		@$offers = $conn->prepare("SELECT `ID`, `offerType`, `username`, `item`, `amount`, `price`, `postDate` FROM `entries` ORDER BY `ID` DESC LIMIT $startpage, $perpage");
        @$offers->execute();
        @$offers->store_result();
        @$offers->bind_result($id, $offerType, $username, $item, $amount, $price, $postDate);
        @$num_rows = $offers->num_rows;
        if ($num_rows == 0)
            echo '<div class="alert-box error"><span>ERROR: </span>No Offers Currently Posted!</div>';
        else {
			echo '<div class="header_text">There are currently '.$num_rows.' active offers <br /></div>';
			echo '<div class="header_text">Need information on an item? Check the <a href="http://yolocatz.x10.mx/wiki" target="_blank">wiki!</a> <br /></div>';
			echo '<div class="header_text">This page only displays 25 offers; please use the page listing at the bottom for more or try narrowing with the search bar. <br /></div>';
			echo '<div class="header_text notice">Notice: we do not confirm any one person owns the item they are "selling"; please report those abusing the system. <br /></div>';
			echo '<hr>';
            echo '<table class="displayoffers">';
                echo '<tr>';
                    echo '<th>User</th>';
                    echo '<th>Offer Type</th>';
                    echo '<th>Item</th>';
                    echo '<th>Amount</th>';
                    echo '<th>Price</th>';
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
                            </form><button type="submit" form="delete" value="'.$id.'" name="id" onclick="return confirm(\'Are you sure you wish to delete this entry?\');">Delete</button></td>';
                	else
                  	echo '<td><button type="button" disabled>Delete</button></td>';
            	echo '</tr>';
            }
            echo '</table>';
    	}
?>