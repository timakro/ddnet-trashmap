<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="stylesheet.css">
<title>DDNet Trashmap - Suggest Mapconfig Command</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
?>

<a href="index.php">Main Page</a> -> <a href="create_server.php">Create Server</a> -> <a href="mapconfig_commands.php">Mapconfig Commands</a> -> Suggest Mapconfig Command
<h2>DDNet Trashmap - Suggest Mapconfig Command</h2>
<p>
You can suggest a new mapcofig command here.
Please don't misuse this service and only suggest commands with are useful and not dangerous.
</p>

<form enctype="multipart/form-data" action="suggest_mapconfig_command_handle.php" method="POST">

<h3>Commandname</h3>
<p>
The name of the suggested command.
The maximal length is <?php echo($config["maxlengthmapconfigcommand"]); ?> characters.
The commandname may only contain small letters and underscores.
This field may not be empty.
</p>
<input type="text" name="commandname" maxlength="<?php echo($config["maxlengthmapconfigcommand"]); ?>">

<br><br><br>
<input type="submit" value="Suggest Command">

</form>

</body>
</html>
