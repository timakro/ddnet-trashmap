<?php
$data = json_decode(file_get_contents("/srv/trashmap/srv/daemon_data.json"), true);
$config = $data["config"];
$SUGGEST_RCON = 12;

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
$errors = array_filter($errors);
$warnings = array_filter($warnings);
if (!empty($errors)) {
    $success = false;
}

if($success) {
    file_put_contents("/srv/trashmap/srv/daemon_input.fifo", json_encode(
        ["type" => $SUGGEST_RCON,
         "command" => $_POST["commandname"]]
    )."\n");
    $commandstatus = 'Thanks for suggesting a new command';
}
else {
    $commandstatus = 'Failed to suggest a new command because an error occurred.';
}

session_start();
$_SESSION['suggestedcommand'] = true;
$_SESSION['commandstatus'] = $commandstatus;
$_SESSION['commandstatus_success'] = $success;
$_SESSION['commandstatus_errors'] = $errors;
$_SESSION['commandstatus_warnings'] = $warnings;
header("Location: suggest_rcon_command.php");
