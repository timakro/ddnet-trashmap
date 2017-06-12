<!DOCTYPE html>
<html lang="en">
<head>
<?php include "includes/head.inc.php";?>
<link rel="stylesheet" href="includes/per/index.css">
<title>DDNet Trashmap</title>
</head>
<body>
<?php include "includes/openingBody.inc.php";?>

<?php
$data = json_decode(file_get_contents("/srv/trashmap/srv/daemon_data.json"), true);
$config = $data["config"];
$servers = $data["storage"]["servers"];
?>

<?php include_once "includes/sprites.svg";?>

<div class="breadcrumbs">
  <div class="crumb">
    Main Page
  </div>
</div>

<div class="main">
  <section class="site_branding">
    <div class="site_branding_box">
      <img class="site_logo" src="includes/ddnet-trashmap.png">
      <p class="site_description">
      DDNet Trashmap is a service for mappers who can't host their own servers.
      You can create a testing server here and test your map alone or with other players.
      </p>
    </div>
  </section>

  <section class="display_case_section">
    <table class="display_case">
      <tr>
        <td>
          <div class="case_head">
            <div class="icon_group icon_group_link">
              <svg role="img" height="48" viewBox="0 0 48 48" width="48" class="icon normal">
                <use xlink:href="#path_serverlist"/>
              </svg>
              <svg role="img" height="48" viewBox="0 0 48 48" width="48" class="icon hovering">
                <use xlink:href="#path_serverlist" class="path_hovering"/>
              </svg>
              <a href="server_list.php"></a>
            </div>
            <h3 class="header_link">
              <a href="server_list.php">Server List</a>
            </h3>
          </div>
          <p class="case_description">View a list of all servers, both saved and currently running.</p>
        </td>

        <?php
        $running = 0;
        foreach($servers as $identifier => $data)
            if($data["running"])
                $running += 1;
        if ($running >= $config["maxrunningservers"]):?>
          <td class="maxed_out">
              <div class="case_head">
                <div class="icon_group">
                  <svg role="img" height="48" viewBox="0 0 48 48" width="48" class="icon normal">
                    <use xlink:href="#path_cross"/>
                  </svg>
                </div>
                <h3 class="header_link">Note</h3>
              </div>
              <p class="case_description">You can't create any more servers because the maximum count of running servers is already reached.</p>
          </td>
        <?php else: ?>
          <td>
            <div class="case_head">
              <div class="icon_group icon_group_link">
                <svg role="img" height="48" viewBox="0 0 48 48" width="48" class="icon normal">
                  <use xlink:href="#path_plus"/>
                </svg>
                <svg role="img" height="48" viewBox="0 0 48 48" width="48" class="icon hovering">
                  <use xlink:href="#path_plus" class="path_hovering"/>
                </svg>
                <a href="create_server.php"></a>
              </div>
              <h3 class="header_link">
                <a href="create_server.php">Create Server</a>
              </h3>
            </div>
            <p class="case_description">Please don't misuse this service or interfere other players who are using this service. Only create a new server if you have no offline server left.</p>
          </td>
        <?php endif; ?>

        <td>
          <div class="case_head">
            <div class="icon_group icon_group_link">
              <svg role="img" height="48" viewBox="0 0 48 48" width="48" class="icon normal">
                <use xlink:href="#path_dev"/>
              </svg>
              <svg role="img" height="48" viewBox="0 0 48 48" width="48" class="icon hovering">
                <use xlink:href="#path_dev" class="path_hovering"/>
              </svg>
              <a href="https://github.com/timakro/DDNet-Trashmap"></a>
            </div>
            <h3 class="header_link">
              <a href="https://github.com/timakro/DDNet-Trashmap">Development</a>
            </h3>
          </div>
          <p class="case_description">DDNet Trashmap is developed by <a href="https://timakro.de">timakro</a> and the web design is made by Oblique.
          You can find the sourcecode on <a href="https://github.com/timakro/DDNet-Trashmap">github</a>.
          Please report bugs on the github page or on the related <a href="https://forum.ddnet.tw/viewtopic.php?f=6&t=1764">thread</a> in the DDNet forum.</p>
        </td>
      </tr>
    </table><!-- display_case -->
  </section>


  <!-- scraps, running servers, creating servers, development -->

  <section class="changelog_section">
    <h3 class="section_title">Changelog</h3>
    <table class="data_table changelog negative">
      <tr>
        <th>Date</th>
        <th>Change</th>
      </tr>
      <tr>
        <td>2017-05-17</td>
        <td>We now got our own logo made by Index</td>
      </tr>
      <tr>
        <td>2017-05-09</td>
        <td>New web design by Oblique</td>
      </tr>
      <tr>
        <td>2017-03-23</td>
        <td>Due to recent exploits we from now on only allow a limited number of saved servers per ip address. With this change all saved servers had to be deleted, sorry for any inconveniences.</td>
      </tr>
      <tr>
        <td>2016-06-06</td>
        <td>No external map configs possible anymore</td>
      </tr>
      <tr>
        <td>2015-12-08</td>
        <td>Forbid suggesting map commands but get them from <a href="https://ddnet.tw/settingscommands/#map-settings">ddnet map settings page</a></td>
      </tr>
      <tr>
        <td>2015-12-08</td>
        <td>Serverside security fixes</td>
      </tr>
      <tr>
        <td>2015-12-08</td>
        <td>Introduce changelog</td>
      </tr>
    </table>
  </section>

</div><!-- main -->
</body>
</html>
