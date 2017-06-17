<!DOCTYPE html>
<html lang="en">
<?php
$data = json_decode(file_get_contents("/srv/trashmap/srv/daemon_data.json"), true);
$config = $data["config"];
?>
<head>
<?php include "includes/head.inc.php";?>
<link rel="stylesheet" href="includes/per/rcon_commands.css">
<title>DDNet <?php echo $config["location"];?> Trashmap - Rcon Commands</title>
</head>
<body>
<?php include "includes/openingBody.inc.php";?>

<div class="breadcrumbs">
  <div class="crumb">
    <a href=".">Main Page</a>
  </div>
  <div class="crumb">
    <a href="create_server.php">Create Server</a>
  </div>
  <div class="crumb">
    Rcon Commands
  </div>
  <div class="locality_tab">
    <h4 class="locality">
      <?php echo $config["location"]?>
    </h4>
    <img class="dropdown" src="includes/dropdown.svg">
  </div>
</div>

<div class="main">
  <section class="page_branding">
    <h2 class="page_title">DDNet <?php echo $config["location"];?> Trashmap - Rcon Commands</h2>
    <p class="page_description">When you have the rcon password of a server you can only execute specifically allowed commands.</p>
  </section>
  <section>
    <h3 class="section_title">Allowed Commands</h3>
    <p>This is a list of all allowed commands in the rcon console:</p>
    <ul>
    <?php
    foreach($data["storage"]["allowed_rcon"] as $command)
        echo("<li>".$command."</li>\n");
    ?>
    </ul>

  </section>
  <section class="suggest_command_section">
    <h3 class="section_title">Suggest Command</h3>
    <p>
    There are probably forbidden commands that are not dangerous and would be useful in the rcon console.
    You can suggest new commands by clicking the following button:
    </p>
    <a href="suggest_rcon_command.php" class="button">Suggest Command</a>
  </section>
</div>



</body>
</html>
