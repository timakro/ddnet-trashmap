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
$SUGGEST_RCON = 12;
?>

<a href="index.php">Main Page</a> -> <a href="create_server.php">Create Server</a> -> <a href="rcon_commands.php">Rcon Commands</a> -> Suggest Rcon Command
<h2>DDNet Trashmap - Suggest Rcon Command</h2>
<p>
<?php
$errors =   ["Commandname" => []];
$warnings = ["Commandname" => []];

if(!$_POST["commandname"])
    array_push($errors["Commandname"], "Field is empty");
if(strlen($_POST["commandname"]) > $config["maxlengthrconcommand"])
    array_push($errors["Commandname"], "Field contains too many characters");
if(!preg_match("/^[a-z_]*$/", $_POST["commandname"]))
    array_push($errors["Commandname"], "Contains forbidden characters");
if(!$errors["Commandname"]) {
    if(in_array($_POST["commandname"], $data["storage"]["allowed_rcon"]))
        array_push($warnings["Commandname"], "Command already allowed");
    else {
        if(in_array($_POST["commandname"], $data["storage"]["suggested_rcon"]))
            array_push($warnings["Commandname"], "Command already suggested");
        }
}
$success = true;
foreach($errors as $type => $errormessages)
    foreach($errormessages as $errormessage) {
        echo("<span style=\"background-color:tomato;\">[".$type."] Error: ".$errormessage."</span><br>\n");
        $success = false;
    }
foreach($warnings as $type => $warningmessages)
    foreach($warningmessages as $warningmessage)
        echo("<span style=\"background-color:orange;\">[".$type."] Warning: ".$warningmessage."</span><br>\n");
echo("<br>\n");
if($success) {
    file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
        ["type" => $SUGGEST_RCON,
         "command" => $_POST["commandname"]]
    )."\n");
    echo("Thanks for suggesting a new command.\nClick <a href=\"suggest_rcon_command.php\">here</a> to get back.\n");
}
else
    echo("Failed to suggest a new command because an error occurred.\nClick <a href=\"suggest_rcon_command.php\">here</a> to get back.\n");
?>
</p>

</body>
</html>
