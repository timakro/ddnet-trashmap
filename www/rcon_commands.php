<!DOCTYPE html>
<html lang="en">
<head>
<?php include "includes/head.inc.php";?>
<title>DDNet Trashmap - Rcon Commands</title>
</head>
<body>
<?php include "includes/openingBody.inc.php";?>

<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
?>

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
</div>

<div class="main">
	<section class="page_branding">
		<h2 class="page_title">DDNet Trashmap - Rcon Commands</h2>
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
		<h3>Suggest Command</h3>
		<p>
		There are probably forbidden commands that are not dangerous and would be useful in the rcon console.
		You can suggest new commands <a href="suggest_rcon_command.php">here</a>.
		</p>
	</section>
</div>



</body>
</html>
