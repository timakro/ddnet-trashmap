<!DOCTYPE html>
<html lang="en">
<head>
<?php include "includes/head.inc.php";?>
<title>DDNet Trashmap - Create Server</title>
</head>
<body>
<?php include "includes/openingBody.inc.php";?>

<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
$CREATE_SERVER = 3;
?>

<div class="breadcrumbs">
    <div class="crumb">
        <a href=".">Main Page</a>
    </div>
    <div class="crumb">
        Create Server
    </div>
</div>

<div class="main">
    <section class="page_branding">
        <h2 class="page_title">DDNet Trashmap - Create Server</h2>
        <div class="page_description">
            <?php
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
            $warnings = array_filter($warnings);
            if (!empty($errors)) {
                $success = false;
            }

            if($success) {
                $identifier = uniqid();
                $link = "access_server.php?".http_build_query(["id" => $identifier, "key" => $raw_accesskey]);
                $mapfile = tempnam("/srv/trashmap/upload", "");
                move_uploaded_file($_FILES["map"]["tmp_name"], $mapfile);
                file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                    ["type" => $CREATE_SERVER,
                     "identifier" => $identifier,
                     "label" => $_POST["label"],
                     "accesskey" => $_POST["accesskey"],
                     "mapfile" => $mapfile,
                     "mapname" => $mapname,
                     "password" => $_POST["password"] ? $_POST["password"] : null,
                     "rcon" => $_POST["rcon"],
                     "playerlimit" => $_POST["playerlimit"],
                     "userip" => $_SERVER["REMOTE_ADDR"]]
                )."\n"); ?>
                <p>Successfully created a new server. You can access the server via the webinterface using this link <a href="<?php $link ?>"></a>. Please save this link to your bookmarks and use it everytime you want to test a map. You can also login using a form at the server list page and your accesskey. You can ofcourse share the link or your accesskey with other players.</p>
            <?php
            }
            else { ?>
                <p>Failed to create a new server because an error occurred.</p>
                <a href="create_server.php" class="button negative">Back</a>
            <?php } ?>
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
