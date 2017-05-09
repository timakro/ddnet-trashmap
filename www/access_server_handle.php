<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
$START_SERVER = 4;
$STOP_SERVER = 5;
$CHANGE_MAP = 6;
$CHANGE_PASSWORD = 8;
$CHANGE_RCON = 9;
$CHANGE_PLAYERLIMIT = 10;
$DELETE_SERVER = 11;

if(!in_array($_POST["id"], array_keys($servers)))
    $settingstatus = "There are no servers saved with the given identifier";
elseif(!password_verify($_POST["key"], $servers[$_POST["id"]]["accesskey"]))
    $settingstatus = "The given accesskey does not match.";
else {
    $identifier = $_POST["id"];
    $info = $servers[$identifier];

    $errors =   ["Map" => [], "Password" => [], "Playerlimit" => [], "Rcon" => [], "Limit" => []];
    $warnings = ["Map" => [], "Password" => [], "Playerlimit" => [], "Rcon" => [], "Limit" => []];

    if($_POST["action"] == "start") {
        $running = 0;
        foreach($servers as $id => $data)
            if($data["running"])
                $running += 1;
        if($running >= $config["maxrunningservers"])
            array_push($errors["Limit"], "The maximum count of running servers is already reached, the server couldn't be started");
        elseif($info["running"])
            array_push($warnings["Limit"], "The server was already running");

    } elseif($_POST["action"] == "stop") {
        if(!$info["running"])
            array_push($warnings["Limit"], "The server was already offline");

    } elseif($_POST["action"] == "map") {
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

    } elseif($_POST["action"] == "password") {
        if(strlen($_POST["password"]) > $config["maxlengthpassword"])
            array_push($errors["Password"], "Field contains too many characters");
    } elseif($_POST["action"] == "rcon") {
        if(!$_POST["rcon"]) {
            array_push($warnings["Rcon"], "Field is empty");
            $_POST["rcon"] = $config["defaultrcon"];
        } elseif(strlen($_POST["rcon"]) > $config["maxlengthrcon"]) {
            array_push($warnings["Rcon"], "Field contains too many characters");
            $_POST["rcon"] = $config["defaultrcon"];
        }

    } elseif($_POST["action"] == "playerlimit") {
        if($info["running"])
            array_push($errors["Playerlimit"], "The server is running at the moment");
        elseif(!$_POST["playerlimit"] || !ctype_digit($_POST["playerlimit"])) {
            array_push($warnings["Playerlimit"], "Value is not a valid number");
            $_POST["playerlimit"] = $config["defaultplayers"];
        } elseif(intval($_POST["playerlimit"]) < $config["minplayers"] || intval($_POST["playerlimit"]) > $config["maxplayers"]) {
            array_push($warnings["Playerlimit"], "Value is not in range");
            $_POST["playerlimit"] = $config["defaultplayers"];
        }
    }

    $success = true;
    $errors = array_filter($errors);
    $warnings = array_filter($warnings);
    if (!empty($errors)) {
        $success = false;
    }

    $link = "access_server.php?".http_build_query(["id" => $_POST["id"], "key" => $_POST["key"]]);
    if($success) {
        if($_POST["action"] == "start") {
            file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                ["type" => $START_SERVER,
                 "identifier" => $identifier]
            )."\n");
            $settingstatus = "Successfully started the server";
        } elseif($_POST["action"] == "stop") {
            file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                ["type" => $STOP_SERVER,
                 "identifier" => $identifier]
            )."\n");
            $settingstatus = "Successfully stopped the server";
        } elseif($_POST["action"] == "map") {
            $mapfile = tempnam("/srv/trashmap/upload", "");
            move_uploaded_file($_FILES["map"]["tmp_name"], $mapfile);
            file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                ["type" => $CHANGE_MAP,
                 "identifier" => $identifier,
                 "mapfile" => $mapfile,
                 "mapname" => $mapname]
            )."\n");
            $settingstatus = "Successfully changed the map";
        } elseif($_POST["action"] == "password") {
            file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                ["type" => $CHANGE_PASSWORD,
                 "identifier" => $identifier,
                 "password" => $_POST["password"] ? $_POST["password"] : null]
            )."\n");
            $settingstatus = "Successfully changed the password";
        } elseif($_POST["action"] == "rcon") {
            file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                ["type" => $CHANGE_RCON,
                 "identifier" => $identifier,
                 "rcon" => $_POST["rcon"]]
            )."\n");
            $settingstatus = "Successfully changed the rcon";
        } elseif($_POST["action"] == "playerlimit") {
            file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                ["type" => $CHANGE_PLAYERLIMIT,
                 "identifier" => $identifier,
                 "playerlimit" => $_POST["playerlimit"]]
            )."\n");
            $settingstatus = "Successfully changed the playerlimit";
        } elseif($_POST["action"] == "delete") {
            file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                ["type" => $DELETE_SERVER,
                 "identifier" => $identifier]
            )."\n");
            $settingstatus = "Successfully deleted the server";
        } else {
            $settingstatus = "No such action";
            $success = false;
        }
    }
    else {
        $settingstatus = "Failed to change setting because an error occurred";
        $success = false;
    }
}
session_start();
$_SESSION['changedsetting'] = true;
$_SESSION['settingstatus'] = $settingstatus;
$_SESSION['settingstatus_success'] = $success;
$_SESSION['settingstatus_errors'] = $errors;
$_SESSION['settingstatus_warnings'] = $warnings;
if(!$error) {
  // Make sure the server recognizes that a setting changed
  sleep($config["tickseconds"]);
}
header("Location: $link");
