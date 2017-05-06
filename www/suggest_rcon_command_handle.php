<!DOCTYPE html>
<html lang="en">
<head>
<?php include "includes/head.inc.php";?>
<title>DDNet Trashmap - Suggest Rcon Command</title>
</head>
<body>
<?php include "includes/openingBody.inc.php";?>

<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
$SUGGEST_RCON = 12;
?>

<div class="breadcrumbs">
    <div class="crumb">
        <a href=".">Main Page</a>
    </div>
    <div class="crumb">
        <a href="create_server.php">Create Server</a>
    </div>
    <div class="crumb">
        <a href="rcon_commands.php">Rcon Commands</a>
    </div>
    <div class="crumb">
        Suggest Rcon Command
    </div>
</div>

<div class="main">
    <section class="page_branding">
        <h2 class="page_title">DDNet Trashmap - Suggest Rcon Command</h2>
        <div class="page_description">
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
        $errors = array_filter($errors);
        $warnings = array_filter($warnings);
        if (!empty($errors)) {
            $success = false;
        }

        if($success) {
            file_put_contents("/srv/trashmap/daemon_input.fifo", json_encode(
                ["type" => $SUGGEST_RCON,
                 "command" => $_POST["commandname"]]
            )."\n"); ?>
            <p>Thanks for suggesting a new command.</p>
            <a href="suggest_rcon_command.php" class="button negative">Back</a>
            <?php
        }
        else { ?>
            <p>Failed to suggest a new command because an error occurred.</p>
            <a href="suggest_rcon_command.php" class="button negative">Back</a>
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
