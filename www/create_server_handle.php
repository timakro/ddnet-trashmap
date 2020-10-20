<?php
$data = json_decode(file_get_contents("/srv/trashmap/srv/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
$CREATE_SERVER = 3;
$errors =   ["Label" => [], "Accesskey" => [], "Map" => [], "Password" => [], "Playerlimit" => [], "Rcon" => [], "Limit" => []];
$warnings = ["Label" => [], "Accesskey" => [], "Map" => [], "Password" => [], "Playerlimit" => [], "Rcon" => [], "Limit" => []];

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
    array_push($errors["Map"], "Maximum file size exceeded");
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
    array_push($errors["Limit"], "The maximum count of saved servers is already reached");
$sameip = 0;
$running = 0;
foreach($servers as $identifier => $data) {
    if($data["userip"] == $_SERVER["REMOTE_ADDR"])
        $sameip += 1;
    if($data["running"])
        $running += 1;
}
if($sameip >= $config["maxserversperip"])
    array_push($errors["Limit"], "The maximum count of saved servers per ip is already reached");
if(!$errors["Limit"] && $running >= $config["maxrunningservers"])
    array_push($warnings["Limit"], "The maximum count of running servers is already reached, the server couldn't be started");

if(in_array($_SERVER["REMOTE_ADDR"], $config["bannedips"]))
    array_push($errors["Limit"], "Your ip is banned");

$success = true;
$errors = array_filter($errors);
if (!empty($errors)) {
    $success = false;
}

if($success) {
    $identifier = uniqid();
    $link = "access_server.php?".http_build_query(["id" => $identifier, "key" => $raw_accesskey]);
    $mapfile = tempnam("/srv/trashmap/upload", "");
    $mapfile7 = tempnam("/srv/trashmap/upload", "");
    move_uploaded_file($_FILES["map"]["tmp_name"], $mapfile);
    exec("/srv/trashmap/srv/build/map_convert_07 ".escapeshellarg($mapfile)." ".escapeshellarg($mapfile7), $map_conv_out);
    foreach($map_conv_out as $line) {
        if(preg_match("/\[map_convert_07\]: ".preg_quote($mapfile, "/").": (.*)$/", $line, $matches))
            array_push($warnings["Map"], $matches[1]);
    }
    chmod($mapfile7, 0644);
    file_put_contents("/srv/trashmap/srv/daemon_input.fifo", json_encode(
        ["type" => $CREATE_SERVER,
         "identifier" => $identifier,
         "label" => $_POST["label"],
         "accesskey" => $_POST["accesskey"],
         "mapfile" => $mapfile,
         "mapfile7" => $mapfile7,
         "mapname" => $mapname,
         "password" => $_POST["password"] ? $_POST["password"] : null,
         "rcon" => $_POST["rcon"],
         "playerlimit" => $_POST["playerlimit"],
         "userip" => $_SERVER["REMOTE_ADDR"]]
    )."\n");
    session_start();
    $_SESSION['newlycreatedserver'] = true;
    $_SESSION['servercreation_warnings'] = array_filter($warnings);
    // Make sure the server recognizes that a new server has been created
    sleep($config["tickseconds"]);
    header("Location: $link");
}
else {
    session_start();
    $_SESSION['unsuccessfulservercreation'] = true;
    $_SESSION['servercreation_errors'] = $errors;
    $_SESSION['servercreation_warnings'] = array_filter($warnings);
    header("Location: create_server.php");
}
