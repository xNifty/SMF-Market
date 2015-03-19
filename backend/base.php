<?php
$DBServer = 'localhost';
$DBUser = 'root';
$DBPass = '';
$DBName = 'items';
@$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
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
echo json_encode($json);
?>