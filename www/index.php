<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="stylesheet.css">
<title>DDNet Trashmap</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
?>

Main Page
<h2>DDNet Trashmap</h2>
<p>
DDNet Trashmap is a service for mappers who can't host their own server.
You can create a testing server here and test your map alone or with other players.
</p>

<h3>Running Servers</h3>
<p>
<?php
$rows = [];
foreach($servers as $identifier => $info) {
    if($info["running"])
        array_push($rows, "<tr><td>".htmlentities($info["label"])."</td><td>".$info["port"]."</td><td>".htmlentities($info["mapname"])."</td><td>".($info["mapconfig"] == null ? "false" : "true")."</td><td>".($info["rcon"] == $config["defaultrcon"] ? "default" : "custom")."</td><td>".($info["password"] == null ? "false" : "true")."</td><td>".count($info["clientids"])."/".$info["playerlimit"]."</td><td>".$info["runtimestring"]."</td><td><form action=\"access_server.php\" method=\"GET\"><input type=\"hidden\" name=\"id\" value=\"".$identifier."\"><input type=\"text\" name=\"key\" maxlength=\"".$config["maxlengthaccesskey"]."\"><input type=\"submit\" value=\"Access\"></form></td></tr>\n");
}
?>
If you lost your access key you can find a list of all servers <a href="server_list.php">here</a>.
There are currently <?php echo(count($rows)); ?> servers running.
The maximal count of running servers is <?php echo($config["maxrunningservers"]); ?>.
<?php if(!$_SERVER["HTTPS"]) { ?>
Click <a href="https://trashmap.timgame.de">here</a> for an encrypted connection if you want to access a server from this site and transmit your accesskey encrypted.
<?php } ?>
</p>
<table cellpadding="5" border="1">
<tr><th>Label</th><th>Port</th><th>Map</th><th>Mapconfig</th><th>Rcon</th><th>Password</th><th>Playercount</th><th>Runtime</th><th>Accesskey</th></tr>
<?php
foreach($rows as $row) {
    echo($row);
}
?>
</table>

<h3>Create Server</h3>
<p>
<?php
$running = 0;
foreach($servers as $identifier => $data)
    if($data["running"])
        $running += 1;
if($running >= $config["maxrunningservers"])
    echo("You can't create any more servers because the maximal count of running servers is already reached.\n");
else
    echo("Please don't misuse this service or interfere other players who are using this service.\nOnly create a new server if you have no offline server left.\nIf you agree with this you can create a new server <a href=\"create_server.php\">here</a>.\n");
?>
</p>

<h3>Development</h3>
<p>
DDNet Trashmap is developed by <a href="https://github.com/timgame">timgame</a>, ingame name <a href="http://forum.ddnet.tw/memberlist.php?mode=viewprofile&u=52">DoNe</a>.
The sourcecode is hosted on <a href="https://github.com/timgame/DDNet-Trashmap">github</a>.
Please report bugs on the github page or on the related <a href="http://forum.ddnet.tw/viewtopic.php?f=6&t=1764">thread</a> in the ddnet forum.
</p>

</body>
</html>
