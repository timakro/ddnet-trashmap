<!DOCTYPE html>
<html lang="en">
<head>
<?php include "includes/head.inc.php";?>
<title>DDNet Trashmap - Suggest Rcon Command</title>
</head>
<body>

<?php
session_start();
if (isset($_SESSION['suggestedcommand'])):
    $commandstatus = $_SESSION['commandstatus'];
    $success = $_SESSION['commandstatus_success'];
    $errors = $_SESSION['commandstatus_errors'];
    $warnings = $_SESSION['commandstatus_warnings'];
?>
<script>
    // instanciate new modal
    var modal = new tingle.modal({
        closeMethods: ['overlay', 'escape'],
        cssClass: ['aside'],
        closeLabel: "Close",
        onOpen: function() {
            console.log('modal open');
        },
        onClose: function() {
            console.log('modal closed');
        },
        beforeClose: function() {
            // here's goes some logic
            // e.g. save content before closing the modal
            return true; // close the modal
            return false; // nothing happens
        }
    });

    var content = "<h2 class=\"modal_title <?php if (!$success) { echo "error_head"; };?>\"><?php echo "$commandstatus";?></h2>";
    var content = content + "<?php if(!empty($errors)) {
            echo '<div class=\"error_block\">';
            foreach($errors as $type => $errormessages) {
                foreach($errormessages as $errormessage) {
                        echo '<div class=\"error_line\">' . '<span class=\"error_type\">[' . $type . ']</span><span class=\"error_message\">' . $errormessage . '</span></div>';
                }
            }
            echo '</div>';
        }?>";

        var content = content + "<?php if(!empty($warnings)) {
            echo '<div class=\"warning_block\">';
            foreach($warnings as $type => $warningmessages) {
                foreach($warningmessages as $warningmessage) {
                        echo '<div class=\"warning_line\">' . '<span class=\"warning_type\">[' . $type . ']</span><span class=\"warning_message\">' . $warningmessage . '</span></div>';
                }
            }
            echo '</div>';
        }?>";

    modal.setContent(content);

    // open modal
    modal.open();
</script>
<?php endif;
session_unset();
?>

<?php include "includes/openingBody.inc.php";?>

<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
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
    <p class="page_description">
    You can suggest a new rcon command here.
    Please don't misuse this service and only suggest commands that are useful and not dangerous.
    </p>
  </section>

  <form enctype="multipart/form-data" action="suggest_rcon_command_handle.php" method="POST">
  <section>
    <h3 class="section_title">Command Name</h3>
    <p>
    The name of the suggested command.
    The maximum length is <?php echo($config["maxlengthrconcommand"]); ?> characters.
    The command name may only contain small letters and underscores.
    This field may not be empty.
    </p>
    <input type="text" name="commandname" maxlength="<?php echo($config["maxlengthrconcommand"]); ?>"><br>
    <input type="submit" value="Submit">
  </section>
  </form>
</div>



</body>
</html>
