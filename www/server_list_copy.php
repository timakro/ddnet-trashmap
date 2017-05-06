<!DOCTYPE html>
<html lang="en">
<head>
<?php include "includes/head.inc.php";?>
<title>DDNet Trashmap - Server List</title>
</head>
<body>
<?php include "includes/openingBody.inc.php";?>

<?php
$data = json_decode(file_get_contents("/srv/trashmap/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
?>

<div class="breadcrumbs">
    <div class="crumb">
    	<a href=".">Main Page</a>
	</div>
	<div class="crumb">
		Server List
	</div>
</div>

<div class="main">
	<section class="page_branding">
		<h2 class="page_title">DDNet Trashmap - Server List</h2>
		<p class="page_description">This is a list of all servers saved. You can use the search function of your browser to find a server with a known label. There are currently <?php echo(strval(count($servers))); ?> servers saved. The maximum count of saved servers is <?php echo($config["maxservers"]); ?>.</p>
	</section>

	<section class="running_servers_section">
		<h3 class="section_title">Running Servers</h3>
		<p>
		<?php
		$rows = [];
		foreach($servers as $identifier => $info) {
		    if($info["running"])
		        array_push($rows, "<tr><td>".htmlentities($info["label"])."</td><td>".$info["port"]."</td><td>".htmlentities($info["mapname"])."</td><td>".($info["rcon"] == $config["defaultrcon"] ? "default" : "custom")."</td><td>".($info["password"] == null ? "false" : "true")."</td><td>".count($info["clientids"])." / ".$info["playerlimit"]."</td><td>".$info["runtimestring"]."</td><td><form action=\"access_server.php\" method=\"GET\"><input type=\"hidden\" name=\"id\" value=\"".$identifier."\"><input type=\"text\" name=\"key\" maxlength=\"".$config["maxlengthaccesskey"]."\"><input type=\"submit\" value=\"Access\"></form></td></tr>\n");
		}
		?>
		There are currently <?php echo(strval(count($rows))); ?> servers running.
		The maximum count of running servers is <?php echo($config["maxrunningservers"]); ?>.
		</p>
		<?php if (count($rows) > 0):?>
		<table class="running_servers data_table">
			<tr>
				<th>Label</th>
				<th>Port</th>
				<th>Map</th>
				<th>Rcon</th>
				<th>Password</th>
				<th>Playercount</th>
				<th>Runtime</th>
				<th>Accesskey</th>
			</tr>
			<?php
			foreach($rows as $row) {
			    echo($row);
			}
			?>
		</table>
		<?php endif; ?>
	</section>

	<section class="offline_servers_section">
		<h3 class="section_title">Offline Servers</h3>
		<p>
		<?php
		$rows = [];
		foreach($servers as $identifier => $info) {
		    if(!$info["running"])
		        array_push($rows, "<tr><td>".htmlentities($info["label"])."</td><td>".$info["port"]."</td><td>".htmlentities($info["mapname"])."</td><td>".($info["rcon"] == $config["defaultrcon"] ? "default" : "custom")."</td><td>".($info["password"] == null ? "false" : "true")."</td><td>- / ".$info["playerlimit"]."</td><td><form action=\"access_server.php\" method=\"GET\"><input type=\"hidden\" name=\"id\" value=\"".$identifier."\"><input type=\"text\" name=\"key\" maxlength=\"".$config["maxlengthaccesskey"]."\"><input type=\"submit\" value=\"Access\"></form></td></tr>\n");
		}
		?>
		There are currently <?php echo(strval(count($rows))); ?> offline servers saved.
		</p>
		<table class="offline_servers_table data_table negative">
		<tr><th>Label</th><th>Port</th><th>Map</th><th>Rcon</th><th>Password</th><th>Playercount</th><th>Accesskey</th></tr>
		<tr><td>nimrocks</td><td>8552</td><td>test</td><td>default</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58e90a8f5caa9"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>PX-7</td><td>8516</td><td>PX-7</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5908770e56f83"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>aaa</td><td>8564</td><td>Flow</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f0937e580e1"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Jubjub trashmap</td><td>8525</td><td>test3</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fe60ad22585"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Fluday</td><td>8532</td><td>Hostile_b1fa764f</td><td>default</td><td>true</td><td>- / 2</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590619ae91942"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>BrutalTest121</td><td>8522</td><td>BrutalMappa</td><td>custom</td><td>false</td><td>- / 6</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5902084d821f1"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>NRG</td><td>8553</td><td>Generica</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ea28508176e"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Rave test</td><td>8561</td><td>Rave</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ef1c9e8ced6"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Mensha</td><td>8528</td><td>MonkeyDream_ee57e505</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="59051945995f4"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>krasser</td><td>8573</td><td>getaim</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f27a7de0da0"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Ryuma</td><td>8560</td><td>JustEdgeIt</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ee563a62252"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>LoveBrasil</td><td>8543</td><td>Goo</td><td>default</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f91db67e1f1"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>loel</td><td>8511</td><td>openfng5-Beat_3b63dea6</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ef88f208a46"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>aaa</td><td>8558</td><td>skychase2</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ee159d49191"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Xtatic</td><td>8585</td><td>Test1</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5902103ec597d"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>im corbeums</td><td>8550</td><td>Axe</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58e7ef68ce18a"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Paralix Gay</td><td>8597</td><td>Prova</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ff4be9358a4"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>LeTEErrorist's server </td><td>8546</td><td>Aim 11.0</td><td>custom</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ecc3f312e53"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>zcv</td><td>8567</td><td>1</td><td>custom</td><td>true</td><td>- / 2</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f0ea949057e"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>mrmatesx's</td><td>8566</td><td>Freeze jungle</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f0d16fce8f9"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>coradax's server</td><td>8526</td><td>Caverun</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58daa6f049863"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Fuck You</td><td>8604</td><td>ctf_gal</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590d0e02cd259"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>1233</td><td>8507</td><td>Impossbile 2</td><td>custom</td><td>true</td><td>- / 3</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fd24c99656e"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>E-ron.' &lt;3</td><td>8536</td><td>Midnight 2_9412ebd3</td><td>custom</td><td>true</td><td>- / 10</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5907432fb7d04"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Craby</td><td>8537</td><td>Craby4Samu</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5907538f2b157"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Random</td><td>8505</td><td>Jazz Hands</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fcd810c1d7d"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Welf's test server</td><td>8500</td><td>Gun</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58d40f2402dd9"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Pipou</td><td>8530</td><td>solo_mescouilles</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58dc531f8a016"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>panik</td><td>8571</td><td>panikdesign</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f214114ff6f"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>1233</td><td>8598</td><td>Impossible2_normal</td><td>custom</td><td>true</td><td>- / 3</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5900ed4e3062f"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>test</td><td>8591</td><td>EDviol</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fb53fdb245d"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>timakro</td><td>8603</td><td>test</td><td>default</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590dd324a7254"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Saij Server</td><td>8581</td><td>Goo</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f5c62d66b0e"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Trevor</td><td>8574</td><td>Upsurge_designed_1a4583ad</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f2ad6c80152"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Danceparty</td><td>8600</td><td>Geometrie_1</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="59078e68e2451"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Rovikko's Server</td><td>8569</td><td>1 emned</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f1fcd46704c"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>bugtets</td><td>8605</td><td>bugbob</td><td>custom</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590e006263dd8"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>stompie's Play Palace</td><td>8517</td><td>Moonwalk</td><td>custom</td><td>true</td><td>- / 4</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58d805eeb8615"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Cors trash</td><td>8594</td><td>A real shit</td><td>custom</td><td>true</td><td>- / 4</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5900ae6c73e9b"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>PX-7</td><td>8599</td><td>PX-7</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5907593597c9b"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>DanilBest Server</td><td>8570</td><td>Intothenight</td><td>custom</td><td>true</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f201a595ee3"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>jao secret test</td><td>8539</td><td>I love panik</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590b518ae54dd"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>v2</td><td>8542</td><td>run_neo</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590cc821893d8"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>nimrocks</td><td>8548</td><td>Blmap_Journey</td><td>default</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58e77b3bd6f82"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>spino's test</td><td>8606</td><td>dummy</td><td>default</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590e0c94239d1"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>asdf</td><td>8559</td><td>Aim 11.0</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ee5537614e0"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>triki</td><td>8562</td><td>Tripleblock</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58efca7da55cd"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>as</td><td>8556</td><td>help map</td><td>custom</td><td>true</td><td>- / 2</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58eb995f6ac7c"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>nimrocks</td><td>8551</td><td>Blmap_Journey</td><td>default</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58e8e8f79680a"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>bugtets</td><td>8544</td><td>bano</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590e0059bb185"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Porn +18</td><td>8518</td><td>ThinkFast</td><td>custom</td><td>false</td><td>- / 2</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5900adc25ce0d"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>aaa</td><td>8579</td><td>unicorn edited</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f513a7df7c8"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>nimrocks</td><td>8515</td><td>Sparadrap</td><td>default</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="59008f1de76be"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>test with friends</td><td>8565</td><td>Freeze jungle</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f0cf7cb72d5"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>1123</td><td>8592</td><td>We are back</td><td>custom</td><td>false</td><td>- / 4</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fb9fec230d7"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>iMTG bois</td><td>8593</td><td>test2</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fbb7b0772a9"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>panik</td><td>8541</td><td>I LOVE JAO</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590c9329bea97"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Mc'ztyla server</td><td>8549</td><td>run_tribute_for_NeXus</td><td>custom</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58e7e046ad6c9"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>zxc</td><td>8568</td><td>znnnnnnnnnnnn</td><td>custom</td><td>true</td><td>- / 2</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f100beabdfd"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Skyrel's nuclear server</td><td>8590</td><td>ParalixGay</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fb4ddfcb18b"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Schulzers Testserver</td><td>8572</td><td>Adrenaline 4.2_327fd6e0</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f27a413da22"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Trashmap</td><td>8501</td><td>Dummy Zenith 26</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58d4182a233b8"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>LoveBrasil</td><td>8506</td><td>Goo</td><td>default</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f91db35676f"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>AAAAAAAAAAAAAAAAAAAAAAAAA</td><td>8578</td><td>Undisclosed</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f3e2ed81f5e"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>INK TEST</td><td>8584</td><td>800 horsepower_0bdfb82c_0bdfb82c_0bdfb82c</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f6541b9a413"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>zxcF</td><td>8533</td><td>Generic World_4b9dca9a</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58dd78c4c2e37"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>testserver</td><td>8576</td><td>hh</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f3d6f7ea5af"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Kosha</td><td>8513</td><td>RaZvlekalka</td><td>custom</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58d8e20fd942b"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Skyrel's nuclear server</td><td>8540</td><td>DarkSpy 3</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590b64933f6ab"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>123</td><td>8589</td><td>Impossbile 2</td><td>custom</td><td>true</td><td>- / 2</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58faa19f674af"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>1</td><td>8529</td><td>5</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58dbacfa938c3"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>aaa</td><td>8521</td><td>meewfruhlingg</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58d92798c81da"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>sss</td><td>8601</td><td>puzl(3)</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5908c16a47123"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>wtf test here</td><td>8502</td><td>WunderGreen</td><td>default</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5908018bded57"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>corneum test</td><td>8534</td><td>blur</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="59064c0aebc12"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>corneum test</td><td>8582</td><td>AllKind</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f63af822f6e"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>jao secret test</td><td>8524</td><td>night</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5907addcebc9a"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>fire</td><td>8557</td><td>Rave</td><td>default</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ee0b41b5c52"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>1234</td><td>8602</td><td>Marceline</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5908d61ff0efd"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Essentiel's Server</td><td>8527</td><td>Zombie Apocalyps</td><td>custom</td><td>true</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5905dfdae20b9"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Irishidiot</td><td>8555</td><td>Grind</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58eb83512cfee"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>reflect</td><td>8503</td><td>aaa</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590b1af3a2702"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>server by rovikko</td><td>8575</td><td>1 emned</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f383f5b5784"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>nimrocks</td><td>8531</td><td>BlmapTouchUp</td><td>default</td><td>false</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58dce97df0752"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Aimazing izz da</td><td>8554</td><td>aim</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ea631ed4937"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Skyrel's nuclear server</td><td>8514</td><td>Prova</td><td>custom</td><td>true</td><td>- / 6</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58ff8298c54cc"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Jazz Hands - Jimmy Jazz</td><td>8577</td><td>Jazz Hands</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f3dad63a126"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>123321</td><td>8504</td><td>Impossible2</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="59034c5292901"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>BrutalTest</td><td>8595</td><td>BrutalMappa</td><td>custom</td><td>false</td><td>- / 6</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5900e24c247b9"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Sushis Trashtest.</td><td>8583</td><td>Solomap</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f648bfb9657"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>zzdd5</td><td>8563</td><td>znnnnnnnnnnnn</td><td>custom</td><td>true</td><td>- / 2</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58efb14e653d2"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Silex</td><td>8509</td><td>1Spring2</td><td>custom</td><td>true</td><td>- / 16</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fe1d6f6439e"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>jao test</td><td>8596</td><td>Ancient</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="59066de1907ee"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>rayb</td><td>8545</td><td>asia_jao</td><td>default</td><td>true</td><td>- / 2</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58e69b52095c2"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>MiM</td><td>8508</td><td>Dirt_texture_test</td><td>default</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fd3c9de9749"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Skyrel's nuclear server</td><td>8523</td><td>Prova</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590368ce049de"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>panik</td><td>8586</td><td>panik contest</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f7bd0c2dd58"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Skyrel's nuclear server</td><td>8510</td><td>Prova</td><td>custom</td><td>false</td><td>- / 5</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fe5e3cde315"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Avasus Test Server!</td><td>8520</td><td>Lost</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58d91e03edb56"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Trevor</td><td>8538</td><td>test</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="5907592d1a28a"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>MiM</td><td>8519</td><td>Spring</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58d81d8622b2f"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Pablos_Test</td><td>8580</td><td>Test66</td><td>default</td><td>true</td><td>- / 4</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f52b926c3b0"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Skyrel's nuclear server</td><td>8535</td><td>DarkSpy 3</td><td>custom</td><td>false</td><td>- / 2</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="590661b035b88"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Trevor</td><td>8588</td><td>Snoop</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58f7bddc79cc1"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>LeTEErrorist's server </td><td>8547</td><td>Lockdown</td><td>custom</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58e7ae73d7d81"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Storm</td><td>8512</td><td>NewProjekt</td><td>default</td><td>true</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58d6a02a0cf5c"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		<tr><td>Skyrel's nuclear server</td><td>8587</td><td>Prova</td><td>custom</td><td>false</td><td>- / 8</td><td><form action="access_server.php" method="GET"><input type="hidden" name="id" value="58fa127d2931b"><input type="text" name="key" maxlength="25"><input type="submit" value="Access"></form></td></tr>
		</table>
	</section>
</div>

</body>
</html>