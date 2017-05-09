var iconLinkHover = (function (public) {
	public = {};

	public.initialize = function () {
		var iconLinks = document.querySelectorAll(".icon_group_link");
		for (var i = 0; i < iconLinks.length; i++) {
			iconLink = iconLinks[i].querySelector("A");
			var normalIcon = iconLink.parentNode.querySelector(".normal")
			var hoverIcon = iconLink.parentNode.querySelector(".hovering")
			var headerLink = iconLink.parentNode.parentNode.querySelector(".header_link").querySelector("A")
			iconLink.onmouseover = _returnIconMouseoverFxtn(normalIcon, hoverIcon, headerLink);
			iconLink.onmouseout = _returnIconMouseoutFxtn(normalIcon, hoverIcon, headerLink);
			headerLink.onmouseover = _returnHeaderMouseoverFxtn(normalIcon, hoverIcon);
			headerLink.onmouseout = _returnHeaderMouseoutFxtn(normalIcon, hoverIcon);
		}
	};

	function _returnIconMouseoverFxtn(normalIcon, hoverIcon, headerLink) {
		return function () {
			normalIcon.style.display = "none";
			hoverIcon.style.display = "inline-block";
			headerLink.classList.add("hovering");
		};
	}

	function _returnIconMouseoutFxtn(normalIcon, hoverIcon, headerLink) {
		return function () {
			normalIcon.style.display = "inline-block";
			hoverIcon.style.display = "none";
			headerLink.classList.remove("hovering");
		};
	}

	function _returnHeaderMouseoverFxtn(normalIcon, hoverIcon) {
		return function () {
			normalIcon.style.display = "none";
			hoverIcon.style.display = "inline-block";
		};
	}

	function _returnHeaderMouseoutFxtn(normalIcon, hoverIcon) {
		return function () {
			normalIcon.style.display = "inline-block";
			hoverIcon.style.display = "none";
		};
	}

	return public;
}(iconLinkHover || {}));
