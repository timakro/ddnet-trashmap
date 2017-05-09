<?php
	$includedFiles = get_included_files();
	$includedFrom = $includedFiles[count($includedFiles) - 2];
	$includedFromName = basename($includedFrom, '.php');
?>
<meta charset="utf-8">
<link rel="stylesheet" href="includes/stylesheet.css">
<link rel="stylesheet" href="includes/tingle.css">
<link rel="stylesheet" href="includes/per/<?php echo $includedFromName;?>.css">
<script src="includes/tingle.js"></script>
<script src="includes/innerContainers.js"></script>
<script src="includes/iconLinkHover.js"></script>
<script src="includes/breadcrumbs.js"></script>
<script src="includes/fileUpload.js"></script>
<script src="includes/start.js"></script>