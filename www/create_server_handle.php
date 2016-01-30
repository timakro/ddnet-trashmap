<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="stylesheet.css">
<title>DDNet Trashmap - Create Server</title>
</head>
<body>
<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
$CREATE_SERVER = 3;
?>

<a href=".">Main Page</a> -> Create Server
<h2>DDNet Trashmap - Create Server</h2>
<p>
<?php
$errors =   ["Label" => [], "Accesskey" => [], "Map" => [], "Mapconfig" => [], "Password" => [], "Playerlimit" => [], "Rcon" => [], "Limit" => []];
$warnings = ["Label" => [], "Accesskey" => [], "Map" => [], "Mapconfig" => [], "Password" => [], "Playerlimit" => [], "Rcon" => [], "Limit" => []];

if(!$_POST["label"])
    array_push($errors["Label"], "Field is empty");
if(strlen($_POST["label"]) > $config["maxlengthlabel"])
    array_push($errors["Label"], "Field contains too many characters");

if(!$_POST["accesskey"])
    array_push($errors["Accesskey"], "Field is empty");
if(strlen($_POST["accesskey"]) > $config["maxlengthaccesskey"])
    array_push($errors["Accesskey"], "Field contains too many characters");
if(!$errors["Accesskey"]) {
    $raw_accesskey = $_POST["accesskey"];
    $_POST["accesskey"] = password_hash($_POST["accesskey"], PASSWORD_DEFAULT);
    if($_POST["accesskey"] == false)
        array_push($errors["Accesskey"], "Failed to hash accesskey");
}

if($_FILES["map"]["error"] == UPLOAD_ERR_NO_FILE)
    array_push($errors["Map"], "No file uploaded");
if($_FILES["map"]["error"] == UPLOAD_ERR_FORM_SIZE || $_FILES["map"]["size"] > $config["mapsize"])
    array_push($errors["Map"], "Maximal file size exceeded");
if($_FILES["map"]["error"] == UPLOAD_ERR_PARTIAL)
    array_push($errors["Map"], "File only partially uploaded");
if($_FILES["map"]["error"] == UPLOAD_ERR_INI_SIZE || $_FILES["map"]["error"] == UPLOAD_ERR_NO_TMP_DIR || $_FILES["map"]["error"] == UPLOAD_ERR_CANT_WRITE || $_FILES["map"]["error"] == UPLOAD_ERR_EXTENSION)
    array_push($errors["Map"], "This error wasn't caused by you, please contact a server administrator to fix the problem");
if(!$_FILES["map"]["name"])
    array_push($errors["Map"], "Filename is empty");
else {
    if(substr($_FILES["map"]["name"], -4) != ".map")
        array_push($errors["Map"], "Filename doesn't end with '.map'");
    else {
        $basename = substr($_FILES["map"]["name"], 0, -4);
        $mapname = preg_replace(["/[^\w\s\d\-_~,;:\[\]\(\).\\\]/", "/\s*(.*[^\s]|)\s*/"], ["", "$1"], $basename);
        if(!$mapname)
            array_push($errors["Map"], "Filename had to be adjusted and is now empty");
        elseif($mapname != $basename)
            array_push($warnings["Map"], "Filename contains critical characters and was adjusted");
    }
}

$mapconfig = [];
if($_FILES["mapconfig"]["error"] != UPLOAD_ERR_NO_FILE) {
    if($_FILES["mapconfig"]["error"] == UPLOAD_ERR_FORM_SIZE || $_FILES["mapconfig"]["size"] > $config["configsize"])
        array_push($errors["Mapconfig"], "Maximal file size exceeded");
    if($_FILES["mapconfig"]["error"] == UPLOAD_ERR_PARTIAL)
        array_push($errors["Mapconfig"], "File only partially uploaded");
    if($_FILES["mapconfig"]["error"] == UPLOAD_ERR_INI_SIZE || $_FILES["mapconfig"]["error"] == UPLOAD_ERR_NO_TMP_DIR || $_FILES["mapconfig"]["error"] == UPLOAD_ERR_CANT_WRITE || $_FILES["mapconfig"]["error"] == UPLOAD_ERR_EXTENSION)
        array_push($errors["Mapconfig"], "This error wasn't caused by you, please contact a server administrator to fix the problem");
    if(!$errors["Mapconfig"]) {
        $commands = preg_split("/(\n|;)/", file_get_contents($_FILES["mapconfig"]["tmp_name"]));
        $filtered = false;
        $allowed_commands = implode("|", $config["allowed_commands"]);
        foreach($commands as $command) {
            $match = [];
            if(preg_match("/^\s*((".$allowed_commands.")(.*[^\s]|)|)\s*$/", $command, $match)) {
                if($match[1])
                    array_push($mapconfig, $match[1].";");
            } else
                $filtered = true;
        }
        if($filtered) {
            if($mapconfig)
                array_push($warnings["Mapconfig"], "Mapconfig contained forbidden commands and was adjusted, you can see allowed commands <a href=\"mapconfig_commands.php\">here</a>");
            else
                array_push($errors["Mapconfig"], "Mapconfig contained forbidden commands, was adjusted and is empty now, you can see allowed commands <a href=\"mapconfig_commands.php\">here</a>");
        }
        elseif(!$mapconfig)
                array_push($errors["Mapconfig"], "Mapconfig is empty");
    }
}

if(strlen($_POST["password"]) > $config["maxlengthpassword"])
    array_push($errors["Password"], "Field contains too many characters");

if(!$_POST["rcon"]) {
    array_push($warnings["Rcon"], "Field is empty");
    $_POST["rcon"] = $config["defaultrcon"];
} elseif(strlen($_POST["rcon"]) > $config["maxlengthrcon"]) {
    array_push($warnings["Rcon"], "Field contains too many characters");
    $_POST["rcon"] = $config["defaultrcon"];
}

if(!$_POST["playerlimit"] || !ctype_digit($_POST["playerlimit"])) {
    array_push($warnings["Playerlimit"], "Value is not a valid number");
    $_POST["playerlimit"] = $config["defaultplayers"];
} elseif(intval($_POST["playerlimit"]) < $config["minplayers"] || intval($_POST["playerlimit"]) > $config["maxplayers"]) {
    array_push($warnings["Playerlimit"], "Value is not in range");
    $_POST["playerlimit"] = $config["defaultplayers"];
}

if(count($servers) >= $config["maxservers"])
    array_push($errors["Limit"], "The maximal count of saved servers is already reached");
else {
    $running = 0;
    foreach($servers as $identifier => $data)
        if($data["running"])
            $running += 1;
    if($running >= $config["maxrunningservers"])
        array_push($warnings["Limit"], "The maximal count of running servers is already reached, the server couldn't be started");
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
    $identifier = uniqid();
    $link = "access_server.php?".http_build_query(["id" => $identifier, "key" => $raw_accesskey]);
    $mapfile = tempnam("/tmp", "trashmap");
    move_uploaded_file($_FILES["map"]["tmp_name"], $mapfile);
    file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
        ["type" => $CREATE_SERVER,
         "identifier" => $identifier,
         "label" => $_POST["label"],
         "accesskey" => $_POST["accesskey"],
         "mapfile" => $mapfile,
         "mapname" => $mapname,
         "mapconfig" => $mapconfig ? $mapconfig : null,
         "password" => $_POST["password"] ? $_POST["password"] : null,
         "rcon" => $_POST["rcon"],
         "playerlimit" => $_POST["playerlimit"]]
    )."\n");
    echo("Successfully created a new server.\nYou can access the server via the webinterface unsing this link <a href=\"".$link."\">".$link."</a>.\nPlease save this link to your bookmarks and use it everytime you want to test a map.\nYou can also login using a form at the server list page and your accesskey.\nYou can ofcourse share the link or your accesskey with other players.\n");
}
else
    echo("Failed to create a new server because an error occurred.\nClick <a href=\"create_server.php\">here</a> to get back.");
?>
</p>

</body>
</html>
