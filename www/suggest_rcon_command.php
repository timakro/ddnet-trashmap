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
