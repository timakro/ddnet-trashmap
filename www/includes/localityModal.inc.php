<div class="locality_modal" style="display:none;">
  <h2 class="trashmap_locations_header">Other Trashmap Locations</h2>
  <ul class="trashmap_locations">
    <?php foreach ($config['locations'] as $location): ?>
      <li><a href="http://<?php echo $location['ip'];?>">DDNet <?php echo $location['location'];?> Trashmap</a></li>
    <?php endforeach; ?>
  </ul>
</div>
