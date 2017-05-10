var breadcrumbs = (function (public) {
    public = {};

    public.initialize = function() {
        var breadcrumbsBar = document.querySelector(".breadcrumbs");
        var initialLength = breadcrumbsBar.children.length;
        var crumbs = [];
        for (var i = 1; i < initialLength; i++) {
            crumbs.push(breadcrumbsBar.children[i]);
            // console.log(breadcrumbsBar.children[i]);
        }
        for (var j = 0; j < crumbs.length; j++) {
            var separator = document.createElement("IMG");
            separator.classList.add("separator");
            separator.src = "includes/arrow_o.svg";
            breadcrumbsBar.insertBefore(separator, crumbs[j]);
        }
    };

    return public;
}(breadcrumbs || {}));
