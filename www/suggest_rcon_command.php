<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DDNet Trashmap - Suggest Rcon Command</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
?>

<a href="index.php">Main Page</a> -> <a href="create_server.php">Create Server</a> -> <a href="rcon_commands.php">Rcon Commands</a> -> Suggest Rcon Command
<h2>DDNet Trashmap - Suggest Rcon Command</h2>
<p>
You can suggest a new rcon command here.
Please don't misuse this service and only suggest commands with are useful and not dangerous.
</p>

<form enctype="multipart/form-data" action="suggest_rcon_command_handle.php" method="POST">

<h3>Commandname</h3>
<p>
The name of the suggested command.
The maximal length is <?php echo($config["maxlengthrconcommand"]); ?> characters.
The commandname may only contain small letters and underscores.
This field may not be empty.
</p>
<input type="text" name="commandname" maxlength="<?php echo($config["maxlengthrconcommand"]); ?>">

<br><br><br>
<input type="submit" value="Suggest Command">

</form>

</body>
</html>
