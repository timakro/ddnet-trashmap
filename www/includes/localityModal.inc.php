<div class="locality_modal" style="display:none;">
	<h2 class="trashmap_locations_header">Other Trashmap Locations</h2>
	<ul class="trashmap_locations">
		<?php foreach ($config['othertrashmaps'] as $othertrashmaps): ?>
			<li><a href="http://<?php echo $othertrashmaps['ip'];?>"><?php echo $othertrashmaps['name'];?></a></li>
		<?php endforeach; ?>
	</ul>
</div>