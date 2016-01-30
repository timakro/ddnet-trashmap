<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="stylesheet.css">
<title>DDNet Trashmap - Access Server</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
?>

<a href=".">Main Page</a> -> <a href="server_list.php">Server List</a> -> Access Server
<h2>DDNet Trashmap - Access Server</h2>
<p>
<?php
if(!in_array($_GET["id"], array_keys($servers))) {
    echo("There is no server saved with the given identifier.\n");
    echo("</p>\n");
} elseif(!password_verify($_GET["key"], $servers[$_GET["id"]]["accesskey"])) {
    echo("The given accesskey does not match.\n");
    echo("</p>\n");
} else {
    $identifier = $_GET["id"];
    $info = $servers[$identifier];
?>
You can control your server on this site.
Please add this link to your favorites and use it everytime you want to test a map.
You can share this link with other players to give them access to the server.
You can also add the teeworlds server to your favorites.
<?php
$link = "https://trashmap.timakro.de/access_server.php?".http_build_query(["id" => $_GET["id"], "key" => $_GET["key"]]);
if(!$_SERVER["HTTPS"])
    echo("Click <a href=\"".$link."\">here</a> for an encrypted connection.\n");
?>
</p>

<h3>Server Status</h3>
<p>
Your server is <?php echo($info["running"] ? "running" : "offline"); ?> at the moment.
<?php
    if($info["running"])
        echo("The ip of your server is 'timakro.de:".$info["port"]."' or '84.38.65.222:".$info["port"]."'.\n");
?>
</p>
<table cellpadding="5" border="1">
<?php
    if($info["running"]) {
        echo("<tr><th>Label</th><th>Port</th><th>Map</th><th>Mapconfig</th><th>Rcon</th><th>Password</th><th>Playercount</th><th>Runtime</th></tr>\n");
        echo("<tr><td>".htmlentities($info["label"])."</td><td>".$info["port"]."</td><td>".htmlentities($info["mapname"])."</td><td>".($info["mapconfig"] == null ? "false" : "true")."</td><td>".($info["rcon"] == $config["defaultrcon"] ? "default" : "custom")."</td><td>".($info["password"] == null ? "false" : "true")."</td><td>".count($info["clientids"])." / ".$info["playerlimit"]."</td><td>".$info["runtimestring"]."</td></tr>\n");
    } else {
        echo("<tr><th>Label</th><th>Port</th><th>Map</th><th>Mapconfig</th><th>Rcon</th><th>Password</th><th>Playercount</th></tr>\n");
        echo("<tr><td>".htmlentities($info["label"])."</td><td>".$info["port"]."</td><td>".htmlentities($info["mapname"])."</td><td>".($info["mapconfig"] == null ? "false" : "true")."</td><td>".($info["rcon"] == $config["defaultrcon"] ? "default" : "custom")."</td><td>".($info["password"] == null ? "false" : "true")."</td><td>- / ".$info["playerlimit"]."</td></tr>\n");
    }
?>
</table>

<h3><?php echo($info["running"] ? "Stop" : "Start"); ?></h3>
<p>
Use this button to <?php echo($info["running"] ? "stop" : "start"); ?> the server.
Later you can <?php echo($info["running"] ? "start" : "stop"); ?> it again on this site.
</p>
<form enctype="multipart/form-data" action="access_server_handle.php" method="POST">
<input type="hidden" name="id" value="<?php echo($_GET["id"]); ?>">
<input type="hidden" name="key" value="<?php echo($_GET["key"]); ?>">
<input type="hidden" name="action" value="<?php echo($info["running"] ? "stop" : "start"); ?>">
<input type="submit" value="<?php echo($info["running"] ? "Stop Server" : "Start Server"); ?>">
</form>

<h3>Map</h3>
<p>
The map file to upload on the server.
The maximal file size is <?php echo($config["mapsizehuman"]); ?>.
The filename has to end with '.map'.
If there are any critical characters in the filename it will be adjusted.
If the uploaded file is no valid map the server can't run.
</p>
<form enctype="multipart/form-data" action="access_server_handle.php" method="POST">
<input type="hidden" name="id" value="<?php echo($_GET["id"]); ?>">
<input type="hidden" name="key" value="<?php echo($_GET["key"]); ?>">
<input type="hidden" name="action" value="map">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo($config["mapsize"]); ?>">
<input type="file" name="map">
<br><br>
<input type="submit" value="Change Map">
</form>

<h3>Mapconfig</h3>
<p>
The mapconfig to use for the map.
The maximal file size is <?php echo($config["configsizehuman"]); ?>.
A list of allowed commands in the mapconfig can be found <a href="mapconfig_commands.php">here</a>.
Select no file to delete the current mapconfig.
</p>
<form enctype="multipart/form-data" action="access_server_handle.php" method="POST">
<input type="hidden" name="id" value="<?php echo($_GET["id"]); ?>">
<input type="hidden" name="key" value="<?php echo($_GET["key"]); ?>">
<input type="hidden" name="action" value="mapconfig">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo($config["configsize"]); ?>">
<input type="file" name="mapconfig">
<br><br>
<input type="submit" value="Change Mapconfig">
</form>

<h3>Password</h3>
<p>
Change the password required to join the server.
The maximal length is <?php echo($config["maxlengthpassword"]); ?> characters.
Please don't choose an important password since it is <?php if(!$_SERVER["HTTPS"]) echo("transmitted and "); ?>stored unencrypted.
Leave this field empty to open the server for everybody.
</p>
<form enctype="multipart/form-data" action="access_server_handle.php" method="POST">
<input type="hidden" name="id" value="<?php echo($_GET["id"]); ?>">
<input type="hidden" name="key" value="<?php echo($_GET["key"]); ?>">
<input type="hidden" name="action" value="password">
<input type="text" name="password" maxlength="<?php echo($config["maxlengthpassword"]); ?>">
<br><br>
<input type="submit" value="Change Password">
</form>

<h3>Rcon</h3>
<p>
Change the rcon password required to access the server console ingame.
The maximal length is <?php echo($config["maxlengthrcon"]); ?> characters.
Please don't choose an important password since it is <?php if(!$_SERVER["HTTPS"]) echo("transmitted and "); ?>stored unencrypted.
A list of allowed commands in the rcon console can be found <a href="rcon_commands.php">here</a>.
If this value is empty or too long the default rcon password '<?php echo($config["defaultrcon"]); ?>' will be used.
</p>
<form enctype="multipart/form-data" action="access_server_handle.php" method="POST">
<input type="hidden" name="id" value="<?php echo($_GET["id"]); ?>">
<input type="hidden" name="key" value="<?php echo($_GET["key"]); ?>">
<input type="hidden" name="action" value="rcon">
<input type="text" name="rcon" maxlength="<?php echo($config["maxlengthrcon"]); ?>" value="<?php echo($config["defaultrcon"]); ?>">
<br><br>
<input type="submit" value="Change Rcon">
</form>

<h3>Playerlimit</h3>
<p>
Change the maximal number of players on the server.
The number has to be between <?php echo($config["minplayers"]); ?> and <?php echo($config["maxplayers"]); ?>.
If this value isn't valid the default value <?php echo($config["defaultplayers"]); ?> will be used.
This can only be changed if the server is offline.
</p>
<form enctype="multipart/form-data" action="access_server_handle.php" method="POST">
<input type="hidden" name="id" value="<?php echo($_GET["id"]); ?>">
<input type="hidden" name="key" value="<?php echo($_GET["key"]); ?>">
<input type="hidden" name="action" value="playerlimit">
<input type="number" name="playerlimit" value="<?php echo($config["defaultplayers"]); ?>" min="<?php echo($config["minplayers"]); ?>" max="<?php echo($config["maxplayers"]); ?>">
<br><br>
<input type="submit" value="Change Playerlimit">
</form>

<h3>Delete</h3>
<p>
Use this button to delete the server.
You can't recover the server after it has been deleted.
</p>
<form enctype="multipart/form-data" action="access_server_handle.php" method="POST">
<input type="hidden" name="id" value="<?php echo($_GET["id"]); ?>">
<input type="hidden" name="key" value="<?php echo($_GET["key"]); ?>">
<input type="hidden" name="action" value="delete">
<input type="submit" value="Delete Server">
</form>
<?php
}
?>

</body>
</html>
