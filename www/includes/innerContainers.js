var innerContainers = (function (public) {
	var public = {};

	public.initialize = function() {
		var mainSections = document.getElementsByClassName("main")[0].querySelectorAll("section");
		for (var i = 0; i < mainSections.length; i++) {
			var innerWrap = document.createElement("DIV");
			innerWrap.classList.add("inner_wrap");
			while (mainSections[i].childNodes.length > 0) {
			    innerWrap.appendChild(mainSections[i].childNodes[0]);
			}
			mainSections[i].innerHTML = "";
			mainSections[i].appendChild(innerWrap);
		}
	}

	return public;
}(innerContainers || {}));
