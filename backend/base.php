<!-- 
	{SMF Market - simple item marketplace tied into SMF}
    Copyright (C) {2015}  {Ryan 'iBeNifty' Malacina}
    You can find the full license included with these files.
-->

<?php
include("config.php");
@$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBItems);
?>
<?php
$search = "%{$_GET['term']}%";
$json = array();
if (@$autocomplete = $conn->prepare("SELECT * FROM `list` WHERE `name` LIKE ?")) {
	@$autocomplete->bind_param('s', $search);
	@$autocomplete->execute();
	@$data = $autocomplete->get_result();
	while ($row = $data->fetch_assoc()) {
		$json[] = array('value'=> ucwords($row['name']), 'label'=> ucwords($row['name']));
	}
}
sort($json);
echo json_encode($json);
?>