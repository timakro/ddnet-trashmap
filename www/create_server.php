<!DOCTYPE html>
<html lang="en">
<?php
$data = json_decode(file_get_contents("/srv/trashmap/srv/daemon_data.json"), true);
$config = $data["config"];
?>
<head>
<?php include "includes/head.inc.php";?>
<title><?php echo $config["name"];?> - Create Server</title>
</head>
<body>

<?php
session_start();
if (isset($_SESSION['unsuccessfulservercreation'])):
  $errors = $_SESSION['servercreation_errors'];
  $warnings = $_SESSION['servercreation_warnings'];
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


    var content = "<h2 class=\"modal_title error_head\">There was an error in creating the server.</h2>";
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

<div class="breadcrumbs">
    <div class="crumb">
      <a href=".">Main Page</a>
  </div>
  <div class="crumb">
    Create Server
  </div>
  <div class="locality_tab">
    <h4 class="locality">
      <?php echo $config["locality"]?>
    </h4>
    <img class="dropdown" src="includes/dropdown.svg">
  </div>
</div>

<div class="main">
  <section class="page_branding">
    <h2 class="page_title"><?php echo $config["name"];?> - Create Server</h2>
    <ul class="page_description">
      <li>When you submit this form a new server with the map you uploaded will be started.</li>
      <li>You are only allowed to have at most <?php echo($config["maxserversperip"]); ?> servers saved at a time.</li>
      <li>After it is started, the server will be running for <?php echo($config["joinminutes"]); ?> minutes until it will start to check regularly if there are players on the server.</li>
      <li>If the server is empty it will be stopped automatically.</li>
      <li>You will also get a link to control the server after you create it.</li>
      <li>The maximum testing time until the server will be stopped automatically is <?php echo($config["stophours"]); ?> hours.</li>
      <li>If a server has not been running for <?php echo($config["deletedays"]); ?> days it will be deleted automatically.</li>
    </ul>
  </section>

  <form enctype="multipart/form-data" action="create_server_handle.php" method="POST">
  <section>
    <h3 class="section_title">Label</h3>
    <p>
    The label to display in the servername.
    The maximum length is <?php echo($config["maxlengthlabel"]); ?> characters.
    This field may not be empty.
    This can't be changed after creating the server.
    </p>
    <input type="text" name="label" maxlength="<?php echo($config["maxlengthlabel"]); ?>">
  </section>

  <section>
    <h3 class="section_title">Accesskey</h3>
    <p>
    The key required to control the server via the webinterface.
    The maximum length is <?php echo($config["maxlengthaccesskey"]); ?> characters.
    This field may not be empty.
    This can't be changed after creating the server.
    </p>
    <input type="text" name="accesskey" maxlength="<?php echo($config["maxlengthaccesskey"]); ?>">
  </section>

  <section>
    <h3 class="section_title">Map</h3>
    <p>
    The map file to upload on the server.
    The maximum file size is <?php echo($config["mapsizehuman"]); ?>.
    The filename has to end with '.map'.
    If there are any critical characters in the filename it will be adjusted.
    If the uploaded file is no valid map the server won't start.
    This file is required to start the server.
    </p>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo($config["mapsize"]); ?>">
    <input id="file_upload" type="file" name="map" class="inputfile" />
    <label for="file_upload" class="custom_file_upload">
        <span>Upload Map</span>
    </label>
  </section>

  <section>
    <h3 class="section_title">Password</h3>
    <p>
    The password required to join the server.
    The maximum length is <?php echo($config["maxlengthpassword"]); ?> characters.
    This field is optional.
    </p>
    <input type="text" name="password" maxlength="<?php echo($config["maxlengthpassword"]); ?>">
  </section>

  <section>
    <h3 class="section_title">Rcon</h3>
    <p>
    The rcon password required to access the server console ingame.
    The maximum length is <?php echo($config["maxlengthrcon"]); ?> characters.
    A list of allowed commands in the rcon console can be found <a href="rcon_commands.php">here</a>.
    If this value is empty or too long the default rcon password '<?php echo($config["defaultrcon"]); ?>' will be used.
    </p>
    <input type="text" name="rcon" maxlength="<?php echo($config["maxlengthrcon"]); ?>" value="<?php echo($config["defaultrcon"]); ?>">
  </section>

  <section>
    <h3 class="section_title">Playerlimit</h3>
    <p>
    The maximum number of players on the server.
    The number has to be between <?php echo($config["minplayers"]); ?> and <?php echo($config["maxplayers"]); ?>.
    If this value isn't valid the default value <?php echo($config["defaultplayers"]); ?> will be used.
    </p>
    <input type="number" name="playerlimit" value="<?php echo($config["defaultplayers"]); ?>" min="<?php echo($config["minplayers"]); ?>" max="<?php echo($config["maxplayers"]); ?>"><br>
  </section>

  <section>
    <input type="submit" value="Create Server">
  </section>

  </form>
</div>

</body>
</html>
