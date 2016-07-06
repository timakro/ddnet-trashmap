<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="stylesheet.css">
<title>DDNet Trashmap - Server List</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
?>

<a href=".">Main Page</a> -> Server List
<h2>DDNet Trashmap - Server List</h2>
<p>
This is a list of all servers saved.
You can use the search function of your browser to find a server with a known label.
There are currently <?php echo(strval(count($servers))); ?> servers saved.
The maximal count of saved servers is <?php echo($config["maxservers"]); ?>.
</p>

<h3>Running Servers</h3>
<p>
<?php
$rows = [];
foreach($servers as $identifier => $info) {
    if($info["running"])
        array_push($rows, "<tr><td>".htmlentities($info["label"])."</td><td>".$info["port"]."</td><td>".htmlentities($info["mapname"])."</td><td>".($info["rcon"] == $config["defaultrcon"] ? "default" : "custom")."</td><td>".($info["password"] == null ? "false" : "true")."</td><td>".count($info["clientids"])." / ".$info["playerlimit"]."</td><td>".$info["runtimestring"]."</td><td><form action=\"access_server.php\" method=\"GET\"><input type=\"hidden\" name=\"id\" value=\"".$identifier."\"><input type=\"text\" name=\"key\" maxlength=\"".$config["maxlengthaccesskey"]."\"><input type=\"submit\" value=\"Access\"></form></td></tr>\n");
}
?>
There are currently <?php echo(strval(count($rows))); ?> servers running.
The maximal count of running servers is <?php echo($config["maxrunningservers"]); ?>.
</p>
<table cellpadding="5" border="1">
<tr><th>Label</th><th>Port</th><th>Map</th><th>Rcon</th><th>Password</th><th>Playercount</th><th>Runtime</th><th>Accesskey</th></tr>
<?php
foreach($rows as $row) {
    echo($row);
}
?>
</table>

<h3>Offline Servers</h3>
<p>
<?php
$rows = [];
foreach($servers as $identifier => $info) {
    if(!$info["running"])
        array_push($rows, "<tr><td>".htmlentities($info["label"])."</td><td>".$info["port"]."</td><td>".htmlentities($info["mapname"])."</td><td>".($info["rcon"] == $config["defaultrcon"] ? "default" : "custom")."</td><td>".($info["password"] == null ? "false" : "true")."</td><td>- / ".$info["playerlimit"]."</td><td><form action=\"access_server.php\" method=\"GET\"><input type=\"hidden\" name=\"id\" value=\"".$identifier."\"><input type=\"text\" name=\"key\" maxlength=\"".$config["maxlengthaccesskey"]."\"><input type=\"submit\" value=\"Access\"></form></td></tr>\n");
}
?>
There are currently <?php echo(strval(count($rows))); ?> offline servers saved.
</p>
<table cellpadding="5" border="1">
<tr><th>Label</th><th>Port</th><th>Map</th><th>Rcon</th><th>Password</th><th>Playercount</th><th>Accesskey</th></tr>
<?php
foreach($rows as $row) {
    echo($row);
}
?>
</table>

</body>
</html>
