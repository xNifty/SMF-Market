<?php
include("config.php");
@$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBItems);
?>
<?php
$search = "%{$_GET['term']}%";
if (@$autocomplete = $conn->prepare("SELECT * FROM `list` WHERE `name` LIKE ?")) {
	@$autocomplete->bind_param('s', $search);
	@$autocomplete->execute();
	@$data = $autocomplete->get_result();
	while ($row = $data->fetch_assoc()) {
		$json[] = array('value'=> ucwords($row['name']), 'label'=> ucwords($row['name']));
	}
}
header('Content-Type: application/json');
sort($json);
echo json_encode($json);
?>