<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="stylesheet.css">
<title>DDNet Trashmap - Rcon Commands</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
?>

<a href=".">Main Page</a> -> <a href="create_server.php">Create Server</a> -> Rcon Commands
<h2>DDNet Trashmap - Rcon Commands</h2>
<p>
When you have the rcon password of a server you can only execute specifically allowed commands.
</p>

<h3>Allowed Commands</h3>
<p>
This is a list of all allowed commands in the rcon console.
</p>
<ul>
<?php
foreach($data["storage"]["allowed_rcon"] as $command)
    echo("<li>".$command."</li>\n");
?>
</ul>

<h3>Suggest Command</h3>
There are probably forbidden commands that are not dangerous and would be useful in the rcon console.
You can suggest new commands <a href="suggest_rcon_command.php">here</a>.
<p>

</body>
</html>
