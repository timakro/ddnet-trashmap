<!DOCTYPE html>
<html lang="en">
<?php
$data = json_decode(file_get_contents("/srv/trashmap/srv/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
?>
<head>
<?php include "includes/head.inc.php";?>
<link rel="stylesheet" href="includes/per/server_list.css">
<title><?php echo $config["name"];?> - Server List</title>
</head>
<body>
<?php include "includes/openingBody.inc.php";?>

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
    <h2 class="page_title"><?php echo $config["name"];?> - Server List</h2>
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
    <?php if (!empty($rows)):?>
    <table class="running_servers_table data_table">
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
    <?php if (!empty($rows)):?>
    <table class="offline_servers_table data_table negative">
      <tr>
        <th>Label</th>
        <th>Port</th>
        <th>Map</th>
        <th>Rcon</th>
        <th>Password</th>
        <th>Playercount</th>
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
</div>

</body>
</html>
