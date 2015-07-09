<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DDNet Trashmap - Mapconfig Commands</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
?>

<h2>DDNet Trashmap - Mapconfig Commands</h2>
<p>
When uploading a mapconfig only specifically allowed commands can be used in it.
If forbidden commands are used in a mapconfig they will be removed and a warning will be displayed.
</p>

<h3>Allowed Commands</h3>
<p>
This is a list of all allowed commands in a mapconfig.
</p>
<ul>
<?php
foreach(array_keys($data["storage"]["allowed_commands"]) as $command)
    echo("<li>".$command."</li>\n");
?>
</ul>

<h3>Suggest Command</h3>
There are probably forbidden commands that are not dangerous and would be useful for a mapconfig.
You can suggest new commands <a href="suggest_mapconfig_command.php">here</a>.
<p>

</body>
</html>
