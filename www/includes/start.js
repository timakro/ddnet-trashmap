// Wait until content loads
window.onload = function () {
	innerContainers.initialize();
	breadcrumbs.initialize();
	document.body.style.display = "block";
	fileUpload.initialize(0);
	iconLinkHover.initialize();
};
