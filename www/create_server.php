<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DDNet Trashmap - Create Server</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
?>

<h2>DDNet Trashmap - Create Server</h2>
<p>
When you submit this form a new server with the map you uploaded will be started.
After it started the server will be running for <?php echo($config["joinminutes"]); ?> minutes until it will start to check regularly if there are players on the server.
If the server is empty it will be stopped automatically.
You will also get a link to control the server after you created it.
The maximal testing time until the server will be stopped automatically is <?php echo($config["stophours"]); ?> hours.
If a servers was not running for <?php echo($config["deletedays"]); ?> days it will be deleted automatically.
</p>

<form enctype="multipart/form-data" action="create_server_handle.php" method="POST">

<h3>Label</h3>
<p>
The label to display in the servername.
The maximal length is <?php echo($config["maxlengthlabel"]); ?> characters.
This field may not be empty.
This can't be changed after creating the server.
</p>
<input type="text" name="label" maxlength="<?php echo($config["maxlengthlabel"]); ?>">

<h3>Accesskey</h3>
<p>
The key required to control the server via the webinterface.
The maximal length is <?php echo($config["maxlengthaccesskey"]); ?> characters.
Please don't choose an important key since it is transmitted unencrypted.
This field may not be empty.
This can't be changed after creating the server.
</p>
</p>
<input type="text" name="accesskey" maxlength="<?php echo($config["maxlengthaccesskey"]); ?>">

<h3>Map</h3>
<p>
The map file to upload on the server.
The maximal file size is <?php echo($config["mapsizehuman"]); ?>.
The filename has to end with '.map'.
If there are any critical characters in the filename it will be adjusted.
If the uploaded file is no valid map the server won't start.
This file is required to start the server.
</p>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo($config["mapsize"]); ?>">
<input type="file" name="map">

<h3>Mapconfig</h3>
<p>
The mapconfig to use for the map.
The maximal file size is <?php echo($config["configsizehuman"]); ?>.
A list of allowed commands in the mapconfig can be found <a href="mapconfig_commands.php">here</a>.
This file is optional.
</p>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo($config["configsize"]); ?>">
<input type="file" name="mapconfig">

<h3>Password</h3>
<p>
The password required to join the server.
The maximal length is <?php echo($config["maxlengthpassword"]); ?> characters.
Please don't choose an important password since it is transmitted unencrypted.
This field is optional.
</p>
<input type="text" name="password" maxlength="<?php echo($config["maxlengthpassword"]); ?>">

<h3>Rcon</h3>
<p>
The rcon password required to access the server console ingame.
The maximal length is <?php echo($config["maxlengthrcon"]); ?> characters.
Please don't choose an important password since it is transmitted unencrypted.
A list of allowed commands in the rcon console can be found <a href="rcon_commands.php">here</a>.
If this value is empty or too long the default rcon password '<?php echo($config["defaultrcon"]); ?>' will be used.
</p>
<input type="text" name="rcon" maxlength="<?php echo($config["maxlengthrcon"]); ?>" value="<?php echo($config["defaultrcon"]); ?>">

<h3>Playerlimit</h3>
<p>
The maximal number of players on the server.
The number has to be between <?php echo($config["minplayers"]); ?> and <?php echo($config["maxplayers"]); ?>.
If this value isn't valid the default value <?php echo($config["defaultplayers"]); ?> will be used.
</p>
<input type="number" name="playerlimit" value="<?php echo($config["defaultplayers"]); ?>" min="<?php echo($config["minplayers"]); ?>" max="<?php echo($config["maxplayers"]); ?>">

<br><br><br>
<input type="submit" value="Create Server">

</form>

</body>
</html>
