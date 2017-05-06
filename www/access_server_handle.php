<!DOCTYPE html>
<html lang="en">
<head>
<?php include "includes/head.inc.php";?>
<title>DDNet Trashmap - Access Server</title>
</head>
<body>
<?php include "includes/openingBody.inc.php";?>

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
?>

<div class="breadcrumbs">
    <div class="crumb">
        <a href=".">Main Page</a>
    </div>
    <div class="crumb">
        <a href="server_list.php">Server List</a>
    </div>
    <div class="crumb">
        Access Server        
    </div>
</div>

<div class="main">
    <section class="page_branding">
        <h2 class="page_title">DDNet Trashmap - Access Server</h2>
        <div class="page_description">
        <?php
        if(!in_array($_POST["id"], array_keys($servers)))
            echo("There is no server saved with the given identifier.\n");
        elseif(!password_verify($_POST["key"], $servers[$_POST["id"]]["accesskey"]))
            echo("The given accesskey does not match.\n");
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
                    )."\n"); ?>
                    <p>Successfully started the server.</p>
                    <a href="<?php echo $link; ?>" class="button negative">Back</a>
                    <?php
                } elseif($_POST["action"] == "stop") {
                    file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                        ["type" => $STOP_SERVER,
                         "identifier" => $identifier]
                    )."\n"); ?>
                    <p>Successfully stopped the server.</p>
                    <a href="<?php echo $link; ?>" class="button negative">Back</a>
                    <?php
                } elseif($_POST["action"] == "map") {
                    $mapfile = tempnam("/srv/trashmap/upload", "");
                    move_uploaded_file($_FILES["map"]["tmp_name"], $mapfile);
                    file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                        ["type" => $CHANGE_MAP,
                         "identifier" => $identifier,
                         "mapfile" => $mapfile,
                         "mapname" => $mapname]
                    )."\n"); ?>
                    <p>Successfully changed the map.</p>
                    <a href="<?php echo $link; ?>" class="button negative">Back</a>
                    <?php
                } elseif($_POST["action"] == "password") {
                    file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                        ["type" => $CHANGE_PASSWORD,
                         "identifier" => $identifier,
                         "password" => $_POST["password"] ? $_POST["password"] : null]
                    )."\n"); ?>
                    <p>Successfully changed the password.</p>
                    <a href="<?php echo $link; ?>" class="button negative">Back</a>
                    <?php
                } elseif($_POST["action"] == "rcon") {
                    file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                        ["type" => $CHANGE_RCON,
                         "identifier" => $identifier,
                         "rcon" => $_POST["rcon"]]
                    )."\n"); ?>
                    <p>Successfully changed the rcon.</p>
                    <a href="<?php echo $link; ?>" class="button negative">Back</a>
                    <?php
                } elseif($_POST["action"] == "playerlimit") {
                    file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                        ["type" => $CHANGE_PLAYERLIMIT,
                         "identifier" => $identifier,
                         "playerlimit" => $_POST["playerlimit"]]
                    )."\n"); ?>
                    <p>Successfully changed the playerlimit.</p>
                    <a href="<?php echo $link; ?>" class="button negative">Back</a>
                    <?php
                } elseif($_POST["action"] == "delete") {
                    file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                        ["type" => $DELETE_SERVER,
                         "identifier" => $identifier]
                    )."\n");?>
                    <p>Successfully deleted the server.</p>
                    <a href="<?php echo $link; ?>" class="button negative">Back</a>
                    <?php
                } else {?>
                    <p>No such action.</p>
                    <?php
                }
            }
            else { ?>
                <p>Failed to access the server because an error occurred</p>
                <a href="<?php echo $link; ?>" class="button negative">Try Again</a>
            <?php }
        }
        ?>
        </div>
    </section>

    <?php if(!empty($errors)): ?>
    <section>
        <div class="error_block">
        <?php
        foreach($errors as $type => $errormessages)
            foreach($errormessages as $errormessage): ?>
                    <div class="error_line">
                        <span class="error_type">[<?php echo $type; ?>]</span>
                        <span class="error_message"><?php echo $errormessage; ?></span>
                    </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif;?>

    <?php if(!empty($warnings)): ?>
    <section>
        <div class="warning_block">
        <?php
        foreach($warnings as $type => $warningmessages)
            foreach($warningmessages as $warningmessage): ?>
                    <div class="warning_line">
                        <span class="warning_type">[<?php echo $type; ?>]</span>
                        <span class="warning_message"><?php echo $warningmessage; ?></span>
                    </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

</body>
</html>
