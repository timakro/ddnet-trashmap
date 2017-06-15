<!DOCTYPE html>
<html lang="en">
<?php
$data = json_decode(file_get_contents("/srv/trashmap/srv/daemon_data.json"), true);
$config = $data["config"];
?>
<head>
<?php include "includes/head.inc.php";?>
<title><?php echo $config["name"];?> - Maintenance</title>
</head>
<body>
<?php include "includes/openingBody.inc.php";?>

<div class="breadcrumbs">
  <div class="crumb">
    Maintenance
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
    <h2 class="page_title"><?php echo $config["name"];?> - Maintenance</h2>
    <p class="page_description">Sorry, we are down for maintenance!</p>
  </section>
</div>

</body>
</html>
