<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DDNet Trashmap - Suggest Mapconfig Command</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
$SUGGEST_COMMAND = 0;
?>

<h2>DDNet Trashmap - Suggest Mapconfig Command</h2>
<p>
<?php
$errors =   ["Commandname" => []];
$warnings = ["Commandname" => []];

if(!$_POST["commandname"])
    array_push($errors["Commandname"], "Field is empty");
if(strlen($_POST["commandname"]) > $config["maxlengthmapconfigcommand"])
    array_push($errors["Commandname"], "Field contains too many characters");
if(!preg_match("/^[a-z_]*$/", $_POST["commandname"]))
    array_push($errors["Commandname"], "Contains forbidden characters");
if(!$errors["Commandname"]) {
    if(in_array($_POST["commandname"], array_keys($data["storage"]["allowed_commands"])))
        array_push($warnings["Commandname"], "Command already allowed");
    else {
        if(in_array($_POST["commandname"], $data["storage"]["suggested_commands"]))
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
        ["type" => $SUGGEST_COMMAND,
         "command" => $_POST["commandname"]]
    )."\n");
    echo("Thanks for suggesting a new command.\n");
}
else
    echo("Failed to suggest a new command because an error occurred.\n");
?>
</p>

</body>
</html>
